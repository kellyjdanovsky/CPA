<?php

namespace App\Services;

use App\Models\InternalNotification;
use App\User;
use App\Helpers\Qs;
use Carbon\Carbon;
use App\Models\PaymentRecord;
use Illuminate\Support\Facades\File;

class NotificationService
{
    /**
     * Create a specific notification for a user
     */
    public static function notify($user_id, $type, $title, $message, $link = null, $icon = null, $color = null, $priority = 'medium')
    {
        return InternalNotification::create([
            'user_id' => $user_id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'icon' => $icon,
            'color' => $color,
            'priority' => $priority,
        ]);
    }

    /**
     * Send notification to all super_admin and admin users
     */
    public static function notifyAdmins($type, $title, $message, $link = null, $icon = null, $color = null, $priority = 'medium')
    {
        $admins = User::whereIn('user_type', ['super_admin', 'admin'])->get();
        foreach ($admins as $admin) {
            self::notify($admin->id, $type, $title, $message, $link, $icon, $color, $priority);
        }
    }

    /**
     * Send notification to a specific role
     */
    public static function notifyRole($role, $type, $title, $message, $link = null, $icon = null, $color = null, $priority = 'medium')
    {
        $users = User::where('user_type', $role)->get();
        foreach ($users as $user) {
            self::notify($user->id, $type, $title, $message, $link, $icon, $color, $priority);
        }
    }

    /**
     * Check system state and create alerts
     */
    public static function checkAndCreateAlerts()
    {
        $current_session = Qs::getCurrentSession();

        // 1. Unpaid students alert
        if (class_exists('App\Models\PaymentRecord')) {
            $unpaid_count = PaymentRecord::where('year', $current_session)
                ->where('paid', 0)
                ->where('balance', '>', 0)
                ->count();

            if ($unpaid_count > 10) {
                self::notifyAdmins(
                    'unpaid_alert',
                    'Alerte Impayés',
                    "Il y a actuellement {$unpaid_count} élèves avec des paiements en retard.",
                    route('payments.manage'),
                    'icon-cash3',
                    'danger',
                    'high'
                );
            }
        }

        // 2. Backup needed alert
        $backupDir = storage_path('app/backups');
        $needsBackup = true;
        if (File::exists($backupDir)) {
            $files = File::files($backupDir);
            foreach ($files as $file) {
                if (Carbon::createFromTimestamp(File::lastModified($file))->diffInDays(now()) < 7) {
                    $needsBackup = false;
                    break;
                }
            }
        }
        
        if ($needsBackup) {
            $super_admins = User::where('user_type', 'super_admin')->get();
            foreach ($super_admins as $admin) {
                self::notify(
                    $admin->id,
                    'backup_needed',
                    'Sauvegarde Requise',
                    'La dernière sauvegarde date de plus de 7 jours.',
                    route('super_admin.backups'),
                    'icon-database-time2',
                    'warning',
                    'high'
                );
            }
        }

        // 3. Term ending (example heuristic: if we are in May/June or Nov/Dec)
        $currentMonth = now()->month;
        if (in_array($currentMonth, [5, 6, 11, 12]) && now()->day > 15) {
            self::notifyRole(
                'teacher',
                'term_ending',
                'Fin de Trimestre',
                'La fin du trimestre approche. Pensez à finaliser la saisie des notes.',
                route('marks.index'),
                'icon-calendar-warning',
                'info',
                'medium'
            );
        }
    }

    /**
     * Get unread count for user
     */
    public static function getUnreadCount($user_id)
    {
        return InternalNotification::forUser($user_id)->unread()->count();
    }

    /**
     * Get recent notifications
     */
    public static function getRecent($user_id, $limit = 10)
    {
        return InternalNotification::forUser($user_id)->recent($limit)->get();
    }

    /**
     * Clean old notifications
     */
    public static function cleanOld($days = 90)
    {
        return InternalNotification::where('created_at', '<', now()->subDays($days))->delete();
    }
}
