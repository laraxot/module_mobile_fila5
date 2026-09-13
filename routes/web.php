<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Mobile\Http\Controllers\MobileController;

Route::prefix('mobile')->name('mobile.')->group(function () {
    Route::get('/', [MobileController::class, 'index'])->name('index');
    Route::get('/tables', [MobileController::class, 'tables'])->name('tables');
    Route::get('/order/{table}', [MobileController::class, 'order'])->name('order');
    Route::post('/order/{table}/submit', [MobileController::class, 'submitOrder'])->name('order.submit');
    Route::get('/menu', [MobileController::class, 'menu'])->name('menu');
    Route::get('/sync', [MobileController::class, 'sync'])->name('sync');
});