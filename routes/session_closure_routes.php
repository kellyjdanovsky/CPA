<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'teamSA'], 'prefix' => 'sessions/closure'], function() {
    Route::get('/', 'SupportTeam\SessionClosureController@wizard')->name('sessions.closure.wizard');
    Route::post('/step', 'SupportTeam\SessionClosureController@executeStep')->name('sessions.closure.step');
    Route::get('/print', 'SupportTeam\SessionClosureController@printClosureReport')->name('sessions.closure.print');
});