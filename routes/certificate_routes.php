<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'teamSA'], 'prefix' => 'certificates'], function() {
    Route::get('/', 'SupportTeam\CertificateController@index')->name('certificates.index');
    Route::get('/create', 'SupportTeam\CertificateController@create')->name('certificates.create');
    Route::post('/generate', 'SupportTeam\CertificateController@generate')->name('certificates.generate');
    Route::get('/print/{id}', 'SupportTeam\CertificateController@print')->name('certificates.print');
    Route::match(['get', 'post'], '/batch-generate', 'SupportTeam\CertificateController@batchGenerate')->name('certificates.batch_generate');
    Route::get('/export', 'SupportTeam\CertificateController@exportList')->name('certificates.export');
    Route::delete('/{id}', 'SupportTeam\CertificateController@destroy')->name('certificates.destroy');
});
