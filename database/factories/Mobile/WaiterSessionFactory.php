<?php

declare(strict_types=1);

namespace Modules\Mobile\Database\Factories;

use Modules\Mobile\Models\WaiterSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class WaiterSessionFactory extends Factory
{
    protected $model = WaiterSession::class;

    public function definition(): array
    {
        return [
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            'device_id' => $this->faker->unique()->uuid,
            'device_name' => $this->faker->words(2, true),
            'platform' => $this->faker->randomElement(['ios', 'android']),
            'token' => $this->faker->sha256,
            'biometric_enabled' => $this->faker->boolean(70),
            'is_active' => true,
            'location_lat' => $this->faker->latitude,
            'location_lng' => $this->faker->longitude,
            'shift_id' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function ios(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'ios',
        ]);
    }

    public function android(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'android',
        ]);
    }
}