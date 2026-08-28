<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Log an activity.
     *
     * @param string $action
     * @param string $module
     * @param string $description
     * @param mixed $model
     * @param array|null $old_values
     * @param array|null $new_values
     * @param int|null $user_id
     * @return ActivityLog
     */
    public static function log($action, $module, $description, $model = null, $old_values = null, $new_values = null, $user_id = null)
    {
        return ActivityLog::create([
            'user_id' => $user_id ?? Auth::id(),
            'action' => $action,
            'module' => $module,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'ip_address' => request()->ip(),
            'old_values' => $old_values,
            'new_values' => $new_values,
        ]);
    }
}
