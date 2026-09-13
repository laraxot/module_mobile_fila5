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
            $request->array('items'),
            $request->array('notes') ?: null,
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
            $request->array('splits'),
            $request->array('payment_methods') ?: null
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
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
