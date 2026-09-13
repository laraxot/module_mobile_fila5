<?php

declare(strict_types=1);

namespace Modules\Mobile\Database\Seeders;

use Modules\Mobile\Models\WaiterSession;
use Modules\Mobile\Models\OrderQueue;
<<<<<<< HEAD
use Modules\Mobile\Database\Factories\WaiterSessionFactory;
use Modules\Mobile\Database\Factories\OrderQueueFactory;
=======
>>>>>>> laraxot/dev
use Illuminate\Database\Seeder;

class MobileSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
        (new WaiterSessionFactory())->count(5)->create();

        (new OrderQueueFactory())->count(10)->create();
        (new OrderQueueFactory())->count(5)->pending()->create();
        (new OrderQueueFactory())->count(3)->synced()->create();
        (new OrderQueueFactory())->count(2)->failed()->create();
=======
        WaiterSession::factory(5)->create();

        OrderQueue::factory(10)->create();
        OrderQueue::factory(5)->pending()->create();
        OrderQueue::factory(3)->synced()->create();
        OrderQueue::factory(2)->failed()->create();
>>>>>>> laraxot/dev
    }
}