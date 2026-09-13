<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Illuminate\Support\Facades\Cache;
use Modules\Mobile\Models\WaiterSession;
use Modules\Restaurant\Models\DiningTable;
use Modules\Restaurant\Models\Zone;
use Spatie\QueueableAction\QueueableAction;

/**
 * Action for viewing real-time floor plan on mobile device.
 * Returns table statuses, zones, and waiter assignments.
 */
class ViewFloorPlanAction
{
    use QueueableAction;

    /** @return array<string, mixed> */
    public function execute(
        ?int $zoneId = null,
        ?string $waiterSessionId = null
    ): array {
        $cacheKey = "floor_plan_{$zoneId}_{$waiterSessionId}";

        /** @var array<string, mixed> $floorPlan */
        $floorPlan = Cache::remember($cacheKey, 5, function () use ($zoneId, $waiterSessionId): array {
            $tablesQuery = DiningTable::with('zone')
                ->when($zoneId, fn($q) => $q->where('zone_id', $zoneId))
                ->get();

            $zones = Zone::with('tables')->get();

            $tables = $tablesQuery->map(function (DiningTable $table): array {
                $order = $table->orders()->latest('id')->first();
                $isAssignedToMe = false;

                return [
                    'id' => $table->id,
                    'number' => $table->name,
                    'name' => $table->name,
                    'zone_id' => $table->zone_id,
                    'zone_name' => $table->zone?->name,
                    'capacity' => $table->seats,
                    'position_x' => $table->pos_x,
                    'position_y' => $table->pos_y,
                    'shape' => $table->shape ?? 'rect',
                    'status' => $this->getTableStatus($table),
                    'order' => $order ? [
                        'id' => $order->id,
                        'status' => $order->status,
                        'item_count' => $order->items()->count(),
                        'total' => $order->total ?? 0,
                        'created_at' => $order->created_at?->toISOString(),
                    ] : null,
                    'is_assigned_to_me' => $isAssignedToMe,
                    'assigned_waiter' => null,
                ];
            });

            return [
                'tables' => $tables->values(),
                'zones' => $zones->map(static fn (Zone $z): array => [
                    'id' => $z->id,
                    'name' => $z->name,
                    'color' => null,
                    'tables_count' => $z->tables()->count(),
                ]),
                'waiter_location' => $waiterSessionId ? $this->getWaiterLocation($waiterSessionId) : null,
                'timestamp' => now()->toISOString(),
            ];
        });

        return $floorPlan;
    }

    private function getTableStatus(DiningTable $table): string
    {
        $order = $table->orders()->latest('id')->first();
        if ($order) {
            return match ($order->status) {
                'pending' => 'occupied',
                'preparing' => 'preparing',
                'ready' => 'ready',
                'served' => 'served',
                'paid' => 'available',
                default => 'available',
            };
        }

        return $table->is_active ? 'available' : 'reserved';
    }

    /** @return array{lat: float, lng: float, updated_at: string|null}|null */
    private function getWaiterLocation(string $waiterSessionId): ?array
    {
        $session = WaiterSession::find($waiterSessionId);
        if ($session && $session->location_lat && $session->location_lng) {
            return [
                'lat' => $session->location_lat,
                'lng' => $session->location_lng,
                'updated_at' => $session->last_active_at?->toISOString(),
            ];
        }

        return null;
    }
}
