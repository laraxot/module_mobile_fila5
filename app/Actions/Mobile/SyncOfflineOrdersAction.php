<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Illuminate\Support\Facades\Http;
<<<<<<< HEAD
use Modules\Mobile\Models\OrderQueue;
=======
>>>>>>> laraxot/dev
use Modules\Restaurant\Models\Order;
use Spatie\QueueableAction\QueueableAction;

/**
 * Sync offline order queue with the server.
 * Runs via scheduled task or when connection restored.
 */
class SyncOfflineOrdersAction
{
    use QueueableAction;

<<<<<<< HEAD
    /** @return array{synced: list<string>, failed: list<string>, total: int, timestamp: string} */
    public function execute(string $waiterSessionId): array
    {
        $pending = OrderQueue::where('waiter_session_id', $waiterSessionId)
=======
    public function execute(string $waiterSessionId): array
    {
        $pending = \Modules\Mobile\Models\OrderQueue::where('waiter_session_id', $waiterSessionId)
>>>>>>> laraxot/dev
            ->where('status', OrderQueue::STATUS_PENDING)
            ->get();

        $synced = [];
        $failed = [];

        foreach ($pending as $queueItem) {
            try {
<<<<<<< HEAD
                $apiUrl = config('mobile.api_url', 'https://api.sottana.com');
                $apiUrl = is_string($apiUrl) ? $apiUrl : 'https://api.sottana.com';
                $response = Http::timeout(15)
                    ->post($apiUrl . '/api/mobile/orders', [
=======
                $response = Http::timeout(15)
                    ->post(env('MOBILE_API_URL', 'https://api.sottana.com') . '/api/mobile/orders', [
>>>>>>> laraxot/dev
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
<<<<<<< HEAD
            'timestamp' => now()->toIso8601String(),
        ];
    }

    private function markFailed(OrderQueue $queueItem, string $error): void
=======
            'timestamp' => now()->toISOString(),
        ];
    }

    private function markFailed(\Modules\Mobile\Models\OrderQueue $queueItem, string $error): void
>>>>>>> laraxot/dev
    {
        $attempts = $queueItem->sync_attempts + 1;
        $queueItem->update([
            'status' => OrderQueue::STATUS_FAILED,
            'sync_attempts' => $attempts,
            'error_message' => $error,
            'last_sync_at' => now(),
        ]);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> laraxot/dev
