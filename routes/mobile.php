<?php

use Modules\Mobile\Actions\Mobile\TakeOrderAction;
use Modules\Mobile\Actions\Mobile\SplitBillAction;
use Modules\Mobile\Actions\Mobile\ViewFloorPlanAction;
use Modules\Mobile\Actions\Mobile\KdsNotificationAction;
use Modules\Mobile\Actions\Mobile\ScanQrAction;
use Modules\Mobile\Actions\Mobile\SyncOfflineOrdersAction;
use Modules\Mobile\Models\WaiterSession;
use Modules\Restaurant\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile API Routes
|--------------------------------------------------------------------------
|
| These routes are for the NativePHP mobile app to communicate with the
| Laravel backend via API endpoints. Actions are called directly — no
| HTTP controllers (per project rules: Folio pages + Actions).
|
*/

Route::prefix('api/mobile')->middleware(['auth:sanctum'])->group(function () {
    // Floor plan and table management
    Route::get('floor-plan', function (ViewFloorPlanAction $action, Request $request) {
        return response()->json($action->execute(
            $request->input('zone_id'),
            $request->header('X-Waiter-Session')
        ));
    });

    Route::get('tables/{table}', function ($table) {
        $t = \Modules\Restaurant\Models\DiningTable::with('currentOrder')->findOrFail($table);
        return response()->json([
            'success' => true,
            'data' => $t,
        ]);
    });

    // Order taking
    Route::post('orders/take', function (TakeOrderAction $action, Request $request) {
        $result = $action->execute(
            $request->input('waiter_session_id'),
            $request->input('table_id'),
            $request->input('items', []),
            $request->input('notes'),
            $request->input('shift_id')
        );
        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    });

    // Split bill
    Route::post('orders/split', function (SplitBillAction $action, Request $request) {
        $result = $action->execute(
            $request->input('order_id'),
            $request->input('split_type'),
            $request->input('splits', []),
            $request->input('payment_methods')
        );
        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    });

    Route::get('orders/current', function (Request $request) {
        $orders = Order::with(['table', 'items.product'])
            ->where('user_id', auth()->id())
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->latest()
            ->get();
        return response()->json(['success' => true, 'data' => $orders]);
    });

    // QR scanning
    Route::post('scan-qr', function (ScanQrAction $action, Request $request) {
        $result = $action->execute(
            $request->input('qr_code'),
            $request->header('X-Waiter-Session')
        );
        return response()->json($result);
    });

    // Session management
    Route::post('session/start', function (Request $request) {
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
    });

    Route::post('session/end', function (Request $request) {
        $session = WaiterSession::where('device_id', $request->input('device_id'))->first();
        if ($session) {
            $session->update(['is_active' => false]);
        }
        return response()->json(['success' => true]);
    });

    Route::post('session/heartbeat', function (Request $request) {
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
    });

    // KDS integration
    Route::post('kds/notify/{order}', function (KdsNotificationAction $action, $order, Request $request) {
        $action->execute($order, $request->boolean('is_new_order', true));
        return response()->json(['success' => true, 'message' => 'KDS notification sent']);
    });

    // Offline queue sync
    Route::post('queue/sync', function (SyncOfflineOrdersAction $action, Request $request) {
        $result = $action->execute($request->input('waiter_session_id'));
        return response()->json($result);
    });

    Route::get('queue/pending', function (Request $request) {
        $queue = \Modules\Mobile\Models\OrderQueue::where('waiter_session_id', $request->input('waiter_session_id'))
            ->where('status', \Modules\Mobile\Models\OrderQueue::STATUS_PENDING)
            ->latest()
            ->get();
        return response()->json(['success' => true, 'data' => $queue]);
    });
});

// Public routes for deep linking
Route::prefix('mobile')->group(function () {
    Route::get('table/{table}', function ($table) {
        return response()->json([
            'success' => true,
            'data' => \Modules\Restaurant\Models\DiningTable::findOrFail($table),
        ]);
    })->where('table', '[0-9]+');

    Route::get('menu/{item}', function ($item) {
        return response()->json([
            'success' => true,
            'data' => \Modules\Restaurant\Models\Product::findOrFail($item),
        ]);
    })->where('item', '[0-9]+');
});