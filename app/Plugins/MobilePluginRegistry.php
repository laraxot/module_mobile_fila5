<?php

declare(strict_types=1);

namespace Modules\Mobile;

<<<<<<< HEAD
=======
use NativePHP\Plugins\Camera\CameraPlugin;
use NativePHP\Plugins\PushNotifications\PushNotificationsPlugin;
use NativePHP\Plugins\Biometrics\BiometricsPlugin;
use NativePHP\Plugins\Location\LocationPlugin;
use NativePHP\Plugins\Haptics\HapticsPlugin;
use NativePHP\Plugins\Sharing\SharingPlugin;
use NativePHP\Plugins\DeepLinks\DeepLinksPlugin;
use NativePHP\Plugins\SecureStorage\SecureStoragePlugin;
use NativePHP\Plugins\Gallery\GalleryPlugin;
use NativePHP\Plugins\Offline\OfflinePlugin;

>>>>>>> laraxot/dev
class MobilePluginRegistry
{
    /**
     * Get all enabled NativePHP plugins for the waiter app.
     *
<<<<<<< HEAD
     * @return list<class-string>
     */
    public static function getPlugins(): array
    {
        // NativePHP plugins are optional dependencies; the host registers them
        // when the corresponding packages are installed.
        return [];
=======
     * @return array<class-string>
     */
    public static function getPlugins(): array
    {
        return [
            CameraPlugin::class,
            PushNotificationsPlugin::class,
            BiometricsPlugin::class,
            LocationPlugin::class,
            HapticsPlugin::class,
            SharingPlugin::class,
            DeepLinksPlugin::class,
            SecureStoragePlugin::class,
            GalleryPlugin::class,
            OfflinePlugin::class,
        ];
>>>>>>> laraxot/dev
    }

    /**
     * Get plugin configuration for the waiter app.
<<<<<<< HEAD
     *
     * @return array<string, array<string, bool|string>>
=======
>>>>>>> laraxot/dev
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
<<<<<<< HEAD
}
=======
}
>>>>>>> laraxot/dev
