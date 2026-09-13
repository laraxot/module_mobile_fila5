<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;





use Spatie\QueueableAction\QueueableAction;

/**
 * Action for scanning QR codes on menu items.
 * Returns product information and adds to current order.
 */
class ScanQrAction
{
    use QueueableAction;

    /** @return array<string, mixed> */
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

    /** @return array<string, mixed> */
    private function handleProductQr(string $qrCode, ?string $waiterSessionId): array
    {
        $productId = (int) substr($qrCode, strlen('product:'));
        $product = Product::with(['category', 'modifiers'])->find($productId);

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
                'modifiers' => $product->modifiers->map(static function (ProductModifier $modifier): array {
                    return [
                        'id' => $modifier->id,
                        'name' => $modifier->name,
                        'price' => $modifier->price_delta,
                    ];
                }),
                'allergens' => $product->allergens === null ? [] : array_map('trim', explode(',', $product->allergens)),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function handleCategoryQr(string $qrCode): array
    {
        $categoryId = (int) substr($qrCode, strlen('category:'));
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
                'products' => $category->products->map(static function (Product $product): array {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                    ];
                })->values(),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function handleTableQr(string $qrCode): array
    {
        $tableId = (int) substr($qrCode, strlen('table:'));
        $table = DiningTable::with('zone')->find($tableId);

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
                'name' => $table->name,
                'capacity' => $table->seats,
                'zone' => $table->zone?->name,
            ],
        ];
    }
}
