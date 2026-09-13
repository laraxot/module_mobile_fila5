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

/** @return list<array{product_id: int, quantity: float|int, unit_price: float|int, notes?: string|null, modifiers?: array<mixed>}> */
$normalizeItems = static function (array $items): array {
    $normalized = [];
    foreach ($items as $item) {
        if (! is_array($item) || ! isset($item['product_id'], $item['quantity'], $item['unit_price'])) {
            continue;
        }
        if (! is_int($item['product_id']) || (! is_int($item['quantity']) && ! is_float($item['quantity'])) || (! is_int($item['unit_price']) && ! is_float($item['unit_price']))) {
            continue;
        }
        $normalized[] = [
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'unit_price' => $item['unit_price'],
            'notes' => is_string($item['notes'] ?? null) ? $item['notes'] : null,
            'modifiers' => is_array($item['modifiers'] ?? null) ? $item['modifiers'] : [],
        ];
    }

    return $normalized;
};

/** @return list<array{amount?: float|int, notes?: string, item_ids?: list<int>}> */
$normalizeSplits = static function (array $splits): array {
    $normalized = [];
    foreach ($splits as $split) {
        if (! is_array($split)) {
            continue;
        }
        $itemIds = [];
        foreach (is_array($split['item_ids'] ?? null) ? $split['item_ids'] : [] as $itemId) {
            if (is_int($itemId)) {
                $itemIds[] = $itemId;
            }
        }
        $amount = $split['amount'] ?? null;
        $entry = ['notes' => is_string($split['notes'] ?? null) ? $split['notes'] : ''];
        if (is_int($amount) || is_float($amount)) {
            $entry['amount'] = $amount;
        }
        if ($itemIds !== []) {
            $entry['item_ids'] = $itemIds;
        }
        $normalized[] = $entry;
    }

    return $normalized;
};

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

Route::prefix('api/mobile')->middleware(['auth:sanctum'])->group(function () use ($normalizeItems, $normalizeSplits) {
    // Floor plan and table management
    Route::get('floor-plan', function (ViewFloorPlanAction $action, Request $request) {
        return response()->json($action->execute(
            $request->integer('zone_id') ?: null,
            $request->header('X-Waiter-Session')
        ));
    });

    Route::get('tables/{table}', function (int $table) {
        $t = \Modules\Restaurant\Models\DiningTable::with('orders')->findOrFail($table);
        return response()->json([
            'success' => true,
            'data' => $t,
        ]);
    });

    // Order taking
    Route::post('orders/take', function (TakeOrderAction $action, Request $request) use ($normalizeItems) {
        $result = $action->execute(
            $request->string('waiter_session_id')->toString(),
            $request->integer('table_id'),
            $normalizeItems($request->array('items')),
            null,
            $request->string('shift_id')->toString() ?: null
        );
        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    });

    // Split bill
    Route::post('orders/split', function (SplitBillAction $action, Request $request) use ($normalizeSplits) {
        $result = $action->execute(
            $request->integer('order_id'),
            $request->string('split_type')->toString(),
            $normalizeSplits($request->array('splits')),
            array_values(array_filter($request->array('payment_methods'), 'is_string')) ?: null
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
            $request->string('qr_code')->toString(),
            $request->header('X-Waiter-Session')
        );
        return response()->json($result);
    });

    // Session management
    Route::post('session/start', function (Request $request) {
        $session = WaiterSession::create([
            'user_id' => auth()->id(),
            'device_id' => $request->string('device_id')->toString(),
            'device_name' => $request->string('device_name')->toString(),
            'platform' => $request->string('platform')->toString(),
            'token' => $request->string('token')->toString(),
        ]);
        return response()->json([
            'success' => true,
            'session' => $session,
            'token' => $session->token,
        ]);
    });

    Route::post('session/end', function (Request $request) {
        $session = WaiterSession::where('device_id', $request->string('device_id')->toString())->first();
        if ($session) {
            $session->update(['is_active' => false]);
        }
        return response()->json(['success' => true]);
    });

    Route::post('session/heartbeat', function (Request $request) {
        $session = WaiterSession::where('device_id', $request->string('device_id')->toString())->first();
        if ($session) {
            $session->update([
                'last_active_at' => now(),
                'location_lat' => $request->float('location_lat'),
                'location_lng' => $request->float('location_lng'),
            ]);
        }
        return response()->json([
            'success' => true,
            'session_id' => $session?->id,
            'timestamp' => now()->toISOString(),
        ]);
    });

    // KDS integration
    Route::post('kds/notify/{order}', function (KdsNotificationAction $action, int $order, Request $request) {
        $action->execute($order, $request->boolean('is_new_order', true));
        return response()->json(['success' => true, 'message' => 'KDS notification sent']);
    });

    // Offline queue sync
    Route::post('queue/sync', function (SyncOfflineOrdersAction $action, Request $request) {
        $result = $action->execute($request->string('waiter_session_id')->toString());
        return response()->json($result);
    });

    Route::get('queue/pending', function (Request $request) {
        $queue = \Modules\Mobile\Models\OrderQueue::where('waiter_session_id', $request->string('waiter_session_id')->toString())
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
