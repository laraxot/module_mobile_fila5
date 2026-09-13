<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Illuminate\Support\Facades\Cache;
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

    public function execute(
        ?int $zoneId = null,
        ?string $waiterSessionId = null
    ): array {
        $cacheKey = "floor_plan_{$zoneId}_{$waiterSessionId}";

        return Cache::remember($cacheKey, 5, function () use ($zoneId, $waiterSessionId) {
            $tablesQuery = DiningTable::with(['zone', 'currentOrder', 'assignedWaiter'])
                ->when($zoneId, fn($q) => $q->where('zone_id', $zoneId))
                ->get();

            $zones = Zone::with('tables')->get();

            $tables = $tablesQuery->map(function ($table) use ($waiterSessionId) {
                $order = $table->currentOrder;
                $isAssignedToMe = $waiterSessionId && $table->assigned_waiter_session_id === $waiterSessionId;

                return [
                    'id' => $table->id,
                    'number' => $table->number,
                    'name' => $table->name,
                    'zone_id' => $table->zone_id,
                    'zone_name' => $table->zone?->name,
                    'capacity' => $table->capacity,
                    'position_x' => $table->position_x,
                    'position_y' => $table->position_y,
                    'shape' => $table->shape ?? 'rect',
                    'status' => $this->getTableStatus($table),
                    'order' => $order ? [
                        'id' => $order->id,
                        'status' => $order->status->value,
                        'item_count' => $order->items_count ?? 0,
                        'total' => $order->total_amount ?? 0,
                        'created_at' => $order->created_at?->toISOString(),
                    ] : null,
                    'is_assigned_to_me' => $isAssignedToMe,
                    'assigned_waiter' => $table->assignedWaiter?->name,
                ];
            });

            return [
                'tables' => $tables->values(),
                'zones' => $zones->map(fn($z) => [
                    'id' => $z->id,
                    'name' => $z->name,
                    'color' => $z->color,
                    'tables_count' => $z->tables_count ?? 0,
                ]),
                'waiter_location' => $waiterSessionId ? $this->getWaiterLocation($waiterSessionId) : null,
                'timestamp' => now()->toISOString(),
            ];
        });
    }

    private function getTableStatus(DiningTable $table): string
    {
        if ($table->currentOrder) {
            return match ($table->currentOrder->status->value) {
                'pending' => 'occupied',
                'preparing' => 'preparing',
                'ready' => 'ready',
                'served' => 'served',
                'paid' => 'available',
                default => 'available',
            };
        }

        return $table->is_available ? 'available' : 'reserved';
    }

    private function getWaiterLocation(string $waiterSessionId): ?array
    {
        $session = \Modules\Mobile\Models\WaiterSession::find($waiterSessionId);
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