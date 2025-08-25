<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Services\TransactionLockService;
use App\Services\DuplicateLoggerService;

class PreventDuplicateRequests
{
    /**
     * @var TransactionLockService
     */
    protected $lockService;

    /**
     * @var DuplicateLoggerService
     */
    protected $loggerService;

    /**
     * URLs that should be protected against duplicates
     */
    protected $protectedRoutes = [
        'students.store',
        'students.update',
        'payments.store',
        'receipts.store',
        'marks.store',
        'marks.update',
        'exam_records.store',
        'exam_records.update'
    ];

    /**
     * Methods that should be protected
     */
    protected $protectedMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function __construct()
    {
        $this->lockService = new TransactionLockService();
        $this->loggerService = new DuplicateLoggerService();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only protect certain HTTP methods
        if (!in_array($request->method(), $this->protectedMethods)) {
            return $next($request);
        }

        // Only protect certain routes
        $routeName = $request->route()->getName();
        if (!$this->shouldProtectRoute($routeName, $request)) {
            return $next($request);
        }

        try {
            // Generate request signature
            $requestSignature = $this->generateRequestSignature($request);
            
            // Check for duplicate request using cache
            if ($this->isDuplicateRequest($requestSignature)) {
                $this->logDuplicateAttempt($request, 'duplicate_request_detected');
                
                return response()->json([
                    'error' => 'Duplicate request detected',
                    'message' => 'This request has already been processed. Please wait before trying again.',
                    'code' => 'DUPLICATE_REQUEST'
                ], 409); // 409 Conflict
            }

            // Try to acquire transaction lock for critical operations
            $lockKey = $this->generateLockKey($request);
            if ($lockKey && !$this->acquireTransactionLock($lockKey, $request)) {
                $this->logDuplicateAttempt($request, 'transaction_lock_failed');
                
                return response()->json([
                    'error' => 'Resource is currently being processed',
                    'message' => 'Another operation is currently in progress. Please wait and try again.',
                    'code' => 'RESOURCE_LOCKED'
                ], 423); // 423 Locked
            }

            // Mark request as in progress
            $this->markRequestInProgress($requestSignature);

            // Process the request
            $response = $next($request);

            // Log successful operation
            $this->logDuplicateAttempt($request, 'request_allowed');

            // Clean up
            $this->cleanupRequest($requestSignature, $lockKey);

            return $response;

        } catch (\Exception $e) {
            // Clean up on error
            $this->cleanupRequest($requestSignature ?? null, $lockKey ?? null);
            
            // Log the error
            $this->logDuplicateAttempt($request, 'request_error', $e->getMessage());
            
            throw $e;
        }
    }

    /**
     * Check if the route should be protected
     */
    protected function shouldProtectRoute($routeName, Request $request)
    {
        // Check specific route names
        if (in_array($routeName, $this->protectedRoutes)) {
            return true;
        }

        // Check route patterns
        $protectedPatterns = [
            '/^students\./',
            '/^payments\./',
            '/^receipts\./',
            '/^marks\./',
            '/^exam_records\./'
        ];

        foreach ($protectedPatterns as $pattern) {
            if (preg_match($pattern, $routeName)) {
                return true;
            }
        }

        // Check for specific form submissions
        if ($request->has('_token') && in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            return true;
        }

        return false;
    }

    /**
     * Generate a unique signature for the request
     */
    protected function generateRequestSignature(Request $request)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : 'anonymous';
        
        // Include critical request data
        $data = [
            'user_id' => $userId,
            'method' => $request->method(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'data_hash' => hash('sha256', json_encode($request->except(['_token', '_method'])))
        ];

        return hash('sha256', json_encode($data));
    }

    /**
     * Check if this is a duplicate request
     */
    protected function isDuplicateRequest($signature)
    {
        $cacheKey = "request_signature:{$signature}";
        return Cache::has($cacheKey);
    }

    /**
     * Mark request as in progress
     */
    protected function markRequestInProgress($signature)
    {
        $cacheKey = "request_signature:{$signature}";
        // Store for 30 seconds to prevent immediate duplicates
        Cache::put($cacheKey, time(), 30);
    }

