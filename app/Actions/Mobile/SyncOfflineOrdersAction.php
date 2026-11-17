<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Illuminate\Support\Facades\Http;
use Modules\Mobile\Contracts\RestaurantGateway;
use Modules\Mobile\Models\OrderQueue;
use Spatie\QueueableAction\QueueableAction;
use Throwable;

/**
 * Sync offline order queue with the server (story 2.2).
 * Preferisce RestaurantGateway::createOrder con source mobile_waiter.
 */
class SyncOfflineOrdersAction
{
    use QueueableAction;

    public function __construct(
        private readonly ?RestaurantGateway $restaurant = null,
    ) {
    }

    /** @return array{synced: list<int|string>, failed: list<int|string>, total: int, timestamp: string} */
    public function execute(string $waiterSessionId): array
    {
        $pending = OrderQueue::query()
            ->where('waiter_session_id', $waiterSessionId)
            ->where('status', OrderQueue::STATUS_PENDING)
            ->get();

        $synced = [];
        $failed = [];

        foreach ($pending as $queueItem) {
            try {
                $orderData = is_array($queueItem->order_data) ? $queueItem->order_data : [];
                $gateway = $this->restaurant ?? (app()->bound(RestaurantGateway::class) ? app(RestaurantGateway::class) : null);

                if ($gateway !== null) {
                    $gateway->createOrder([
                        'table_id' => $queueItem->table_id,
                        'idempotency_key' => (string) ($orderData['idempotency_key'] ?? $queueItem->id),
                        'items' => is_array($orderData['items'] ?? null) ? $orderData['items'] : [],
                        'covers' => (int) ($orderData['covers'] ?? 1),
                        'waiter_id' => $orderData['waiter_id'] ?? null,
                        'source' => 'mobile_waiter',
                        'send_to_stations' => true,
                    ]);
                    $this->markSynced($queueItem);
                    $synced[] = $queueItem->id;

                    continue;
                }

                $response = Http::timeout(15)
                    ->post((is_string(config('mobile.api_url')) ? config('mobile.api_url') : 'https://api.sottana.com').'/api/mobile/orders', [
                        'order_data' => $orderData,
                        'waiter_session_id' => $waiterSessionId,
                        'table_id' => $queueItem->table_id,
                        'queue_id' => $queueItem->id,
                        'idempotency_key' => $orderData['idempotency_key'] ?? $queueItem->id,
                    ]);

                if ($response->successful()) {
                    $this->markSynced($queueItem);
                    $synced[] = $queueItem->id;
                } else {
                    $this->markFailed($queueItem, $response->body());
                    $failed[] = $queueItem->id;
                }
            } catch (Throwable $e) {
                $this->markFailed($queueItem, $e->getMessage());
                $failed[] = $queueItem->id;
            }
        }

        return [
            'synced' => $synced,
            'failed' => $failed,
            'total' => $pending->count(),
            'timestamp' => now()->toISOString() ?? now()->toAtomString(),
        ];
    }

    private function markSynced(OrderQueue $queueItem): void
    {
        $queueItem->update([
            'status' => OrderQueue::STATUS_SYNCED,
            'sync_attempts' => $queueItem->sync_attempts + 1,
            'last_sync_at' => now(),
            'error_message' => null,
        ]);
    }

    private function markFailed(OrderQueue $queueItem, string $error): void
    {
        $queueItem->update([
            'status' => OrderQueue::STATUS_FAILED,
            'sync_attempts' => $queueItem->sync_attempts + 1,
            'error_message' => $error,
            'last_sync_at' => now(),
        ]);
    }
}
