<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Illuminate\Support\Facades\Notification;
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
        $order = Order::with(['items.product', 'table'])->findOrFail($orderId);

        $payload = [
            'order_id' => $order->id,
            'table_number' => $order->table?->getAttribute('number') ?? $order->table?->getAttribute('name'),
            'table_name' => $order->table?->getAttribute('name'),
            'waiter_name' => 'Unknown',
            'items' => $order->items->map(function (OrderItem $item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product->name ?? 'Unknown',
                    'quantity' => $item->quantity,
                    'notes' => $item->notes,
                    'modifiers' => $item->modifiers ?? [],
                    'status' => $item->kitchen_status ?? 'pending',
                    'course' => $item->product->course ?? 'main',
                ];
            })->values(),
            'notes' => is_scalar($order->getAttribute('notes')) ? (string) $order->getAttribute('notes') : '',
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
        $rawNotes = $order->getAttribute('notes');
        $notes = is_scalar($rawNotes) ? (string) $rawNotes : '';
        if ($notes !== '' && stripos($notes, 'rush') !== false) {
            $priority += 100;
        }

        // Courses: appetizers first, then mains, then desserts
        $hasAppetizer = $items->contains(fn($i) => ($i->product->course ?? '') === 'appetizer');
        $hasMain = $items->contains(fn($i) => ($i->product->course ?? '') === 'main');

        if ($hasAppetizer) $priority += 50;
        if ($hasMain) $priority += 10;

        // Large parties higher priority
        $capacity = $order->table?->getAttribute('capacity');
        if (is_numeric($capacity) && (int) $capacity >= 6) {
            $priority += 20;
        }

        return $priority;
    }

    /** @return \Illuminate\Support\Collection<int, object> */
    private function getKitchenStaff(): \Illuminate\Support\Collection
    {
        /** @var \Illuminate\Support\Collection<int, object> $staff */
        $staff = collect();

        return $staff;
    }
}
