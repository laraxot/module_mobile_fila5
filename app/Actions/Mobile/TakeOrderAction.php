<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions;

use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use function Safe\json_encode;
use Spatie\QueueableAction\QueueableAction;

/**
 * Azione presa ordine cameriere — NativePHP offline-first.
 *
 * Accetta dati ordine da mobile (QR tavolo, prodotti, modificatori),
 * crea/aggiorna ticket in stato offline, sincronizza quando online.
 *
 * @mixin TakeOrderAction
 */
class TakeOrderAction
{
    use QueueableAction;

    /** @param array<string, mixed> $payload */
    public function execute(array $payload): string
    {
        $waiterId = SafeStringCastAction::cast($payload['waiter_id'] ?? '');
        $tableId = SafeStringCastAction::cast($payload['table_id'] ?? '');
        $items = $payload['items'] ?? [];
        $qrCode = SafeStringCastAction::cast($payload['qr_code'] ?? '');

        $itemsPayload = is_array($items) ? $items : [];
        $content = [
            'waiter_id' => $waiterId,
            'table_id' => $tableId,
            'items' => $itemsPayload,
            'qr_code' => $qrCode,
            'status' => 'open',
            'source' => 'mobile',
            'created_at_utc' => date('c'),
        ];

        $ticket = Ticket::query()->firstOrCreate(
            ['code' => 'MOBILE-'.bin2hex(random_bytes(8))],
            [
                'name' => 'Ordine cameriere tavolo '.$tableId,
                'content' => json_encode($content),
                'owner_id' => $waiterId,
                'ticket_prefix' => 'MOBILE',
            ],
        );

        Ticket::withoutEvents(static function () use ($ticket, $content): void {
            Ticket::query()->whereKey($ticket->id)->update([
                'content' => json_encode($content),
            ]);
        });

        return (string) $ticket->code;
    }
}
