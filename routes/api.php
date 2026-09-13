<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Mobile\Http\Controllers\Api\MobileApiController;

Route::prefix('mobile')->name('mobile.api.')->group(function () {
    Route::get('/tables', [MobileApiController::class, 'getTables']);
    Route::get('/orders', [MobileApiController::class, 'getOrders']);
    Route::post('/order', [MobileApiController::class, 'createOrder']);
    Route::post('/sync', [MobileApiController::class, 'sync']);
    Route::get('/menu', [MobileApiController::class, 'getMenu']);
});