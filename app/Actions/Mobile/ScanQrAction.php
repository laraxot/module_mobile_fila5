<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use Spatie\QueueableAction\QueueableAction;

/**
 * Action for scanning QR codes on menu items.
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
        // Mock product data for demo - in production would query OrderQueue or a Product model
        $mockProducts = [
            1 => ['name' => 'Caffè', 'description' => 'Espresso italiano', 'price' => 1.50, 'modifiers' => [], 'category' => 'Bevande'],
            2 => ['name' => 'Pasta al Pomodoro', 'description' => 'Pasta fresca', 'price' => 8.00, 'modifiers' => [], 'category' => 'Primi'],
        ];

        $product = $mockProducts[$productId] ?? null;

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
                'id' => $productId,
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'category' => $product['category'],
                'modifiers' => [],
                'allergens' => [],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function handleCategoryQr(string $qrCode): array
    {
        $categoryId = (int) substr($qrCode, strlen('category:'));
        $mockCategories = [
            1 => ['name' => 'Bevande', 'products' => [1 => ['name' => 'Caffè', 'price' => 1.50]]],
            2 => ['name' => 'Primi', 'products' => [2 => ['name' => 'Pasta al Pomodoro', 'price' => 8.00]]],
        ];

        $category = $mockCategories[$categoryId] ?? null;

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
                'id' => $categoryId,
                'name' => $category['name'],
                'products' => array_map(function ($p) use ($category) {
                    return [
                        'id' => array_search($p, $category['products']),
                        'name' => $p['name'],
                        'price' => $p['price'],
                    ];
                }, $category['products']),
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function handleTableQr(string $qrCode): array
    {
        $tableId = (int) substr($qrCode, strlen('table:'));
        // Mock table data for demo
        $mockTables = [
            1 => ['name' => 'Tavolo 1', 'seats' => 4, 'zone' => 'Centro'],
            2 => ['name' => 'Tavolo 2', 'seats' => 2, 'zone' => 'Finestra'],
        ];

        $table = $mockTables[$tableId] ?? null;

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
                'id' => $tableId,
                'name' => $table['name'],
                'capacity' => $table['seats'],
                'zone' => $table['zone'],
            ],
        ];
    }
}
