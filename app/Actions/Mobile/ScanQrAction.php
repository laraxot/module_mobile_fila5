<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Modules\Restaurant\Models\Product;
use Modules\Restaurant\Models\ProductCategory;
use Spatie\QueueableAction\QueueableAction;

/**
 * Action for scanning QR codes on menu items.
 * Returns product information and adds to current order.
 */
class ScanQrAction
{
    use QueueableAction;

    public function execute(string $qrCode, ?string $waiterSessionId = null): array
    {
        // QR code format: "product:{id}" or "category:{id}" or "table:{id}"
        if (str_starts_with($qrCode, 'product:')) {
            return $this->handleProductQr($qrCode, $waiterSessionId);
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

    private function handleProductQr(string $qrCode, ?string $waiterSessionId): array
    {
        $productId = (int) str_after($qrCode, 'product:');
        $product = Product::with(['category', 'modifiers', 'images'])->find($productId);

        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found',
            ];
        }

        return [
            'success' => true,
            'type' => 'product',
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'category' => $product->category?->name,
                'image' => $product->images->first()?->url,
                'modifiers' => $product->modifiers->map(fn($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'price' => $m->price,
                    'type' => $m->type,
                ]),
                'allergens' => $product->allergens ?? [],
                'dietary' => $product->dietary ?? [],
            ],
        ];
    }

    private function handleCategoryQr(string $qrCode): array
    {
        $categoryId = (int) str_after($qrCode, 'category:');
        $category = ProductCategory::with('products')->find($categoryId);

        if (!$category) {
            return [
                'success' => false,
                'message' => 'Category not found',
            ];
        }

        return [
            'success' => true,
            'type' => 'category',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'products' => $category->products->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => $p->price,
                    'image' => $p->images->first()?->url,
                ])->values(),
            ],
        ];
    }

    private function handleTableQr(string $qrCode): array
    {
        $tableId = (int) str_after($qrCode, 'table:');
        $table = \Modules\Restaurant\Models\DiningTable::find($tableId);

        if (!$table) {
            return [
                'success' => false,
                'message' => 'Table not found',
            ];
        }

        return [
            'success' => true,
            'type' => 'table',
            'data' => [
                'id' => $table->id,
                'number' => $table->number,
                'name' => $table->name,
                'capacity' => $table->capacity,
                'zone' => $table->zone?->name,
            ],
        ];
    }
}