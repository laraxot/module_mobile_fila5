<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Modules\Restaurant\Models\DiningTable;
use Modules\Restaurant\Models\Product;
use Modules\Restaurant\Models\ProductCategory;
use Spatie\QueueableAction\QueueableAction;

/**
 * Action for scanning QR codes on menu items and dining tables.
 * Returns product, category or table information only: adding lines to an order
 * is up to the caller (see TakeOrderAction, which receives items and qr_code).
 */
class ScanQrAction
{
    use QueueableAction;

    /** @return array<string, mixed> */
    public function execute(string $qrCode): array
    {
        // QR code format: "product:{id}" or "category:{id}" or "table:{id}"
        if (str_starts_with($qrCode, 'product:')) {
            return $this->handleProductQr($qrCode);
        }

        if (str_starts_with($qrCode, 'category:')) {
            return $this->handleCategoryQr($qrCode);
        }

        if (str_starts_with($qrCode, 'table:')) {
            return $this->handleTableQr($qrCode);
        }

        return [
            'success' => false,
            'message' => 'Invalid QR code format',
        ];
    }

    /** @return array<string, mixed> */
    private function handleProductQr(string $qrCode): array
    {
        $productId = (int) substr($qrCode, strlen('product:'));
        $product = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->find($productId);

        if ($product === null) {
            return [
                'success' => false,
                'message' => 'Product not found',
            ];
        }

        return [
            'success' => true,
            'type' => 'product',
            'data' => [
                'id' => $productId,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'category' => $product->category?->name,
                'modifiers' => [],
                'allergens' => $product->allergens === null ? [] : [$product->allergens],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function handleCategoryQr(string $qrCode): array
    {
        $categoryId = (int) substr($qrCode, strlen('category:'));
        $category = ProductCategory::query()
            ->with('products')
            ->where('is_active', true)
            ->find($categoryId);

        if ($category === null) {
            return [
                'success' => false,
                'message' => 'Category not found',
            ];
        }

        return [
            'success' => true,
            'type' => 'category',
            'data' => [
                'id' => $categoryId,
                'name' => $category->name,
                'products' => $category->products
                    ->where('is_active', true)
                    ->sortBy('sort_order')
                    ->map(static function (Product $product): array {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => $product->price,
                        ];
                    })->values()->all(),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function handleTableQr(string $qrCode): array
    {
        $tableId = (int) substr($qrCode, strlen('table:'));
        $table = DiningTable::query()
            ->with('zone')
            ->where('is_active', true)
            ->find($tableId);

        if ($table === null) {
            return [
                'success' => false,
                'message' => 'Table not found',
            ];
        }

        return [
            'success' => true,
            'type' => 'table',
            'data' => [
                'id' => $tableId,
                'name' => $table->name,
                'capacity' => $table->seats,
                'zone' => $table->zone->name,
            ],
        ];
    }
}
