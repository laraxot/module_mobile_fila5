<?php

declare(strict_types=1);

namespace Modules\Mobile\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Restaurant\Models\DiningTable;
use Modules\Restaurant\Models\Order;

class MobileController extends Controller
{
    public function index()
    {
        return view('mobile::mobile.app');
    }

    public function tables()
    {
        $tables = DiningTable::query()->orderBy('floor')->orderBy('name')->get();

        return view('mobile::mobile.tables', compact('tables'));
    }

    public function order(DiningTable $table)
    {
        $orders = Order::query()->where('table_id', $table->id)->get();

        return view('mobile::mobile.order', compact('table', 'orders'));
    }

    public function submitOrder(Request $request, DiningTable $table)
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

    public function menu()
    {
        return view('mobile::mobile.menu');
    }

    public function sync()
    {
        return response()->json(['status' => 'synced']);
    }
}