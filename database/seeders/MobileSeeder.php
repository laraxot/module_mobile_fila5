<?php

declare(strict_types=1);

namespace Modules\Mobile\Database\Seeders;

use Modules\Mobile\Models\WaiterSession;
use Modules\Mobile\Models\OrderQueue;
use Illuminate\Database\Seeder;

class MobileSeeder extends Seeder
{
    public function run(): void
    {
        WaiterSession::factory(5)->create();

        OrderQueue::factory(10)->create();
        OrderQueue::factory(5)->pending()->create();
        OrderQueue::factory(3)->synced()->create();
        OrderQueue::factory(2)->failed()->create();
    }
}