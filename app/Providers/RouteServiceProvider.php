<?php

declare(strict_types=1);

namespace Modules\Mobile\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('api')
            ->prefix('mobile')
            ->group(__DIR__ . '/../../routes/api.php');

        Route::middleware('web')
            ->group(__DIR__ . '/../../routes/web.php');
    }
}
