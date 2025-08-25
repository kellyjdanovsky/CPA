<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;

class TransactionLock extends Eloquent
{
    protected $fillable = [
        'lock_key',
        'user_id',
        'session_id',
        'locked_at',
        'expires_at',
        'operation_type',
        'lock_data'
    ];

    protected $casts = [
        'lock_data' => 'array',
        'locked_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    /**
     * Check if lock is expired
     */
    public function isExpired()
    {
        return $this->expires_at <= Carbon::now();
    }

    /**
     * Get active locks
     */
    public static function getActiveLocks()
    {
        return static::where('expires_at', '>', Carbon::now())
            ->orderBy('locked_at', 'desc')
            ->get();
    }

    /**
     * Get expired locks
     */
    public static function getExpiredLocks()
    {
        return static::where('expires_at', '<=', Carbon::now())
            ->get();
    }

    /**
     * Clean up expired locks
     */
    public static function cleanupExpired()
    {
        return static::where('expires_at', '<=', Carbon::now())->delete();
    }

    /**
     * Get locks by user
     */
    public static function getLocksByUser($userId)
    {
        return static::where('user_id', $userId)
            ->where('expires_at', '>', Carbon::now())
            ->get();
    }

    /**
     * Get lock statistics
     */
    public static function getLockStats()
    {
        $now = Carbon::now();
        
        return [
            'total_active' => static::where('expires_at', '>', $now)->count(),
            'total_expired' => static::where('expires_at', '<=', $now)->count(),
            'unique_users' => static::where('expires_at', '>', $now)->distinct('user_id')->count(),
            'operation_types' => static::where('expires_at', '>', $now)
                ->groupBy('operation_type')
                ->selectRaw('operation_type, COUNT(*) as count')
                ->pluck('count', 'operation_type')
                ->toArray()
        ];
    }
}