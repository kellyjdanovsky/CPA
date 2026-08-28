<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\ActivityLogController;

Route::group(['middleware' => ['auth', 'super_admin'], 'prefix' => 'super_admin/activity-logs'], function () {
    Route::get('/', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/export', [ActivityLogController::class, 'exportExcel'])->name('activity-logs.export');
    Route::post('/cleanup', [ActivityLogController::class, 'cleanup'])->name('activity-logs.cleanup');
    Route::get('/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
});