    /**
     * Generate lock key for transaction locking
     */
    protected function generateLockKey(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        $routeName = $request->route()->getName();
        
        // Generate lock key based on operation type
        switch (true) {
            case strpos($routeName, 'students') !== false:
                $studentId = $request->route('student') ?? $request->input('user_id');
                return $studentId ? TransactionLockService::generateStudentLockKey($studentId, 'update') : null;
                
            case strpos($routeName, 'payments') !== false:
            case strpos($routeName, 'receipts') !== false:
                $studentId = $request->input('student_id');
                $paymentId = $request->input('payment_id');
                $year = $request->input('year') ?? date('Y');
                return ($studentId && $paymentId) ? 
                    TransactionLockService::generatePaymentLockKey($studentId, $paymentId, $year) : null;
                
            case strpos($routeName, 'marks') !== false:
            case strpos($routeName, 'exam_records') !== false:
                $studentId = $request->input('student_id');
                $examId = $request->input('exam_id');
                $year = $request->input('year') ?? date('Y');
                return ($studentId && $examId) ? 
                    TransactionLockService::generateExamLockKey($studentId, $examId, $year) : null;
                
            default:
                return "general_operation_{$user->id}_" . hash('crc32', $request->path());
        }
    }

    /**
     * Acquire transaction lock
     */
    protected function acquireTransactionLock($lockKey, Request $request)
    {
        try {
            $operationType = $this->getOperationType($request);
            return $this->lockService->acquireLock($lockKey, $operationType, 30, [
                'route' => $request->route()->getName(),
                'method' => $request->method(),
                'path' => $request->path()
            ]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get operation type for logging
     */
    protected function getOperationType(Request $request)
    {
        $method = strtolower($request->method());
        $routeName = $request->route()->getName();
        
        if (strpos($routeName, 'store') !== false) {
            return 'create';
        } elseif (strpos($routeName, 'update') !== false) {
            return 'update';
        } elseif (strpos($routeName, 'destroy') !== false) {
            return 'delete';
        }
        
        return $method;
    }

    /**
     * Log duplicate attempt
     */
    protected function logDuplicateAttempt(Request $request, $status, $reason = null)
    {
        try {
            $routeName = $request->route()->getName();
            $operationType = $this->getOperationType($request);
            
            // Determine table name from route
            $tableName = 'unknown';
            if (strpos($routeName, 'students') !== false) {
                $tableName = 'student_records';
            } elseif (strpos($routeName, 'payments') !== false) {
                $tableName = 'payment_records';
            } elseif (strpos($routeName, 'receipts') !== false) {
                $tableName = 'receipts';
            } elseif (strpos($routeName, 'marks') !== false) {
                $tableName = 'marks';
            } elseif (strpos($routeName, 'exam_records') !== false) {
                $tableName = 'exam_records';
            }

            $this->loggerService->logDuplicateAttempt(
                $tableName,
                $operationType,
                $status,
                $request->except(['_token', 'password', 'password_confirmation']),
                $reason
            );
        } catch (\Exception $e) {
            // Don't fail the request if logging fails
            \Log::error('Failed to log duplicate attempt: ' . $e->getMessage());
        }
    }

    /**
     * Clean up after request processing
     */
    protected function cleanupRequest($signature, $lockKey)
    {
        try {
            // Remove request signature from cache
            if ($signature) {
                $cacheKey = "request_signature:{$signature}";
                Cache::forget($cacheKey);
            }

            // Release transaction lock
            if ($lockKey) {
                $this->lockService->releaseLock($lockKey);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to cleanup request: ' . $e->getMessage());
        }
    }

    /**
     * Handle request completion
     */
    public function terminate($request, $response)
    {
        // Clean up any remaining locks for this session
        try {
            $user = Auth::user();
            if ($user && $response->getStatusCode() >= 400) {
                // If request failed, ensure locks are released
                $activeLocks = $this->lockService->getActiveLocks();
                foreach ($activeLocks as $lock) {
                    if ($lock->user_id == $user->id && $lock->session_id == session()->getId()) {
                        $this->lockService->releaseLock($lock->lock_key);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to cleanup locks in terminate: ' . $e->getMessage());
        }
    }
}