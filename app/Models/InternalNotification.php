<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class InternalNotification extends Model
{
    protected $table = 'internal_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'link',
        'icon',
        'color',
        'is_read',
        'priority',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Relationship to the User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include notifications for a specific user.
     */
    public function scopeForUser($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    /**
     * Scope a query to order by most recent.
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    /**
     * Mark this notification as read.
     */
    public function markAsRead()
    {
        $this->is_read = true;
        return $this->save();
    }

    /**
     * Mark all unread notifications as read for a given user.
     */
    public static function markAllAsRead($user_id)
    {
        return static::forUser($user_id)->unread()->update(['is_read' => true]);
    }
}
