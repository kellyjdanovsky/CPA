<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

class DuplicateLoggerService
{
    /**
     * Log a duplicate detection event
     * 
     * @param string $tableName
     * @param string $operationType
     * @param string $status
     * @param array $data
     * @param string|null $reason
     * @param string|null $operationUuid
     * @return int Log ID
     */
    public function logDuplicateAttempt(
        $tableName, 
        $operationType, 
        $status, 
        $data = [], 
        $reason = null, 
        $operationUuid = null
    ) {
        $user = Auth::user();
        
        $logData = [
            'table_name' => $tableName,
            'operation_type' => $operationType,
            'operation_uuid' => $operationUuid,
            'data_fingerprint' => json_encode([
                'fingerprint' => $this->generateFingerprint($data),
                'data_sample' => $this->sanitizeDataForLogging($data),
                'timestamp' => now()->toISOString()
            ]),
            'user_id' => $user ? $user->id : null,
            'session_id' => session()->getId(),
            'ip_address' => Request::ip(),
            'attempted_at' => Carbon::now(),
            'status' => $status,
            'reason' => $reason,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        return DB::table('duplicate_detection_logs')->insertGetId($logData);
    }

    /**
     * Generate a fingerprint for the data
     * 
     * @param array $data
     * @return string
     */
    protected function generateFingerprint($data)
    {
        // Remove timestamps and non-critical fields for fingerprint generation
        $criticalData = array_filter($data, function($key) {
            return !in_array($key, ['created_at', 'updated_at', 'id', 'operation_uuid']);
        }, ARRAY_FILTER_USE_KEY);
        
        ksort($criticalData);
        return hash('sha256', json_encode($criticalData));
    }

    /**
     * Sanitize data for logging (remove sensitive information)
     * 
     * @param array $data
     * @return array
     */
    protected function sanitizeDataForLogging($data)
    {
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'secret'];
        
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveFields)) {
                $sanitized[$key] = '[REDACTED]';
            } else {
                $sanitized[$key] = is_string($value) && strlen($value) > 100 ? 
                    substr($value, 0, 100) . '...' : $value;
            }
        }
        
        return $sanitized;
    }

    /**
     * Get duplicate attempts for a specific table
     * 
     * @param string $tableName
     * @param int $days
     * @return \Illuminate\Support\Collection
     */
    public function getDuplicateAttempts($tableName, $days = 7)
    {
        return DB::table('duplicate_detection_logs')
            ->where('table_name', $tableName)
            ->where('attempted_at', '>=', Carbon::now()->subDays($days))
            ->orderBy('attempted_at', 'desc')
            ->get();
    }

    /**
     * Get duplicate attempts by user
     * 
     * @param int $userId
     * @param int $days
     * @return \Illuminate\Support\Collection
     */
    public function getUserDuplicateAttempts($userId, $days = 7)
    {
        return DB::table('duplicate_detection_logs')
            ->where('user_id', $userId)
            ->where('attempted_at', '>=', Carbon::now()->subDays($days))
            ->orderBy('attempted_at', 'desc')
            ->get();
    }

    /**
     * Get duplicate statistics
     * 
     * @param int $days
     * @return array
     */
    public function getDuplicateStatistics($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        // Overall statistics
        $overall = DB::table('duplicate_detection_logs')
            ->where('attempted_at', '>=', $startDate)
            ->select(
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('SUM(CASE WHEN status = "blocked" THEN 1 ELSE 0 END) as blocked_attempts'),
                DB::raw('SUM(CASE WHEN status = "allowed" THEN 1 ELSE 0 END) as allowed_attempts'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users'),
                DB::raw('COUNT(DISTINCT table_name) as affected_tables')
            )
            ->first();

        // By table
        $byTable = DB::table('duplicate_detection_logs')
            ->where('attempted_at', '>=', $startDate)
            ->select('table_name', 'status', DB::raw('COUNT(*) as count'))
            ->groupBy('table_name', 'status')
            ->get()
            ->groupBy('table_name');

        // By operation type
        $byOperation = DB::table('duplicate_detection_logs')
            ->where('attempted_at', '>=', $startDate)
            ->select('operation_type', 'status', DB::raw('COUNT(*) as count'))
            ->groupBy('operation_type', 'status')
            ->get()
            ->groupBy('operation_type');

        // Top users with blocked attempts
        $topBlockedUsers = DB::table('duplicate_detection_logs')
            ->where('attempted_at', '>=', $startDate)
            ->where('status', 'blocked')
            ->select('user_id', DB::raw('COUNT(*) as blocked_count'))
            ->groupBy('user_id')
            ->orderBy('blocked_count', 'desc')
            ->limit(10)
            ->get();

        // Hourly distribution
        $hourlyDistribution = DB::table('duplicate_detection_logs')
            ->where('attempted_at', '>=', Carbon::now()->subDays(1))
            ->select(
                DB::raw('HOUR(attempted_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return [
            'overall' => $overall,
            'by_table' => $byTable,
            'by_operation' => $byOperation,
            'top_blocked_users' => $topBlockedUsers,
            'hourly_distribution' => $hourlyDistribution
        ];
    }

    /**
     * Detect potential duplicate patterns
     * 
     * @param int $days
     * @return array
     */
    public function detectDuplicatePatterns($days = 7)
    {
        $startDate = Carbon::now()->subDays($days);
        
        // Find users with frequent duplicate attempts
        $frequentDuplicators = DB::table('duplicate_detection_logs')
            ->where('attempted_at', '>=', $startDate)
            ->where('status', 'blocked')
            ->select('user_id', 'table_name', DB::raw('COUNT(*) as attempt_count'))
            ->groupBy('user_id', 'table_name')
            ->having('attempt_count', '>', 5)
            ->orderBy('attempt_count', 'desc')
            ->get();

        // Find patterns by fingerprint (same data being submitted multiple times)
        $fingerprintPatterns = DB::table('duplicate_detection_logs')
            ->where('attempted_at', '>=', $startDate)
            ->select(
                DB::raw('JSON_EXTRACT(data_fingerprint, "$.fingerprint") as fingerprint'),
                'table_name',
                DB::raw('COUNT(*) as occurrence_count'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users')
            )
            ->groupBy('fingerprint', 'table_name')
            ->having('occurrence_count', '>', 3)
            ->orderBy('occurrence_count', 'desc')
            ->get();

        // Find rapid succession attempts (potential system issues)
        $rapidAttempts = DB::table('duplicate_detection_logs as d1')
            ->join('duplicate_detection_logs as d2', function($join) {
                $join->on('d1.user_id', '=', 'd2.user_id')
                     ->on('d1.table_name', '=', 'd2.table_name')
                     ->whereRaw('d2.attempted_at BETWEEN d1.attempted_at AND DATE_ADD(d1.attempted_at, INTERVAL 5 SECOND)')
                     ->where('d1.id', '!=', 'd2.id');
            })
            ->where('d1.attempted_at', '>=', $startDate)
            ->select(
                'd1.user_id',
                'd1.table_name',
                DB::raw('COUNT(*) as rapid_attempts')
            )
            ->groupBy('d1.user_id', 'd1.table_name')
            ->having('rapid_attempts', '>', 2)
            ->get();

        return [
            'frequent_duplicators' => $frequentDuplicators,
            'fingerprint_patterns' => $fingerprintPatterns,
            'rapid_attempts' => $rapidAttempts
        ];
    }

    /**
     * Generate duplicate prevention report
     * 
     * @param int $days
     * @return array
     */
    public function generateDuplicateReport($days = 30)
    {
        $statistics = $this->getDuplicateStatistics($days);
        $patterns = $this->detectDuplicatePatterns($days);
        
        $effectiveness = 0;
        if ($statistics['overall']->total_attempts > 0) {
            $effectiveness = ($statistics['overall']->blocked_attempts / $statistics['overall']->total_attempts) * 100;
        }

        return [
            'period' => $days,
            'generated_at' => Carbon::now(),
            'effectiveness_percentage' => round($effectiveness, 2),
            'statistics' => $statistics,
            'patterns' => $patterns,
            'recommendations' => $this->generateRecommendations($statistics, $patterns)
        ];
    }

    /**
     * Generate recommendations based on duplicate patterns
     * 
     * @param array $statistics
     * @param array $patterns
     * @return array
     */
    protected function generateRecommendations($statistics, $patterns)
    {
        $recommendations = [];

        // High duplicate rate recommendation
        if ($statistics['overall']->total_attempts > 0) {
            $duplicateRate = ($statistics['overall']->blocked_attempts / $statistics['overall']->total_attempts) * 100;
            
            if ($duplicateRate > 20) {
                $recommendations[] = [
                    'type' => 'high_duplicate_rate',
                    'priority' => 'high',
                    'message' => "High duplicate rate detected ({$duplicateRate}%). Consider reviewing user workflows and adding better validation messages.",
                    'action' => 'Review user interface and add clearer feedback'
                ];
            }
        }

        // Frequent duplicators recommendation
        if (count($patterns['frequent_duplicators']) > 0) {
            $recommendations[] = [
                'type' => 'frequent_duplicators',
                'priority' => 'medium',
                'message' => count($patterns['frequent_duplicators']) . " users are frequently triggering duplicates. Consider user training or system improvements.",
                'action' => 'Review user behavior and provide additional training'
            ];
        }

        // Rapid attempts recommendation
        if (count($patterns['rapid_attempts']) > 0) {
            $recommendations[] = [
                'type' => 'rapid_attempts',
                'priority' => 'high',
                'message' => "Rapid successive attempts detected. This may indicate double-click issues or system performance problems.",
                'action' => 'Implement frontend button disabling and review system performance'
            ];
        }

        return $recommendations;
    }

    /**
     * Clean up old duplicate logs
     * 
     * @param int $daysToKeep
     * @return int Number of records deleted
     */
    public function cleanupOldLogs($daysToKeep = 90)
    {
        $cutoffDate = Carbon::now()->subDays($daysToKeep);
        
        return DB::table('duplicate_detection_logs')
            ->where('attempted_at', '<', $cutoffDate)
            ->delete();
    }

    /**
     * Export duplicate logs to CSV
     * 
     * @param string $tableName
     * @param int $days
     * @return string CSV content
     */
    public function exportDuplicateLogsToCSV($tableName = null, $days = 30)
    {
        $query = DB::table('duplicate_detection_logs')
            ->where('attempted_at', '>=', Carbon::now()->subDays($days))
            ->orderBy('attempted_at', 'desc');
        
        if ($tableName) {
            $query->where('table_name', $tableName);
        }
        
        $logs = $query->get();
        
        $csv = "ID,Table,Operation,Status,User ID,IP Address,Attempted At,Reason\n";
        
        foreach ($logs as $log) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s,%s\n",
                $log->id,
                $log->table_name,
                $log->operation_type,
                $log->status,
                $log->user_id ?? 'N/A',
                $log->ip_address,
                $log->attempted_at,
                str_replace(['"', "\n", "\r"], ['""', ' ', ' '], $log->reason ?? '')
            );
        }
        
        return $csv;
    }

    /**
     * Get recent critical duplicate events
     * 
     * @param int $hours
     * @return \Illuminate\Support\Collection
     */
    public function getRecentCriticalEvents($hours = 24)
    {
        return DB::table('duplicate_detection_logs')
            ->where('attempted_at', '>=', Carbon::now()->subHours($hours))
            ->where('status', 'blocked')
            ->whereIn('table_name', ['payment_records', 'receipts', 'student_records', 'marks'])
            ->orderBy('attempted_at', 'desc')
            ->get();
    }
}