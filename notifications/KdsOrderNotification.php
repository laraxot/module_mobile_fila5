<?php

declare(strict_types=1);

namespace Modules\Mobile\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Modules\Mobile\Models\WaiterSession;

class KdsOrderNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly array $payload)
    {
    }

    public function via(object $notifiable): array
    {
        return ['broadcast', 'database'];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'order_id' => $this->payload['order_id'],
            'table_number' => $this->payload['table_number'],
            'items' => $this->payload['items'],
            'type' => 'kds_order',
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Order',
            'body' => "Table {$this->payload['table_number']} - " . count($this->payload['items']) . " items",
            'data' => $this->payload,
        ];
    }
}

class KdsPushNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly array $payload)
    {
    }

    public function via(object $notifiable): array
    {
        return ['onesignal'];
    }

    public function toOneSignal(object $notifiable): array
    {
        return [
            'app_id' => env('NATIVEPHP_ONESIGNAL_APP_ID'),
            'include_external_user_ids' => [$this->payload['waiter_id'] ?? ''],
            'headings' => ['en' => 'New Order - Table ' . $this->payload['table_number']],
            'contents' => ['en' => 'New order from Table ' . $this->payload['table_number']],
            'data' => $this->payload,
        ];
    }
}