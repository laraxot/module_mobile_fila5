<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Illuminate\Support\Facades\DB;
use Modules\Mobile\Models\WaiterSession;
use Modules\Mobile\Models\OrderQueue;
use Modules\Restaurant\Models\Order;
use Modules\Restaurant\Models\OrderItem;
use Modules\Restaurant\Models\DiningTable;
<<<<<<< HEAD
=======
use Modules\Restaurant\Enums\OrderStatusEnum;
>>>>>>> laraxot/dev
use Spatie\QueueableAction\QueueableAction;

/**
 * Action for waiter to take order from mobile device.
 * Creates order in offline queue if no connection, syncs when online.
 */
class TakeOrderAction
{
    use QueueableAction;

<<<<<<< HEAD
    /**
     * @param list<array{product_id: int, quantity: int|float, unit_price: int|float, notes?: string|null, modifiers?: array<mixed>}> $items
     * @param array<string, mixed>|null $notes
     */
=======
>>>>>>> laraxot/dev
    public function execute(
        string $waiterSessionId,
        int $tableId,
        array $items,
        ?array $notes = null,
        ?string $shiftId = null
    ): Order|OrderQueue {
        $session = WaiterSession::findOrFail($waiterSessionId);
        $table = DiningTable::findOrFail($tableId);

        $orderData = [
            'table_id' => $tableId,
            'user_id' => $session->user_id,
            'waiter_session_id' => $waiterSessionId,
            'shift_id' => $shiftId ?? $session->shift_id,
<<<<<<< HEAD
            'status' => 'pending',
=======
            'status' => OrderStatusEnum::PENDING,
>>>>>>> laraxot/dev
            'items' => $items,
            'notes' => $notes,
            'source' => 'mobile',
            'device_id' => $session->device_id,
        ];

        // Try to create order directly if online
        try {
            return DB::transaction(function () use ($orderData, $items) {
                $order = Order::create([
                    'table_id' => $orderData['table_id'],
                    'user_id' => $orderData['user_id'],
                    'shift_id' => $orderData['shift_id'],
                    'status' => $orderData['status'],
                    'notes' => $orderData['notes'] ?? '',
                    'source' => $orderData['source'],
                ]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'notes' => $item['notes'] ?? null,
                        'modifiers' => $item['modifiers'] ?? [],
                    ]);
                }

                return $order;
            });
        } catch (\Throwable $e) {
            // Queue offline
            return OrderQueue::create([
                'waiter_session_id' => $waiterSessionId,
                'table_id' => $tableId,
                'order_data' => $orderData,
                'status' => OrderQueue::STATUS_PENDING,
                'sync_attempts' => 0,
            ]);
        }
    }
}
