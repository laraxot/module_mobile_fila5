<?php

declare(strict_types=1);

use Modules\Mobile\Actions\Mobile\TakeOrderAction;
use Modules\Mobile\Contracts\RestaurantGateway;
use Modules\Mobile\Datas\CategoryData;
use Modules\Mobile\Datas\ProductData;
use Modules\Mobile\Datas\TableData;

final class TakeOrderFakeGateway implements RestaurantGateway
{
    public function __construct(private ?TableData $table = null)
    {
    }

    public function table(int|string $tableId): ?TableData
    {
        return $this->table;
    }

    public function product(int|string $productId): ?ProductData
    {
        return null;
    }

    public function category(int|string $categoryId): ?CategoryData
    {
        return null;
    }

    public function tables(?int $zoneId = null): array
    {
        return [];
    }

    public function categories(): array
    {
        return [];
    }

    public function createOrder(array $order): int|string
    {
        return 1;
    }
}

test('take order rejects a payload without session table or idempotency', function (): void {
    $action = new TakeOrderAction(new TakeOrderFakeGateway(new TableData(3, 'T3', 4, 'Sala', 'available')));

    expect(fn () => $action->execute([]))->toThrow(InvalidArgumentException::class)
        ->and(fn () => $action->execute(['waiter_session_id' => 's1']))->toThrow(InvalidArgumentException::class)
        ->and(fn () => $action->execute(['waiter_session_id' => 's1', 'table_id' => 3]))->toThrow(InvalidArgumentException::class)
        ->and(fn () => $action->execute([
            'waiter_session_id' => 's1',
            'table_id' => 3,
            'idempotency_key' => 'k1',
            'items' => [],
        ]))->toThrow(InvalidArgumentException::class);
});

test('take order rejects inactive or missing table', function (): void {
    $action = new TakeOrderAction(new TakeOrderFakeGateway(null));

    expect(fn () => $action->execute([
        'waiter_session_id' => 's1',
        'table_id' => 99,
        'idempotency_key' => 'k1',
        'items' => [['product_id' => 1, 'quantity' => 1]],
    ]))->toThrow(InvalidArgumentException::class);
});
