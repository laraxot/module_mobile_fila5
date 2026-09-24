<?php

declare(strict_types=1);

<<<<<<< HEAD
/*
 * Bridge — file legacy, non autocaricato.
 *
 * Il composer.json del modulo Mobile mappa `Modules\Mobile\` su `app/` (solo PSR-4),
 * quindi questo file in `Modules/Mobile/notifications/` (fuori da `app/`) non viene
 * risolto dall'autoloader: le classi `KdsOrderNotification` e `KdsPushNotification`
 * usate da `Modules\Mobile\Actions\Mobile\KdsNotificationAction` risolvono invece a
 * `Modules/Mobile/app/Notifications/`, dove ora vivono davvero (un file per classe,
 * come richiede PSR-4).
 *
 * Contenuto storico conservato qui invariato (nessuna cancellazione, git forward-only):
 * vedi `Modules/Mobile/app/Notifications/KdsOrderNotification.php` e
 * `Modules/Mobile/app/Notifications/KdsPushNotification.php` per le versioni correnti.
 */

namespace Modules\Mobile\LegacyNotifications;
=======
namespace Modules\Mobile\Notifications;
>>>>>>> 07320e7 (docs: README.md con frontmatter YAML, link second brain e GitHub (bmad+second brain))

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
<<<<<<< HEAD
=======
use Modules\Mobile\Models\WaiterSession;
>>>>>>> 07320e7 (docs: README.md con frontmatter YAML, link second brain e GitHub (bmad+second brain))

class KdsOrderNotification extends Notification
{
    use Queueable;

    /** @param array<string, mixed> $payload */
<<<<<<< HEAD
    public function __construct(private readonly array $payload) {}
=======
    public function __construct(private readonly array $payload)
    {
    }
>>>>>>> 07320e7 (docs: README.md con frontmatter YAML, link second brain e GitHub (bmad+second brain))

    /** @return list<string> */
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

    /** @return array<string, mixed> */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Order',
            'body' => sprintf('Table %s - %d items', $this->scalar($this->payload['table_number'] ?? null), count(is_array($this->payload['items'] ?? null) ? $this->payload['items'] : [])),
            'data' => $this->payload,
        ];
    }

    private function scalar(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}

class KdsPushNotification extends Notification
{
    use Queueable;

    /** @param array<string, mixed> $payload */
<<<<<<< HEAD
    public function __construct(private readonly array $payload) {}
=======
    public function __construct(private readonly array $payload)
    {
    }
>>>>>>> 07320e7 (docs: README.md con frontmatter YAML, link second brain e GitHub (bmad+second brain))

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['onesignal'];
    }

    /** @return array<string, mixed> */
    public function toOneSignal(object $notifiable): array
    {
        return [
            'app_id' => config('mobile.onesignal_app_id'),
            'include_external_user_ids' => [$this->payload['waiter_id'] ?? ''],
<<<<<<< HEAD
            'headings' => ['en' => 'New Order - Table '.$this->scalar($this->payload['table_number'] ?? null)],
            'contents' => ['en' => 'New order from Table '.$this->scalar($this->payload['table_number'] ?? null)],
=======
            'headings' => ['en' => 'New Order - Table ' . $this->scalar($this->payload['table_number'] ?? null)],
            'contents' => ['en' => 'New order from Table ' . $this->scalar($this->payload['table_number'] ?? null)],
>>>>>>> 07320e7 (docs: README.md con frontmatter YAML, link second brain e GitHub (bmad+second brain))
            'data' => $this->payload,
        ];
    }

    private function scalar(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
