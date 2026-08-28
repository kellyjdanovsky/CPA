<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'SupportTeam', 'middleware' => ['auth', 'teamSA'], 'prefix' => 'staff'], function () {
    Route::get('/', 'StaffController@index')->name('staff.index');
    Route::get('/show/{id}', 'StaffController@show')->name('staff.show');
    Route::get('/edit/{id}', 'StaffController@edit')->name('staff.edit');
    Route::put('/update/{id}', 'StaffController@update')->name('staff.update');
    Route::get('/export', 'StaffController@exportExcel')->name('staff.export');
    Route::get('/print-list', 'StaffController@printList')->name('staff.print_list');
});
