<?php

declare(strict_types=1);

namespace Modules\Mobile\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Restaurant\Models\Order;
use Modules\Restaurant\Models\DiningTable;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
        $table = DiningTable::firstOrCreate(['name' => 'Tavolo 1'], ['zone_id' => 1, 'status' => 'available', 'is_active' => true]);
=======
        $table = DiningTable::firstWhere('code', 'TAV-001');
>>>>>>> laraxot/dev

        Order::query()->create([
            'table_id' => $table->id,
            'status' => 'open',
            'waiter_id' => 1,
            'total' => 0,
        ]);

        Order::query()->create([
            'table_id' => $table->id,
            'status' => 'sent_to_kitchen',
            'waiter_id' => 1,
            'total' => 45.50,
        ]);
    }
}