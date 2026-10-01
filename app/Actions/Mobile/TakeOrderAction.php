<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Modules\Mobile\Models\OrderQueue;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

/**
 * Azione presa ordine cameriere — NativePHP offline-first.
 *
 * Accetta dati ordine da mobile (tavolo, prodotti, note, turno), accoda
 * l'ordine in `mobile_order_queue` in stato pending e lascia il sync al
 * worker `SyncOfflineOrdersAction`.
 */
class TakeOrderAction
{
    use QueueableAction;

    /**
     * @param list<array{product_id: int, quantity: int|float, unit_price: int|float, notes?: string|null, modifiers?: array<mixed>}> $items
     * @param array<string, mixed>|null $notes
     * @return array{queue_id: string, waiter_session_id: string, table_id: int, status: string, sync_attempts: int}
     */
    public function execute(
        string $waiterSessionId,
        int $tableId,
        array $items = [],
        ?array $notes = null,
        ?string $shiftId = null,
    ): array {
        $waiterSessionKey = SafeStringCastAction::cast($waiterSessionId);

        $order = OrderQueue::query()->create([
            'waiter_session_id' => $waiterSessionKey,
            'table_id' => $tableId,
            'status' => OrderQueue::STATUS_PENDING,
            'order_data' => [
                'waiter_session_id' => $waiterSessionKey,
                'table_id' => $tableId,
                'shift_id' => $shiftId,
                'items' => $items,
                'notes' => $notes ?? [],
                'status' => 'open',
                'source' => 'mobile',
                'created_at_utc' => date('c'),
            ],
        ]);

        return [
            'queue_id' => $order->id,
            'waiter_session_id' => $waiterSessionKey,
            'table_id' => $tableId,
            'status' => OrderQueue::STATUS_PENDING,
            'sync_attempts' => $order->sync_attempts,
        ];
    }
}
