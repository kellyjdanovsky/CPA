<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;

class DuplicateDetectionLog extends Eloquent
{
    protected $fillable = [
        'table_name',
        'operation_type',
        'operation_uuid',
        'data_fingerprint',
        'user_id',
        'session_id',
        'ip_address',
        'attempted_at',
        'status',
        'reason'
    ];

    protected $casts = [
        'data_fingerprint' => 'array',
        'attempted_at' => 'datetime'
    ];

    /**
     * Get logs for a specific table
     */
    public static function getLogsForTable($tableName, $days = 30)
    {
        return static::where('table_name', $tableName)
            ->where('attempted_at', '>=', Carbon::now()->subDays($days))
            ->orderBy('attempted_at', 'desc')
            ->get();
    }

    /**
     * Get blocked attempts
     */
    public static function getBlockedAttempts($days = 7)
    {
        return static::where('status', 'blocked')
            ->where('attempted_at', '>=', Carbon::now()->subDays($days))
            ->orderBy('attempted_at', 'desc')
            ->get();
    }

    /**
     * Get logs by user
     */
    public static function getLogsByUser($userId, $days = 30)
    {
        return static::where('user_id', $userId)
            ->where('attempted_at', '>=', Carbon::now()->subDays($days))
            ->orderBy('attempted_at', 'desc')
            ->get();
    }

    /**
     * Get summary statistics
     */
    public static function getSummaryStats($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return [
            'total_attempts' => static::where('attempted_at', '>=', $startDate)->count(),
            'blocked_attempts' => static::where('attempted_at', '>=', $startDate)->where('status', 'blocked')->count(),
            'allowed_attempts' => static::where('attempted_at', '>=', $startDate)->where('status', 'allowed')->count(),
            'unique_users' => static::where('attempted_at', '>=', $startDate)->distinct('user_id')->count(),
            'unique_tables' => static::where('attempted_at', '>=', $startDate)->distinct('table_name')->count(),
        ];
    }
}