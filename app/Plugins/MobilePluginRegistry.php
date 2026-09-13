<?php

declare(strict_types=1);

namespace Modules\Mobile;

class MobilePluginRegistry
{
    /**
     * Get all enabled NativePHP plugins for the waiter app.
     *
     * @return list<class-string>
     */
    public static function getPlugins(): array
    {
        // NativePHP plugins are optional dependencies; the host registers them
        // when the corresponding packages are installed.
        return [];
    }

    /**
     * Get plugin configuration for the waiter app.
     *
     * @return array<string, array<string, bool|string>>
     */
    public static function getPluginConfig(): array
    {
        return [
            'camera' => [
                'enabled' => true,
                'permission_reason' => 'Scan QR codes for menu items and tables',
            ],
            'push_notifications' => [
                'enabled' => true,
                'provider' => 'onesignal',
            ],
            'biometrics' => [
                'enabled' => true,
                'local_auth_reason' => 'Authenticate to access waiter dashboard',
            ],
            'location' => [
                'enabled' => true,
                'permission_reason' => 'Show waiter position on floor map',
                'accuracy' => 'best',
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
            ],
            'secure_storage' => [
                'enabled' => true,
            ],
            'gallery' => [
                'enabled' => true,
            ],
            'offline' => [
                'enabled' => true,
                'database' => 'mobile_offline.db',
            ],
        ];
    }
}
