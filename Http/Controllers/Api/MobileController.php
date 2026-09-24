<?php

declare(strict_types=1);

namespace Modules\Mobile\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Mobile\Actions\Mobile\TakeOrderAction;
use Modules\Mobile\Actions\Mobile\SplitBillAction;
use Modules\Mobile\Actions\Mobile\ViewFloorPlanAction;
use Modules\Mobile\Actions\Mobile\KdsNotificationAction;
use Modules\Mobile\Actions\Mobile\ScanQrAction;
use Modules\Mobile\Actions\Mobile\SyncOfflineOrdersAction;
use Modules\Mobile\Models\WaiterSession;

class MobileController extends Controller
{
    public function __construct(
        private readonly TakeOrderAction $takeOrderAction,
        private readonly SplitBillAction $splitBillAction,
        private readonly ViewFloorPlanAction $viewFloorPlanAction,
        private readonly KdsNotificationAction $kdsNotificationAction,
        private readonly ScanQrAction $scanQrAction,
        private readonly SyncOfflineOrdersAction $syncOfflineOrdersAction,
    ) {
    }

    public function getFloorPlan(Request $request): JsonResponse
    {
        $zoneId = $request->integer('zone_id') ?: null;
        $result = $this->viewFloorPlanAction->execute(
            $zoneId,
            $request->header('X-Waiter-Session')
        );

        return response()->json($result);
    }

    public function takeOrder(Request $request): JsonResponse
    {
        $result = $this->takeOrderAction->execute(
            $request->string('waiter_session_id')->toString(),
            $request->integer('table_id'),
            $this->normalizeItems($request->array('items')),
            $this->normalizeNotes($request->array('notes')) ?: null,
            $request->has('shift_id') ? $request->string('shift_id')->toString() : null
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function splitBill(Request $request): JsonResponse
    {
        $result = $this->splitBillAction->execute(
            $request->integer('order_id'),
            $request->string('split_type')->toString(),
            $this->normalizeSplits($request->array('splits')),
            array_values(array_map(static fn (mixed $value): string => is_scalar($value) ? (string) $value : '', $request->array('payment_methods'))) ?: null
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * @param array<int|string, mixed> $notes
     * @return array<string, mixed>
     */
    private function normalizeNotes(array $notes): array
    {
        $result = [];
        foreach ($notes as $key => $value) {
            if (is_scalar($value)) {
                $result[(string) $key] = $value;
            }
        }
        return $result;
    }

    /** @param array<int|string, mixed> $items
     * @return list<array{product_id: int, quantity: int|float, unit_price: int|float, notes?: string|null, modifiers?: array<mixed>}> */
    private function normalizeItems(array $items): array
    {
        return array_values(array_filter(array_map(static function (mixed $item): ?array {
            if (!is_array($item)) {
                return null;
            }

            return [
                'product_id' => is_numeric($item['product_id'] ?? null) ? (int) $item['product_id'] : 0,
                'quantity' => is_numeric($item['quantity'] ?? null) ? (float) $item['quantity'] : 1.0,
                'unit_price' => is_numeric($item['unit_price'] ?? null) ? (float) $item['unit_price'] : 0.0,
                'notes' => is_scalar($item['notes'] ?? null) ? (string) $item['notes'] : null,
                'modifiers' => is_array($item['modifiers'] ?? null) ? $item['modifiers'] : [],
            ];
        }, $items)));
    }

    /** @param array<int|string, mixed> $splits
     * @return list<array{amount?: float|int, notes?: string, item_ids?: list<int>}> */
    private function normalizeSplits(array $splits): array
    {
        return array_values(array_filter(array_map(static function (mixed $split): ?array {
            if (!is_array($split)) {
                return null;
            }

            $itemIds = $split['item_ids'] ?? null;
            return [
                ...(is_numeric($split['amount'] ?? null) ? ['amount' => (float) $split['amount']] : []),
                ...(is_scalar($split['notes'] ?? null) ? ['notes' => (string) $split['notes']] : []),
                ...(is_array($itemIds) ? ['item_ids' => array_values(array_map(static fn (mixed $id): int => is_numeric($id) ? (int) $id : 0, $itemIds))] : []),
            ];
        }, $splits)));
    }

    public function scanQr(Request $request): JsonResponse
    {
        $result = $this->scanQrAction->execute(
            $request->string('qr_code')->toString(),
            $request->header('X-Waiter-Session')
        );

        return response()->json($result);
    }

    public function notifyKds(Request $request, int $order): JsonResponse
    {
        $this->kdsNotificationAction->execute($order, $request->boolean('is_new_order', true));

        return response()->json([
            'success' => true,
            'message' => 'KDS notification sent',
        ]);
    }

    public function syncQueue(Request $request): JsonResponse
    {
        $result = $this->syncOfflineOrdersAction->execute(
            $request->string('waiter_session_id')->toString()
        );

        return response()->json($result);
    }

    public function startSession(Request $request): JsonResponse
    {
        $session = WaiterSession::create([
            'user_id' => auth()->id(),
            'device_id' => $request->input('device_id'),
            'device_name' => $request->input('device_name'),
            'platform' => $request->input('platform'),
            'token' => $request->input('token'),
        ]);

        return response()->json([
            'success' => true,
            'session' => $session,
            'token' => $session->token,
        ]);
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $session = WaiterSession::where('device_id', $request->input('device_id'))->first();

        if ($session) {
            $session->update([
                'last_active_at' => now(),
                'location_lat' => $request->input('location_lat'),
                'location_lng' => $request->input('location_lng'),
            ]);
        }

        return response()->json([
            'success' => true,
            'session_id' => $session?->id,
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function getCurrentOrders(Request $request): JsonResponse
    {
        $orders = \Modules\Restaurant\Models\Order::with(['table', 'items.product'])
            ->where('user_id', auth()->id())
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function getPendingQueue(Request $request): JsonResponse
    {
        $queue = \Modules\Mobile\Models\OrderQueue::where('waiter_session_id', $request->input('waiter_session_id'))
            ->where('status', 'pending')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $queue,
        ]);
    }
}
