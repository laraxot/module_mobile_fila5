<?php

return [
<<<<<<< HEAD
    'app_name' => (getenv('NATIVEPHP_APP_NAME') ?: 'Sottana Waiter'),
    'version' => (getenv('NATIVEPHP_APP_VERSION') ?: '1.0.0'),
=======
    'app_name' => env('NATIVEPHP_APP_NAME', 'Sottana Waiter'),
    'version' => env('NATIVEPHP_APP_VERSION', '1.0.0'),
>>>>>>> laraxot/dev
    'description' => 'Native mobile app for waiters to take orders and manage restaurant floor plans',
    'identifier' => 'com.sottana.waiter',
    'window' => [
        'width' => 390,
        'height' => 844,
        'title' => 'Sottana Waiter',
        'resizable' => false,
        'maximizable' => false,
        'fullscreenable' => false,
        'transparent' => false,
        'decorations' => false,
        'visible_on_all_workspaces' => false,
        'focused' => true,
        'icon' => 'icons/icon.icns',
        'background_color' => '#1a1a2e',
    ],
    'menu' => [
        'quit_on_close' => true,
        'native' => true,
        'items' => [],
    ],
    'updater' => [
        'enabled' => true,
        'provider' => 'github',
        'owner' => 'sottana',
        'repo' => 'restaurant-fila5',
        'channel' => 'stable',
        'frequency' => 3600,
    ],
    'php' => [
        'ini' => [
            'memory_limit' => '256M',
            'max_execution_time' => 0,
            'upload_max_filesize' => '50M',
            'post_max_size' => '50M',
        ],
        'extensions' => [
            'sqlite3', 'pdo_sqlite', 'mbstring', 'openssl', 'curl', 'zip', 'gd', 'intl',
        ],
    ],
    'nativephp' => [
        'plugins' => [
            'camera' => [
                'enabled' => true,
                'permission_reason' => 'This app needs camera access to scan QR codes for menu items and tables',
            ],
            'push_notifications' => [
                'enabled' => true,
                'provider' => 'onesignal',
<<<<<<< HEAD
                'app_id' => (getenv('NATIVEPHP_ONESIGNAL_APP_ID') ?: null),
                'api_key' => (getenv('NATIVEPHP_ONESIGNAL_API_KEY') ?: null),
                'safari_web_id' => (getenv('NATIVEPHP_ONESIGNAL_SAFARI_WEB_ID') ?: null),
=======
                'app_id' => env('NATIVEPHP_ONESIGNAL_APP_ID'),
                'api_key' => env('NATIVEPHP_ONESIGNAL_API_KEY'),
                'safari_web_id' => env('NATIVEPHP_ONESIGNAL_SAFARI_WEB_ID'),
>>>>>>> laraxot/dev
            ],
            'biometrics' => [
                'enabled' => true,
                'local_auth_reason' => 'Authenticate to access the waiter dashboard',
            ],
            'location' => [
                'enabled' => true,
                'permission_reason' => 'This app needs location access to show your position on the restaurant floor map',
                'accuracy' => 'best',
                'update_interval' => 30000,
            ],
            'haptics' => [
                'enabled' => true,
                'default_style' => 'medium',
            ],
            'sharing' => [
                'enabled' => true,
            ],
            'deep_links' => [
                'enabled' => true,
                'scheme' => 'myapp',
                'domains' => [
                    'sottana.com',
                    'app.sottana.com',
                ],
            ],
            'secure_storage' => [
                'enabled' => true,
<<<<<<< HEAD
                'key' => (getenv('NATIVEPHP_STORAGE_KEY') ?: null),
=======
                'key' => env('NATIVEPHP_STORAGE_KEY'),
>>>>>>> laraxot/dev
            ],
            'gallery' => [
                'enabled' => true,
            ],
            'offline' => [
                'enabled' => true,
                'database' => 'mobile_offline.db',
                'sync_interval' => 30000,
            ],
        ],
    ],
    'build' => [
        'android' => [
            'package' => 'com.sottana.waiter',
            'version_code' => 1,
            'version_name' => '1.0.0',
            'min_sdk' => 24,
            'target_sdk' => 34,
            'compile_sdk' => 34,
            'signing' => [
<<<<<<< HEAD
                'store_file' => (getenv('ANDROID_KEYSTORE_PATH') ?: null),
                'store_password' => (getenv('ANDROID_KEYSTORE_PASSWORD') ?: null),
                'key_alias' => (getenv('ANDROID_KEY_ALIAS') ?: null),
                'key_password' => (getenv('ANDROID_KEY_PASSWORD') ?: null),
=======
                'store_file' => env('ANDROID_KEYSTORE_PATH'),
                'store_password' => env('ANDROID_KEYSTORE_PASSWORD'),
                'key_alias' => env('ANDROID_KEY_ALIAS'),
                'key_password' => env('ANDROID_KEY_PASSWORD'),
>>>>>>> laraxot/dev
            ],
            'permissions' => [
                'INTERNET',
                'CAMERA',
                'ACCESS_FINE_LOCATION',
                'ACCESS_COARSE_LOCATION',
                'VIBRATE',
                'FOREGROUND_SERVICE',
                'RECEIVE_BOOT_COMPLETED',
                'WAKE_LOCK',
            ],
        ],
        'ios' => [
            'bundle_id' => 'com.sottana.waiter',
            'version' => '1.0.0',
            'build' => '1',
            'deployment_target' => '15.0',
            'signing' => [
<<<<<<< HEAD
                'team_id' => (getenv('IOS_TEAM_ID') ?: null),
                'provisioning_profile' => (getenv('IOS_PROVISIONING_PROFILE') ?: null),
=======
                'team_id' => env('IOS_TEAM_ID'),
                'provisioning_profile' => env('IOS_PROVISIONING_PROFILE'),
>>>>>>> laraxot/dev
            ],
            'capabilities' => [
                'push_notifications' => true,
                'location' => true,
                'camera' => true,
            ],
            'info_plist' => [
                'NSCameraUsageDescription' => 'This app needs camera access to scan QR codes for menu items and tables',
                'NSLocationWhenInUseUsageDescription' => 'This app needs location access to show your position on the restaurant floor map',
                'NSLocationAlwaysAndWhenInUseUsageDescription' => 'This app needs location access to show your position on the restaurant floor map',
                'NSFaceIDUsageDescription' => 'This app uses Face ID to authenticate waiters',
                'UIBackgroundModes' => ['location', 'fetch', 'remote-notification'],
            ],
        ],
    ],
<<<<<<< HEAD
];
=======
];
>>>>>>> laraxot/dev
