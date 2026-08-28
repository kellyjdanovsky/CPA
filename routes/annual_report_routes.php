<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'teamSAT'], 'prefix' => 'reports/annual'], function() {
    Route::get('/', 'SupportTeam\AnnualReportController@index')->name('reports.annual.index');
    Route::get('/print', 'SupportTeam\AnnualReportController@printReport')->name('reports.annual.print');
    Route::get('/excel', 'SupportTeam\AnnualReportController@exportExcel')->name('reports.annual.excel');
});