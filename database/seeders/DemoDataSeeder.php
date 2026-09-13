<?php

declare(strict_types=1);

namespace Modules\Mobile\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Restaurant\Models\DiningTable;
use Modules\Restaurant\Models\Order;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        DiningTable::query()->delete();
        Order::query()->delete();

        DiningTable::query()->create([
            'name' => 'Tavolo 1',
            'code' => 'TAV-001',
            'capacity' => 4,
            'floor' => 1,
            'status' => 'available',
        ]);

        DiningTable::query()->create([
            'name' => 'Tavolo 2',
            'code' => 'TAV-002',
            'capacity' => 4,
            'floor' => 1,
            'status' => 'available',
        ]);

        DiningTable::query()->create([
            'name' => 'Tavolo 3',
            'code' => 'TAV-003',
            'capacity' => 6,
            'floor' => 1,
            'status' => 'reserved',
        ]);

        Order::query()->create([
<<<<<<< HEAD
            'table_id' => DiningTable::firstOrCreate(['name' => 'Tavolo 1'], ['zone_id' => 1, 'status' => 'available', 'is_active' => true])->id,
=======
            'table_id' => DiningTable::firstWhere('code', 'TAV-001')->id,
>>>>>>> laraxot/dev
            'status' => 'open',
            'total' => 0,
        ]);
    }
}
