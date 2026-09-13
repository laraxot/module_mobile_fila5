<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Illuminate\Support\Facades\Cache;
<<<<<<< HEAD
use Modules\Mobile\Models\WaiterSession;
use Modules\Restaurant\Models\DiningTable;
use Modules\Restaurant\Models\Zone;
=======
// RIMOSSO: dipendenza proibita
// RIMOSSO: dipendenza proibita
>>>>>>> laraxot/dev
use Spatie\QueueableAction\QueueableAction;

/**
 * Action for viewing real-time floor plan on mobile device.
 * Returns table statuses, zones, and waiter assignments.
 */
class ViewFloorPlanAction
{
    use QueueableAction;

<<<<<<< HEAD
    /** @return array<string, mixed> */
=======
>>>>>>> laraxot/dev
    public function execute(
        ?int $zoneId = null,
        ?string $waiterSessionId = null
    ): array {
        $cacheKey = "floor_plan_{$zoneId}_{$waiterSessionId}";

<<<<<<< HEAD
        /** @var array<string, mixed> $floorPlan */
        $floorPlan = Cache::remember($cacheKey, 5, function () use ($zoneId, $waiterSessionId): array {
            $tablesQuery = DiningTable::with('zone')
=======
        return Cache::remember($cacheKey, 5, function () use ($zoneId, $waiterSessionId) {
            $tablesQuery = DiningTable::with(['zone', 'currentOrder', 'assignedWaiter'])
>>>>>>> laraxot/dev
                ->when($zoneId, fn($q) => $q->where('zone_id', $zoneId))
                ->get();

            $zones = Zone::with('tables')->get();

<<<<<<< HEAD
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
=======
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
>>>>>>> laraxot/dev
                    'shape' => $table->shape ?? 'rect',
                    'status' => $this->getTableStatus($table),
                    'order' => $order ? [
                        'id' => $order->id,
<<<<<<< HEAD
                        'status' => $order->status,
                        'item_count' => $order->items()->count(),
                        'total' => $order->total ?? 0,
                        'created_at' => $order->created_at?->toISOString(),
                    ] : null,
                    'is_assigned_to_me' => $isAssignedToMe,
                    'assigned_waiter' => null,
=======
                        'status' => $order->status->value,
                        'item_count' => $order->items_count ?? 0,
                        'total' => $order->total_amount ?? 0,
                        'created_at' => $order->created_at?->toISOString(),
                    ] : null,
                    'is_assigned_to_me' => $isAssignedToMe,
                    'assigned_waiter' => $table->assignedWaiter?->name,
>>>>>>> laraxot/dev
                ];
            });

            return [
                'tables' => $tables->values(),
<<<<<<< HEAD
                'zones' => $zones->map(static fn (Zone $z): array => [
                    'id' => $z->id,
                    'name' => $z->name,
                    'color' => null,
                    'tables_count' => $z->tables()->count(),
=======
                'zones' => $zones->map(fn($z) => [
                    'id' => $z->id,
                    'name' => $z->name,
                    'color' => $z->color,
                    'tables_count' => $z->tables_count ?? 0,
>>>>>>> laraxot/dev
                ]),
                'waiter_location' => $waiterSessionId ? $this->getWaiterLocation($waiterSessionId) : null,
                'timestamp' => now()->toISOString(),
            ];
        });
<<<<<<< HEAD

        return $floorPlan;
=======
>>>>>>> laraxot/dev
    }

    private function getTableStatus(DiningTable $table): string
    {
<<<<<<< HEAD
        $order = $table->orders()->latest('id')->first();
        if ($order) {
            return match ($order->status) {
=======
        if ($table->currentOrder) {
            return match ($table->currentOrder->status->value) {
>>>>>>> laraxot/dev
                'pending' => 'occupied',
                'preparing' => 'preparing',
                'ready' => 'ready',
                'served' => 'served',
                'paid' => 'available',
                default => 'available',
            };
        }

<<<<<<< HEAD
        return $table->is_active ? 'available' : 'reserved';
    }

    /** @return array{lat: float, lng: float, updated_at: string|null}|null */
    private function getWaiterLocation(string $waiterSessionId): ?array
    {
        $session = WaiterSession::find($waiterSessionId);
=======
        return $table->is_available ? 'available' : 'reserved';
    }

    private function getWaiterLocation(string $waiterSessionId): ?array
    {
        $session = \Modules\Mobile\Models\WaiterSession::find($waiterSessionId);
>>>>>>> laraxot/dev
        if ($session && $session->location_lat && $session->location_lng) {
            return [
                'lat' => $session->location_lat,
                'lng' => $session->location_lng,
                'updated_at' => $session->last_active_at?->toISOString(),
            ];
        }

        return null;
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> laraxot/dev
