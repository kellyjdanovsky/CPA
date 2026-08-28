<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'teamSAT']], function() {

    // Discipline
    Route::group(['prefix' => 'discipline'], function() {
        Route::get('/', 'DisciplineController@index')->name('discipline.index');
        Route::get('/create', 'DisciplineController@create')->name('discipline.create');
        Route::post('/', 'DisciplineController@store')->name('discipline.store');
        Route::get('/show/{id}', 'DisciplineController@show')->name('discipline.show');
        Route::get('/student/{student_id}', 'DisciplineController@studentHistory')->name('discipline.student_history');
        Route::get('/class-report', 'DisciplineController@classReport')->name('discipline.class_report');
        Route::get('/export', 'DisciplineController@exportExcel')->name('discipline.export');
        Route::get('/print', 'DisciplineController@printReport')->name('discipline.print');
        Route::delete('/{id}', 'DisciplineController@destroy')->name('discipline.destroy');
    });

    // Calendar
    Route::group(['prefix' => 'calendar'], function() {
        Route::get('/', 'CalendarController@index')->name('calendar.index');
        Route::get('/create', 'CalendarController@create')->name('calendar.create');
        Route::post('/', 'CalendarController@store')->name('calendar.store');
        Route::get('/edit/{id}', 'CalendarController@edit')->name('calendar.edit');
        Route::put('/{id}', 'CalendarController@update')->name('calendar.update');
        Route::delete('/{id}', 'CalendarController@destroy')->name('calendar.destroy');
        Route::get('/month-data', 'CalendarController@monthData')->name('calendar.month_data');
        Route::get('/annual', 'CalendarController@annualView')->name('calendar.annual_view');
        Route::get('/print-annual', 'CalendarController@printAnnual')->name('calendar.print_annual');
        Route::get('/export', 'CalendarController@exportExcel')->name('calendar.export');
    });

});
