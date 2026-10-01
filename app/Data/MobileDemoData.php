<?php

declare(strict_types=1);

namespace Modules\Mobile\App\Data;

/**
 * Mobile App Demo Data (Agnostic).
 * For investor demo - shows citizen mobile flows.
 */
class MobileDemoData
{
    /** @var array<string, mixed> */
    public static array $appConfig = [
        'name' => 'FixCity Mobile',
        'version' => '1.0.0-demo',
        'features' => ['report', 'map', 'notifications', 'profile'],
        'flows' => [
            'guest' => [
                'view_map', 'view_reports', 'report_anon',
            ],
            'user' => [
                'view_map', 'view_reports', 'create_report',
                'view_my_reports', 'receive_notifications',
            ],
            'moderator' => [
                'view_map', 'moderate_reports', 'approve', 'reject',
            ],
            'admin' => [
                'manage_users', 'manage_reports', 'analytics',
                'system_config',
            ],
        ],
    ];

    public static function toJson(): string
    {
        return \Safe\json_encode(self::$appConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
