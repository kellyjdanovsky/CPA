<?php

use Illuminate\Support\Facades\Route;

Route::prefix('attendance')->middleware(['auth', 'teamSAT'])->group(function () {
    Route::get('/', 'SupportTeam\AttendanceController@index')->name('attendance.index');
    Route::get('/mark', 'SupportTeam\AttendanceController@markAttendance')->name('attendance.mark');
    Route::post('/store', 'SupportTeam\AttendanceController@store')->name('attendance.store');
    Route::get('/monthly-report', 'SupportTeam\AttendanceController@monthlyReport')->name('attendance.monthly-report');
    Route::get('/student/{student_id}', 'SupportTeam\AttendanceController@studentReport')->name('attendance.student-report');
    Route::get('/export-monthly', 'SupportTeam\AttendanceController@exportMonthly')->name('attendance.export-monthly');
    Route::get('/print-monthly', 'SupportTeam\AttendanceController@printMonthly')->name('attendance.print-monthly');
});
