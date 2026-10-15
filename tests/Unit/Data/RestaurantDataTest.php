<?php

declare(strict_types=1);

use Modules\Mobile\Data\CategoryData;
use Modules\Mobile\Data\ProductData;
use Modules\Mobile\Data\TableData;

test('restaurant boundary data is immutable and serializable by properties', function (): void {
    $product = new ProductData(7, 'Pizza Margherita', 8.5, allergens: ['gluten']);
    $category = new CategoryData(2, 'Pizze', [$product]);
    $table = new TableData(4, 'Tavolo 4', 4, 'Sala', 'available');

    expect($category->products)->toHaveCount(1)
        ->and($category->products[0])->toBe($product)
        ->and($product->allergens)->toContain('gluten')
        ->and($table->status)->toBe('available');
});
