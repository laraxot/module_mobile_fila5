<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Mobile\Http\Controllers\Api\MobileController;

Route::prefix('mobile')->name('mobile.api.')->group(function () {
    Route::get('/tables', [MobileController::class, 'getFloorPlan']);
    Route::get('/orders', [MobileController::class, 'getCurrentOrders']);
    Route::post('/order', [MobileController::class, 'takeOrder']);
    Route::post('/sync', [MobileController::class, 'syncQueue']);
    Route::get('/menu', [MobileController::class, 'getCurrentOrders']);
});
