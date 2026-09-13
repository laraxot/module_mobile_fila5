<?php

declare(strict_types=1);

namespace Modules\Mobile\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Modules\Restaurant\Models\DiningTable;
use Modules\Restaurant\Models\Order;

class MobileController extends Controller
{
    public function index(): View
    {
        /** @phpstan-var view-string $view */
        $view = 'mobile::mobile.app';

        return view($view);
    }

    public function tables(): View
    {
        $tables = DiningTable::query()->orderBy('floor')->orderBy('name')->get();
        /** @phpstan-var view-string $view */
        $view = 'mobile::mobile.tables';

        return view($view, compact('tables'));
    }

    public function order(DiningTable $table): View
    {
        $orders = Order::query()->where('table_id', $table->id)->get();
        /** @phpstan-var view-string $view */
        $view = 'mobile::mobile.order';

        return view($view, compact('table', 'orders'));
    }

    public function submitOrder(Request $request, DiningTable $table): RedirectResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::query()->create([
            'table_id' => $table->id,
            'status' => 'open',
            'waiter_id' => auth()->id(),
            'total' => 0,
        ]);

        return redirect()->route('mobile.order', $table);
    }

    public function menu(): View
    {
        /** @phpstan-var view-string $view */
        $view = 'mobile::mobile.menu';

        return view($view);
    }

    public function sync(): JsonResponse
    {
        return response()->json(['status' => 'synced']);
    }
}
