<?php

declare(strict_types=1);

namespace Modules\Mobile\Database\Factories;

use Modules\Mobile\Models\OrderQueue;
<<<<<<< HEAD
use Modules\Mobile\Database\Factories\WaiterSessionFactory;
use Modules\Restaurant\Database\Factories\DiningTableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OrderQueue> */
=======
use Illuminate\Database\Eloquent\Factories\Factory;

>>>>>>> laraxot/dev
class OrderQueueFactory extends Factory
{
    protected $model = OrderQueue::class;

    public function definition(): array
    {
        return [
<<<<<<< HEAD
            'waiter_session_id' => static function (): string {
                return (new WaiterSessionFactory())->createOne()->id;
            },
            'table_id' => static function (): int {
                return (new DiningTableFactory())->createOne()->id;
=======
            'waiter_session_id' => function () {
                return \Modules\Mobile\Models\WaiterSession::factory()->create()->id;
            },
            'table_id' => function () {
                return \Modules\Restaurant\Models\DiningTable::factory()->create()->id;
>>>>>>> laraxot/dev
            },
            'order_data' => [
                'items' => [
                    [
                        'product_id' => 1,
                        'product_name' => 'Pizza Margherita',
                        'quantity' => 2,
                        'unit_price' => 12.5,
                        'notes' => null,
                    ],
                ],
                'total' => 25.0,
            ],
            'status' => OrderQueue::STATUS_PENDING,
            'sync_attempts' => 0,
            'last_sync_at' => null,
            'error_message' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderQueue::STATUS_PENDING,
        ]);
    }

    public function synced(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderQueue::STATUS_SYNCED,
            'sync_attempts' => 1,
            'last_sync_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderQueue::STATUS_FAILED,
            'sync_attempts' => 3,
            'error_message' => 'Connection timeout',
        ]);
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> laraxot/dev
