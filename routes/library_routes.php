<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'SupportTeam', 'middleware' => ['auth', 'teamSAT'], 'prefix' => 'library'], function () {
    Route::get('/', 'LibraryController@index')->name('library.index');
    Route::get('/create', 'LibraryController@create')->name('library.create');
    Route::post('/store', 'LibraryController@store')->name('library.store');
    Route::get('/edit/{id}', 'LibraryController@edit')->name('library.edit');
    Route::put('/update/{id}', 'LibraryController@update')->name('library.update');
    Route::delete('/destroy/{id}', 'LibraryController@destroy')->name('library.destroy');
    Route::get('/loan/{book_id}', 'LibraryController@loanForm')->name('library.loan_form');
    Route::post('/loan', 'LibraryController@issueLoan')->name('library.issue_loan');
    Route::post('/return/{request_id}', 'LibraryController@returnBook')->name('library.return_book');
    Route::get('/export', 'LibraryController@exportExcel')->name('library.export');
    Route::get('/print-card/{id}', 'LibraryController@printCard')->name('library.print_card');
});
