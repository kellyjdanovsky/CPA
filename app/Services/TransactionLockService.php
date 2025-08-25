<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionLockService
{
    /**
     * Default lock duration in seconds
     */
    const DEFAULT_LOCK_DURATION = 30;

    /**
     * Maximum lock duration in seconds
     */
    const MAX_LOCK_DURATION = 300; // 5 minutes

    /**
     * Acquire a lock for a specific resource
     * 
     * @param string $lockKey
     * @param string $operationType
     * @param int $duration Duration in seconds
     * @param array $lockData Additional data to store with the lock
     * @return bool
     * @throws \Exception
     */
    public function acquireLock($lockKey, $operationType, $duration = self::DEFAULT_LOCK_DURATION, $lockData = [])
    {
        // Validate duration
        $duration = min($duration, self::MAX_LOCK_DURATION);
        $expiresAt = Carbon::now()->addSeconds($duration);
        
        // Clean up expired locks first
        $this->cleanupExpiredLocks();
        
        $user = Auth::user();
        
        try {
            DB::beginTransaction();
            
            // Check if lock already exists and is not expired
            $existingLock = DB::table('transaction_locks')
                ->where('lock_key', $lockKey)
                ->where('expires_at', '>', Carbon::now())
                ->first();
            
            if ($existingLock) {
                DB::rollBack();
                
                // If it's the same user and session, renew the lock
                if ($existingLock->user_id == ($user ? $user->id : null) && 
                    $existingLock->session_id == session()->getId()) {
                    return $this->renewLock($lockKey, $duration);
                }
                
                throw new \Exception("Resource is currently locked by another user. Lock expires at: " . $existingLock->expires_at);
            }
            
            // Create new lock
            DB::table('transaction_locks')->insert([
                'lock_key' => $lockKey,
                'user_id' => $user ? $user->id : null,
                'session_id' => session()->getId(),
                'locked_at' => Carbon::now(),
                'expires_at' => $expiresAt,
                'operation_type' => $operationType,
                'lock_data' => json_encode($lockData),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Release a lock
     * 
     * @param string $lockKey
     * @return bool
     */
    public function releaseLock($lockKey)
    {
        $user = Auth::user();
        
        $deleted = DB::table('transaction_locks')
            ->where('lock_key', $lockKey)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user ? $user->id : null)
                      ->where('session_id', session()->getId());
            })
            ->delete();
        
        return $deleted > 0;
    }

    /**
     * Renew an existing lock
     * 
     * @param string $lockKey
     * @param int $duration
     * @return bool
     */
    public function renewLock($lockKey, $duration = self::DEFAULT_LOCK_DURATION)
    {
        $duration = min($duration, self::MAX_LOCK_DURATION);
        $expiresAt = Carbon::now()->addSeconds($duration);
        $user = Auth::user();
        
        $updated = DB::table('transaction_locks')
            ->where('lock_key', $lockKey)
            ->where('user_id', $user ? $user->id : null)
            ->where('session_id', session()->getId())
            ->update([
                'expires_at' => $expiresAt,
                'updated_at' => Carbon::now()
            ]);
        
        return $updated > 0;
    }

    /**
     * Check if a resource is locked
     * 
     * @param string $lockKey
     * @return bool
     */
    public function isLocked($lockKey)
    {
        $lock = DB::table('transaction_locks')
            ->where('lock_key', $lockKey)
            ->where('expires_at', '>', Carbon::now())
            ->first();
        
        return $lock !== null;
    }

    /**
     * Get lock information
     * 
     * @param string $lockKey
     * @return object|null
     */
    public function getLockInfo($lockKey)
    {
        return DB::table('transaction_locks')
            ->where('lock_key', $lockKey)
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }

    /**
     * Check if current user owns the lock
     * 
     * @param string $lockKey
     * @return bool
     */
    public function ownsLock($lockKey)
    {
        $user = Auth::user();
        
        $lock = DB::table('transaction_locks')
            ->where('lock_key', $lockKey)
            ->where('expires_at', '>', Carbon::now())
            ->where('user_id', $user ? $user->id : null)
            ->where('session_id', session()->getId())
            ->first();
        
        return $lock !== null;
    }

    /**
     * Clean up expired locks
     */
    public function cleanupExpiredLocks()
    {
        DB::table('transaction_locks')
            ->where('expires_at', '<=', Carbon::now())
            ->delete();
    }

    /**
     * Get all active locks for debugging
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getActiveLocks()
    {
        return DB::table('transaction_locks')
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('locked_at', 'desc')
            ->get();
    }

    /**
     * Force release all locks for a user (admin function)
     * 
     * @param int $userId
     * @return int Number of locks released
     */
    public function forceReleaseUserLocks($userId)
    {
        return DB::table('transaction_locks')
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * Generate a lock key for student operations
     * 
     * @param int $studentId
     * @param string $operation
     * @return string
     */
    public static function generateStudentLockKey($studentId, $operation = 'general')
    {
        return "student_{$studentId}_{$operation}";
    }

    /**
     * Generate a lock key for payment operations
     * 
     * @param int $studentId
     * @param int $paymentId
     * @param string $year
     * @return string
     */
    public static function generatePaymentLockKey($studentId, $paymentId, $year)
    {
        return "payment_{$studentId}_{$paymentId}_{$year}";
    }

    /**
     * Generate a lock key for exam/mark operations
     * 
     * @param int $studentId
     * @param int $examId
     * @param string $year
     * @return string
     */
    public static function generateExamLockKey($studentId, $examId, $year)
    {
        return "exam_{$studentId}_{$examId}_{$year}";
    }

    /**
     * Generate a lock key for class operations
     * 
     * @param int $classId
     * @param string $operation
     * @return string
     */
    public static function generateClassLockKey($classId, $operation = 'general')
    {
        return "class_{$classId}_{$operation}";
    }

    /**
     * Execute a callback with a lock
     * 
     * @param string $lockKey
     * @param string $operationType
     * @param callable $callback
     * @param int $duration
     * @param array $lockData
     * @return mixed
     * @throws \Exception
     */
    public function withLock($lockKey, $operationType, callable $callback, $duration = self::DEFAULT_LOCK_DURATION, $lockData = [])
    {
        if (!$this->acquireLock($lockKey, $operationType, $duration, $lockData)) {
            throw new \Exception("Could not acquire lock for: {$lockKey}");
        }

        try {
            return $callback();
        } finally {
            $this->releaseLock($lockKey);
        }
    }

    /**
     * Get lock statistics
     * 
     * @return array
     */
    public function getLockStatistics()
    {
        $stats = DB::table('transaction_locks')
            ->select(
                DB::raw('COUNT(*) as total_active_locks'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users'),
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, locked_at, expires_at)) as avg_duration'),
                DB::raw('MAX(expires_at) as latest_expiry')
            )
            ->where('expires_at', '>', Carbon::now())
            ->first();

        $operationStats = DB::table('transaction_locks')
            ->select('operation_type', DB::raw('COUNT(*) as count'))
            ->where('expires_at', '>', Carbon::now())
            ->groupBy('operation_type')
            ->get();

        return [
            'summary' => $stats,
            'by_operation' => $operationStats
        ];
    }
}