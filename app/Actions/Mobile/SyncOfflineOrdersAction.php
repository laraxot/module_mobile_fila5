<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Illuminate\Support\Facades\Http;
use Modules\Mobile\Models\OrderQueue;
use Modules\Restaurant\Models\Order;
use Spatie\QueueableAction\QueueableAction;

/**
 * Sync offline order queue with the server.
 * Runs via scheduled task or when connection restored.
 */
class SyncOfflineOrdersAction
{
    use QueueableAction;

    /** @return array{synced: list<string>, failed: list<string>, total: int, timestamp: string} */
    public function execute(string $waiterSessionId): array
    {
        $pending = OrderQueue::where('waiter_session_id', $waiterSessionId)
            ->where('status', OrderQueue::STATUS_PENDING)
            ->get();

        $synced = [];
        $failed = [];

        foreach ($pending as $queueItem) {
            try {
                $apiUrl = config('mobile.api_url', 'https://api.sottana.com');
                $apiUrl = is_string($apiUrl) ? $apiUrl : 'https://api.sottana.com';
                $response = Http::timeout(15)
                    ->post($apiUrl . '/api/mobile/orders', [
                        'order_data' => $queueItem->order_data,
                        'waiter_session_id' => $waiterSessionId,
                        'table_id' => $queueItem->table_id,
                        'queue_id' => $queueItem->id,
                    ]);

                if ($response->successful()) {
                    $queueItem->update([
                        'status' => OrderQueue::STATUS_SYNCED,
                        'sync_attempts' => $queueItem->sync_attempts + 1,
                        'last_sync_at' => now(),
                    ]);
                    $synced[] = $queueItem->id;
                } else {
                    $this->markFailed($queueItem, $response->body());
                    $failed[] = $queueItem->id;
                }
            } catch (\Throwable $e) {
                $this->markFailed($queueItem, $e->getMessage());
                $failed[] = $queueItem->id;
            }
        }

        return [
            'synced' => $synced,
            'failed' => $failed,
            'total' => $pending->count(),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    private function markFailed(OrderQueue $queueItem, string $error): void
    {
        $attempts = $queueItem->sync_attempts + 1;
        $queueItem->update([
            'status' => OrderQueue::STATUS_FAILED,
            'sync_attempts' => $attempts,
            'error_message' => $error,
            'last_sync_at' => now(),
        ]);
    }
}
