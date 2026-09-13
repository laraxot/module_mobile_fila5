<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use Modules\Mobile\Http\Controllers\Api\MobileController;

Route::prefix('mobile')->name('mobile.api.')->group(function () {
    Route::get('/tables', [MobileController::class, 'getFloorPlan']);
    Route::get('/orders', [MobileController::class, 'getCurrentOrders']);
    Route::post('/order', [MobileController::class, 'takeOrder']);
    Route::post('/sync', [MobileController::class, 'syncQueue']);
    Route::get('/menu', [MobileController::class, 'getCurrentOrders']);
});
=======
use Modules\Mobile\Http\Controllers\Api\MobileApiController;

Route::prefix('mobile')->name('mobile.api.')->group(function () {
    Route::get('/tables', [MobileApiController::class, 'getTables']);
    Route::get('/orders', [MobileApiController::class, 'getOrders']);
    Route::post('/order', [MobileApiController::class, 'createOrder']);
    Route::post('/sync', [MobileApiController::class, 'sync']);
    Route::get('/menu', [MobileApiController::class, 'getMenu']);
});
>>>>>>> laraxot/dev
