<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth', 'prefix' => 'notifications'], function () {
    Route::get('/', 'NotificationController@index')->name('notifications.index');
    Route::get('/unread', 'NotificationController@getUnread')->name('notifications.unread');
    Route::post('/{id}/read', 'NotificationController@markAsRead')->name('notifications.read');
    Route::post('/read-all', 'NotificationController@markAllAsRead')->name('notifications.read-all');
    Route::delete('/{id}', 'NotificationController@destroy')->name('notifications.destroy');
});

Route::group(['middleware' => ['auth', 'teamSA'], 'prefix' => 'notifications'], function () {
    Route::post('/check-alerts', 'NotificationController@checkAlerts')->name('notifications.check-alerts');
});
