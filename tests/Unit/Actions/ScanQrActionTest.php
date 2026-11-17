<?php

declare(strict_types=1);

use Modules\Mobile\Actions\Mobile\ScanQrAction;
use Modules\Mobile\Contracts\RestaurantGateway;
use Modules\Mobile\Datas\CategoryData;
use Modules\Mobile\Datas\ProductData;
use Modules\Mobile\Datas\TableData;

/**
 * Gateway di test: restituisce solo i record "attivi" registrati in memoria.
 * Nessun database: soddisfa AC #4 della story 1.1.
 */
final class FakeRestaurantGateway implements RestaurantGateway
{
    /**
     * Chiavi = id del record; PHP converte gli id numerici in chiavi int.
     *
     * @param  array<array-key, TableData>  $tables
     * @param  array<array-key, ProductData>  $products
     * @param  array<array-key, CategoryData>  $categories
     */
    public function __construct(
        private array $tables = [],
        private array $products = [],
        private array $categories = [],
    ) {}

    public function table(int|string $tableId): ?TableData
    {
        return $this->tables[(string) $tableId] ?? null;
    }

    public function product(int|string $productId): ?ProductData
    {
        return $this->products[(string) $productId] ?? null;
    }

    public function category(int|string $categoryId): ?CategoryData
    {
        return $this->categories[(string) $categoryId] ?? null;
    }

    public function tables(?int $zoneId = null): array
    {
        return array_values($this->tables);
    }

    public function categories(): array
    {
        return array_values($this->categories);
    }

    public function createOrder(array $order): int
    {
        return 0;
    }
}

test('scan table qr resolves an active table', function (): void {
    $table = new TableData(4, 'Tavolo 4', 4, 'Sala', 'available');
    $gateway = new FakeRestaurantGateway(tables: ['4' => $table]);
    $action = new ScanQrAction($gateway);

    $result = $action->execute('table:4');

    expect($result['type'])->toBe('table')
        ->and($result['table'])->toBe($table)
        ->and($result['product'])->toBeNull()
        ->and($result['category'])->toBeNull()
        ->and($result['raw'])->toBe('table:4');
});

test('scan numeric payload is treated as table id', function (): void {
    $table = new TableData(12, 'T12', 2, 'Terrazza', 'open');
    $gateway = new FakeRestaurantGateway(tables: ['12' => $table]);
    $action = new ScanQrAction($gateway);

    $result = $action->execute('12');

    expect($result['type'])->toBe('table')
        ->and($result['table']?->tableId)->toBe(12);
});

test('scan product qr resolves an active product', function (): void {
    $product = new ProductData(7, 'Pizza Margherita', 8.5, allergens: ['gluten']);
    $gateway = new FakeRestaurantGateway(products: ['7' => $product]);
    $action = new ScanQrAction($gateway);

    $result = $action->execute('product:7');

    expect($result['type'])->toBe('product')
        ->and($result['product'])->toBe($product)
        ->and($result['table'])->toBeNull();
});

test('scan category qr resolves an active category', function (): void {
    $category = new CategoryData(2, 'Pizze', []);
    $gateway = new FakeRestaurantGateway(categories: ['2' => $category]);
    $action = new ScanQrAction($gateway);

    $result = $action->execute('category:2');

    expect($result['type'])->toBe('category')
        ->and($result['category'])->toBe($category);
});

test('missing or inactive table raises a safe error', function (): void {
    $inactive = new TableData(9, 'T9', 2, 'Sala', 'inactive');
    $gateway = new FakeRestaurantGateway(tables: ['9' => $inactive]);
    $action = new ScanQrAction($gateway);

    expect(fn () => $action->execute('table:9'))->toThrow(InvalidArgumentException::class)
        ->and(fn () => $action->execute('table:999'))->toThrow(InvalidArgumentException::class);
});

test('missing product or category raises a safe error', function (): void {
    $action = new ScanQrAction(new FakeRestaurantGateway);

    expect(fn () => $action->execute('product:1'))->toThrow(InvalidArgumentException::class)
        ->and(fn () => $action->execute('category:1'))->toThrow(InvalidArgumentException::class);
});

test('empty or unknown qr format is rejected', function (): void {
    $action = new ScanQrAction(new FakeRestaurantGateway);

    expect(fn () => $action->execute(''))->toThrow(InvalidArgumentException::class)
        ->and(fn () => $action->execute('   '))->toThrow(InvalidArgumentException::class)
        ->and(fn () => $action->execute('unknown:foo'))->toThrow(InvalidArgumentException::class)
        ->and(fn () => $action->execute('table:'))->toThrow(InvalidArgumentException::class);
});

test('result shape never exposes foreign tenant fields', function (): void {
    $table = new TableData(1, 'T1', 4, 'Sala', 'available');
    $action = new ScanQrAction(new FakeRestaurantGateway(tables: ['1' => $table]));

    $result = $action->execute('table:1');

    expect($result)->toHaveKeys(['type', 'table', 'product', 'category', 'raw'])
        ->and($result)->not->toHaveKey('tenant_id')
        ->and($result)->not->toHaveKey('location_id');
});
