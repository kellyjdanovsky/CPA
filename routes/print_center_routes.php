<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupportTeam\PrintCenterController;

Route::group(['middleware' => ['auth', 'teamSAT'], 'prefix' => 'print-center'], function () {
    Route::get('/', [PrintCenterController::class, 'index'])->name('print-center.index');
});
