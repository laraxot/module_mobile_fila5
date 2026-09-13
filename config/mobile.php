<?php

declare(strict_types=1);

return [
<<<<<<< HEAD
    'enabled' => (getenv('MOBILE_ENABLED') ?: true),
    'auth_guard' => (getenv('MOBILE_AUTH_GUARD') ?: 'api'),
    'offline_ttl' => (int) (getenv('MOBILE_OFFLINE_TTL') ?: 3600),
    'sync_interval' => (int) (getenv('MOBILE_SYNC_INTERVAL') ?: 30),
    'onesignal_app_id' => (getenv('NATIVEPHP_ONESIGNAL_APP_ID') ?: null),
=======
    'enabled' => env('MOBILE_ENABLED', true),
    'auth_guard' => env('MOBILE_AUTH_GUARD', 'api'),
    'offline_ttl' => env('MOBILE_OFFLINE_TTL', 3600),
    'sync_interval' => env('MOBILE_SYNC_INTERVAL', 30),
>>>>>>> laraxot/dev
    'device_storage' => storage_path('app/mobile'),
];
