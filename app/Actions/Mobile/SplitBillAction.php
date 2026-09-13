<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Illuminate\Support\Facades\DB;
use Modules\Restaurant\Models\Order;
use Modules\Restaurant\Models\Payment;
<<<<<<< HEAD
=======
use Modules\Restaurant\Enums\PaymentMethodEnum;
use Modules\Restaurant\Enums\OrderStatusEnum;
>>>>>>> laraxot/dev
use Spatie\QueueableAction\QueueableAction;

/**
 * Action for splitting bill on mobile device.
 * Supports equal split, custom amounts, or item-based split.
 */
class SplitBillAction
{
    use QueueableAction;

<<<<<<< HEAD
    /**
     * @param list<array{amount?: float|int, notes?: string, item_ids?: list<int>}> $splits
     * @param list<string>|null $paymentMethods
     * @return list<Payment>
     */
=======
>>>>>>> laraxot/dev
    public function execute(
        int $orderId,
        string $splitType,
        array $splits,
        ?array $paymentMethods = null
    ): array {
        $order = Order::with(['items', 'table'])->findOrFail($orderId);

<<<<<<< HEAD
        if ($order->status === 'paid') {
            throw new \InvalidArgumentException('Order already paid');
        }

        $totalAmount = $order->total ?? $order->items->sum(static fn ($item): float => (float) $item->quantity * (float) $item->unit_price);
        $results = [];

        /** @var list<Payment> $result */
        $result = DB::transaction(function () use ($order, $splitType, $splits, $paymentMethods, $totalAmount, &$results): array {
=======
        if ($order->status === OrderStatusEnum::PAID) {
            throw new \InvalidArgumentException('Order already paid');
        }

        $totalAmount = $order->total_amount ?? $order->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $results = [];

        return DB::transaction(function () use ($order, $splitType, $splits, $paymentMethods, $totalAmount, &$results) {
>>>>>>> laraxot/dev
            switch ($splitType) {
                case 'equal':
                    $count = count($splits);
                    $amountPerPerson = round($totalAmount / $count, 2);
                    foreach ($splits as $index => $split) {
                        $payment = Payment::create([
                            'order_id' => $order->id,
                            'amount' => $amountPerPerson,
<<<<<<< HEAD
                            'method' => $paymentMethods[$index] ?? 'cash',
=======
                            'method' => $paymentMethods[$index] ?? PaymentMethodEnum::CASH,
>>>>>>> laraxot/dev
                            'notes' => $split['notes'] ?? ('Split '.($index + 1).'/'.$count),
                        ]);
                        $results[] = $payment;
                    }
                    break;

                case 'custom':
                    $sum = array_sum(array_column($splits, 'amount'));
                    if (abs($sum - $totalAmount) > 0.01) {
                        throw new \InvalidArgumentException('Split amounts do not match total');
                    }
                    foreach ($splits as $index => $split) {
                        $payment = Payment::create([
                            'order_id' => $order->id,
<<<<<<< HEAD
                            'amount' => $split['amount'] ?? 0,
                            'method' => $paymentMethods[$index] ?? 'cash',
=======
                            'amount' => $split['amount'],
                            'method' => $paymentMethods[$index] ?? PaymentMethodEnum::CASH,
>>>>>>> laraxot/dev
                            'notes' => $split['notes'] ?? ('Custom split '.($index + 1)),
                        ]);
                        $results[] = $payment;
                    }
                    break;

                case 'items':
                    foreach ($splits as $index => $split) {
                        $amount = 0;
<<<<<<< HEAD
                        foreach ($split['item_ids'] ?? [] as $itemId) {
=======
                        foreach ($split['item_ids'] as $itemId) {
>>>>>>> laraxot/dev
                            $item = $order->items->firstWhere('id', $itemId);
                            if ($item) {
                                $amount += $item->quantity * $item->unit_price;
                            }
                        }
                        $payment = Payment::create([
                            'order_id' => $order->id,
                            'amount' => round($amount, 2),
<<<<<<< HEAD
                            'method' => $paymentMethods[$index] ?? 'cash',
=======
                            'method' => $paymentMethods[$index] ?? PaymentMethodEnum::CASH,
>>>>>>> laraxot/dev
                            'notes' => $split['notes'] ?? ('Items split '.($index + 1)),
                        ]);
                        $results[] = $payment;
                    }
                    break;

                default:
                    throw new \InvalidArgumentException("Unknown split type: {$splitType}");
            }

            // Check if fully paid
            $paidTotal = $order->payments->sum('amount');
            if ($paidTotal >= $totalAmount - 0.01) {
<<<<<<< HEAD
                $order->update(['status' => 'paid']);
=======
                $order->update(['status' => OrderStatusEnum::PAID]);
>>>>>>> laraxot/dev
            }

            return $results;
        });
<<<<<<< HEAD

        return $result;
=======
>>>>>>> laraxot/dev
    }
}
