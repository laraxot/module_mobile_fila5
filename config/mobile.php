<?php

declare(strict_types=1);

return [
    'enabled' => env('MOBILE_ENABLED', true),
    'auth_guard' => env('MOBILE_AUTH_GUARD', 'api'),
    'offline_ttl' => env('MOBILE_OFFLINE_TTL', 3600),
    'sync_interval' => env('MOBILE_SYNC_INTERVAL', 30),
    'device_storage' => storage_path('app/mobile'),
];
