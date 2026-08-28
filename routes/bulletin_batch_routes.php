<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth', 'teamSAT'], 'prefix' => 'marks/batch'], function() {
    Route::get('/zip/{exam_id}/{class_id}/{section_id}', 'SupportTeam\MarkController@batchDownloadPdfZip')->name('marks.batch.zip');
    Route::get('/print/{exam_id}/{class_id}/{section_id}', 'SupportTeam\MarkController@batchPrintView')->name('marks.batch.print');
});