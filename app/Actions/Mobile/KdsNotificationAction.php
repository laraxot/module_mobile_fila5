<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Modules\Restaurant\Models\Order;
use Modules\Restaurant\Models\OrderItem;
use Spatie\QueueableAction\QueueableAction;

/**
 * Action for sending order to Kitchen Display System (KDS).
 * Pushes notification to kitchen screens and mobile devices.
 */
class KdsNotificationAction
{
    use QueueableAction;

    public function execute(int $orderId, bool $isNewOrder = true): void
    {
        $order = Order::with(['items.product', 'table', 'waiter'])->findOrFail($orderId);

        $payload = [
            'order_id' => $order->id,
            'table_number' => $order->table?->name,
            'table_name' => $order->table?->name,
            'waiter_name' => $order->waiter->name ?? 'Unknown',
            'items' => $order->items->map(static function (OrderItem $item): array {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product->name ?? 'Unknown',
                    'quantity' => $item->quantity,
                    'notes' => $item->notes,
                    'modifiers' => [],
                    'status' => $item->status,
                    'course' => 'main',
                ];
            })->values(),
            'notes' => $order->cashier_note,
            'priority' => $this->calculatePriority($order),
            'timestamp' => now()->toISOString(),
            'type' => $isNewOrder ? 'new_order' : 'update',
        ];

        // Broadcast to kitchen channel
        broadcast(new \Modules\Mobile\Notifications\KdsOrderNotification($payload))
            ->toOthers();

        // Also send push notification to kitchen staff
        Notification::send(
            $this->getKitchenStaff(),
            new \Modules\Mobile\Notifications\KdsPushNotification($payload)
        );
    }

    private function calculatePriority(Order $order): int
    {
        $priority = 0;
        $items = $order->items;

        // Rush orders get higher priority
        if ($order->cashier_note && stripos($order->cashier_note, 'rush') !== false) {
            $priority += 100;
        }

        // Courses: appetizers first, then mains, then desserts
        $hasAppetizer = false;
        $hasMain = $items->isNotEmpty();

        if ($hasMain) $priority += 10;

        // Large parties higher priority
        if ($order->table && ($order->table->seats ?? 0) >= 6) {
            $priority += 20;
        }

        return $priority;
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, User> */
    private function getKitchenStaff(): \Illuminate\Database\Eloquent\Collection
    {
        return User::query()->get();
    }
}
