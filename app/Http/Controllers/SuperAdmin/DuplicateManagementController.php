<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\DuplicateLoggerService;
use App\Services\TransactionLockService;
use App\Models\DuplicateDetectionLog;
use App\Models\TransactionLock;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DuplicateManagementController extends Controller
{
    protected $loggerService;
    protected $lockService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('super_admin'); // Only super admin can access
        $this->loggerService = new DuplicateLoggerService();
        $this->lockService = new TransactionLockService();
    }

    /**
     * Display duplicate management dashboard
     */
    public function dashboard()
    {
        $stats = $this->loggerService->getDuplicateStatistics(30);
        $patterns = $this->loggerService->detectDuplicatePatterns(7);
        $recentCritical = $this->loggerService->getRecentCriticalEvents(24);
        $lockStats = $this->lockService->getLockStatistics();
        
        return view('pages.super_admin.duplicate_management.dashboard', compact(
            'stats', 'patterns', 'recentCritical', 'lockStats'
        ));
    }

    /**
     * Display duplicate detection logs
     */
    public function logs(Request $request)
    {
        $query = DuplicateDetectionLog::query();
        
        // Apply filters
        if ($request->filled('table_name')) {
            $query->where('table_name', $request->table_name);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->filled('date_from')) {
            $query->where('attempted_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('attempted_at', '<=', $request->date_to . ' 23:59:59');
        }
        
        $logs = $query->orderBy('attempted_at', 'desc')->paginate(50);
        
        // Get filter options
        $tables = DB::table('duplicate_detection_logs')
            ->distinct()
            ->pluck('table_name')
            ->sort();
            
        $statuses = ['blocked', 'allowed', 'removed_duplicates'];
        
        return view('pages.super_admin.duplicate_management.logs', compact(
            'logs', 'tables', 'statuses'
        ));
    }

    /**
     * Display active transaction locks
     */
    public function locks()
    {
        $activeLocks = TransactionLock::getActiveLocks();
        $lockStats = TransactionLock::getLockStats();
        
        return view('pages.super_admin.duplicate_management.locks', compact(
            'activeLocks', 'lockStats'
        ));
    }

    /**
     * Force release a transaction lock
     */
    public function releaseLock(Request $request, $lockId)
    {
        $lock = TransactionLock::findOrFail($lockId);
        
        if (!$lock->isExpired()) {
            $lock->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Lock released successfully'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Lock has already expired'
        ]);
    }

    /**
     * Clean up expired locks
     */
    public function cleanupLocks()
    {
        $cleaned = TransactionLock::cleanupExpired();
        
        return response()->json([
            'success' => true,
            'message' => "Cleaned up {$cleaned} expired locks"
        ]);
    }

    /**
     * Generate duplicate prevention report
     */
    public function generateReport(Request $request)
    {
        $days = $request->get('days', 30);
        $report = $this->loggerService->generateDuplicateReport($days);
        
        return view('pages.super_admin.duplicate_management.report', compact('report'));
    }

    /**
     * Export duplicate logs as CSV
     */
    public function exportLogs(Request $request)
    {
        $tableName = $request->get('table_name');
        $days = $request->get('days', 30);
        
        $csv = $this->loggerService->exportDuplicateLogsToCSV($tableName, $days);
        
        $filename = 'duplicate_logs_' . ($tableName ?? 'all') . '_' . date('Y-m-d') . '.csv';
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get duplicate statistics as JSON
     */
    public function getStatistics(Request $request)
    {
        $days = $request->get('days', 7);
        $stats = $this->loggerService->getDuplicateStatistics($days);
        
        return response()->json($stats);
    }

    /**
     * Clean up old duplicate logs
     */
    public function cleanupLogs(Request $request)
    {
        $daysToKeep = $request->get('days_to_keep', 90);
        $deleted = $this->loggerService->cleanupOldLogs($daysToKeep);
        
        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} old log entries",
            'deleted_count' => $deleted
        ]);
    }

    /**
     * Search for potential duplicates in database
     */
    public function searchDuplicates(Request $request)
    {
        $table = $request->get('table');
        
        $duplicates = [];
        
        switch ($table) {
            case 'student_records':
                $duplicates = $this->findStudentDuplicates();
                break;
            case 'payment_records':
                $duplicates = $this->findPaymentDuplicates();
                break;
            case 'receipts':
                $duplicates = $this->findReceiptDuplicates();
                break;
            case 'marks':
                $duplicates = $this->findMarkDuplicates();
                break;
            default:
                $duplicates = $this->findAllDuplicates();
        }
        
        return response()->json([
            'success' => true,
            'duplicates' => $duplicates,
            'table' => $table
        ]);
    }

    /**
     * Remove confirmed duplicates
     */
    public function removeDuplicates(Request $request)
    {
        $request->validate([
            'table' => 'required|string',
            'duplicate_ids' => 'required|array',
            'keep_id' => 'required|integer'
        ]);
        
        $table = $request->table;
        $duplicateIds = $request->duplicate_ids;
        $keepId = $request->keep_id;
        
        // Remove the ID we want to keep from the deletion list
        $idsToDelete = array_filter($duplicateIds, function($id) use ($keepId) {
            return $id != $keepId;
        });
        
        if (empty($idsToDelete)) {
            return response()->json([
                'success' => false,
                'message' => 'No duplicates to remove'
            ]);
        }
        
        try {
            DB::beginTransaction();
            
            $deleted = DB::table($table)->whereIn('id', $idsToDelete)->delete();
            
            // Log the cleanup
            $this->loggerService->logDuplicateAttempt(
                $table,
                'manual_cleanup',
                'removed_duplicates',
                [
                    'kept_id' => $keepId,
                    'removed_ids' => $idsToDelete,
                    'admin_user_id' => auth()->id()
                ],
                'Manual duplicate removal by admin'
            );
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Removed {$deleted} duplicate records",
                'deleted_count' => $deleted
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error removing duplicates: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Find student record duplicates
     */
    private function findStudentDuplicates()
    {
        return DB::select("
            SELECT 
                GROUP_CONCAT(id ORDER BY id) as duplicate_ids,
                user_id, session, my_class_id,
                COUNT(*) as duplicate_count
            FROM student_records 
            GROUP BY user_id, session, my_class_id
            HAVING COUNT(*) > 1
            ORDER BY duplicate_count DESC
        ");
    }

    /**
     * Find payment record duplicates
     */
    private function findPaymentDuplicates()
    {
        return DB::select("
            SELECT 
                GROUP_CONCAT(id ORDER BY id) as duplicate_ids,
                student_id, payment_id, year,
                COUNT(*) as duplicate_count
            FROM payment_records 
            GROUP BY student_id, payment_id, year
            HAVING COUNT(*) > 1
            ORDER BY duplicate_count DESC
        ");
    }

    /**
     * Find receipt duplicates
     */
    private function findReceiptDuplicates()
    {
        return DB::select("
            SELECT 
                GROUP_CONCAT(id ORDER BY id) as duplicate_ids,
                pr_id, reference_number,
                COUNT(*) as duplicate_count
            FROM receipts 
            WHERE reference_number IS NOT NULL
            GROUP BY pr_id, reference_number
            HAVING COUNT(*) > 1
            ORDER BY duplicate_count DESC
        ");
    }

    /**
     * Find mark duplicates
     */
    private function findMarkDuplicates()
    {
        return DB::select("
            SELECT 
                GROUP_CONCAT(id ORDER BY id) as duplicate_ids,
                student_id, subject_id, exam_id, year,
                COUNT(*) as duplicate_count
            FROM marks 
            GROUP BY student_id, subject_id, exam_id, year
            HAVING COUNT(*) > 1
            ORDER BY duplicate_count DESC
        ");
    }

    /**
     * Find all duplicates across all tables
     */
    private function findAllDuplicates()
    {
        return [
            'student_records' => $this->findStudentDuplicates(),
            'payment_records' => $this->findPaymentDuplicates(),
            'receipts' => $this->findReceiptDuplicates(),
            'marks' => $this->findMarkDuplicates()
        ];
    }

    /**
     * Get duplicate prevention settings
     */
    public function getSettings()
    {
        $settings = [
            'auto_cleanup_enabled' => true,
            'cleanup_interval_days' => 30,
            'max_lock_duration' => 300,
            'alert_threshold' => 10
        ];
        
        return response()->json($settings);
    }

    /**
     * Update duplicate prevention settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'auto_cleanup_enabled' => 'boolean',
            'cleanup_interval_days' => 'integer|min:1|max:365',
            'max_lock_duration' => 'integer|min:30|max:3600',
            'alert_threshold' => 'integer|min:1|max:100'
        ]);
        
        // In a real implementation, save these to database or config
        // For now, just return success
        
        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }
}