<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Modules\Mobile\Contracts\RestaurantGateway;
use Modules\Mobile\Data\TableData;
use Spatie\QueueableAction\QueueableAction;

/**
 * Action for viewing floor plan of restaurant.
 * Displays tables, zones, and seating arrangement.
 *
 * Le tavole arrivano dal port `RestaurantGateway`: il modulo Mobile non deve
 * conoscere i modelli del dominio Restaurant (story 1.11 mobile-module-boundary).
 */
class ViewFloorPlanAction
{
    use QueueableAction;

    public function __construct(
        private readonly RestaurantGateway $restaurantGateway,
    ) {
    }

    /**
     * @return array{success: bool, type: string, data: array{zone_id: int|null, waiter_session_id: string|null, capacity: int, tables: list<array<string, mixed>>}}
     */
    public function execute(?int $zoneId = null, ?string $waiterSessionId = null): array
    {
        $tables = $this->restaurantGateway->tables($zoneId);

        return [
            'success' => true,
            'type' => 'floor_plan',
            'data' => [
                'zone_id' => $zoneId,
                'waiter_session_id' => $waiterSessionId,
                'capacity' => array_sum(array_map(
                    static fn (TableData $table): int => $table->capacity,
                    $tables
                )),
                'tables' => array_map(static fn (TableData $table): array => [
                    'id' => $table->tableId,
                    'name' => $table->name,
                    'seats' => $table->capacity,
                    'status' => $table->status,
                    'zone' => $table->zone,
                ], $tables),
            ],
        ];
    }
}
