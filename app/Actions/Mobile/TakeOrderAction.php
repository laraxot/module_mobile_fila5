<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use InvalidArgumentException;
use Modules\Mobile\Contracts\RestaurantGateway;
use Modules\Mobile\Models\OrderQueue;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;
use Throwable;

/**
 * Presa ordine del cameriere, offline-first (story 2.2).
 *
 * Accoda l'ordine in `mobile_order_queue` con stato pending.
 * Valida il tavolo tramite RestaurantGateway (catalogo/tavoli reali).
 * Idempotency: stessa chiave + sessione → stessa riga in coda (no duplicati).
 */
class TakeOrderAction
{
    use QueueableAction;

    public function __construct(
        private readonly RestaurantGateway $restaurant,
    ) {}

    /** @param array<string, mixed> $payload */
    public function execute(array $payload): string
    {
        $waiterSessionId = SafeStringCastAction::cast($payload['waiter_session_id'] ?? '');
        $waiterId = SafeStringCastAction::cast($payload['waiter_id'] ?? '');
        $tableId = SafeStringCastAction::cast($payload['table_id'] ?? '');
        $items = $payload['items'] ?? [];
        $qrCode = SafeStringCastAction::cast($payload['qr_code'] ?? '');
        $idempotencyKey = SafeStringCastAction::cast($payload['idempotency_key'] ?? '');

        if ($waiterSessionId === '' || $tableId === '') {
            throw new InvalidArgumentException('waiter_session_id e table_id sono obbligatori');
        }

        if ($idempotencyKey === '') {
            throw new InvalidArgumentException('idempotency_key è obbligatoria');
        }

        if (! is_array($items) || $items === []) {
            throw new InvalidArgumentException('items deve contenere almeno una riga');
        }

        // Normalizza product_id/quantity interi lato server già in coda
        $normalizedItems = [];
        foreach ($items as $row) {
            if (! is_array($row)) {
                throw new InvalidArgumentException('ogni item deve essere un oggetto');
            }
            $normalizedItems[] = [
                'product_id' => SafeIntCastAction::cast($row['product_id'] ?? 0),
                'quantity' => max(1, SafeIntCastAction::cast($row['quantity'] ?? 1, 1)),
                'notes' => isset($row['notes']) ? SafeStringCastAction::cast($row['notes']) : null,
                'modifiers' => is_array($row['modifiers'] ?? null) ? $row['modifiers'] : [],
            ];
            if ($normalizedItems[array_key_last($normalizedItems)]['product_id'] < 1) {
                throw new InvalidArgumentException('product_id non valido');
            }
        }

        $table = $this->restaurant->table($tableId);
        if ($table === null) {
            throw new InvalidArgumentException('Tavolo non trovato o non attivo');
        }

        $existing = $this->findExistingQueue($waiterSessionId, $idempotencyKey);
        if ($existing !== null) {
            return $existing->id;
        }

        $order = OrderQueue::query()->create([
            'waiter_session_id' => $waiterSessionId,
            'table_id' => (int) $tableId,
            'order_data' => [
                'waiter_id' => $waiterId,
                'table_id' => (int) $tableId,
                'table_name' => $table->name,
                'items' => $normalizedItems,
                'qr_code' => $qrCode,
                'idempotency_key' => $idempotencyKey,
                'status' => 'open',
                'source' => 'mobile_waiter',
                'channel' => 'waiter',
                'created_at_utc' => date('c'),
            ],
            'status' => OrderQueue::STATUS_PENDING,
        ]);

        return $order->id;
    }

    private function findExistingQueue(string $waiterSessionId, string $idempotencyKey): ?OrderQueue
    {
        $base = OrderQueue::query()
            ->where('waiter_session_id', $waiterSessionId)
            ->where('status', '!=', OrderQueue::STATUS_FAILED);

        try {
            $hit = (clone $base)
                ->where('order_data->idempotency_key', $idempotencyKey)
                ->first();
            if ($hit !== null) {
                return $hit;
            }
        } catch (Throwable) {
            // driver senza JSON path: fallback sotto
        }

        return $base->get()->first(static function (OrderQueue $row) use ($idempotencyKey): bool {
            $data = is_array($row->order_data) ? $row->order_data : [];

            return ($data['idempotency_key'] ?? null) === $idempotencyKey;
        });
    }
}
