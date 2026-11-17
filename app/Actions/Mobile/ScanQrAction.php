<?php

declare(strict_types=1);

namespace Modules\Mobile\Actions\Mobile;

use InvalidArgumentException;
use Modules\Mobile\Contracts\RestaurantGateway;
use Modules\Mobile\Datas\CategoryData;
use Modules\Mobile\Datas\ProductData;
use Modules\Mobile\Datas\TableData;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

/**
 * Risolve un payload QR in contesto reale (tavolo / prodotto / categoria).
 *
 * Formati accettati (case-insensitive sul prefisso):
 * - `table:<id>`
 * - `product:<id>`
 * - `category:<id>`
 * - oppure solo un id numerico interpretato come tavolo (compatibilità QR stampati semplici)
 *
 * Non contiene mock: delega a {@see RestaurantGateway}. Record mancanti o inattivi
 * sollevano InvalidArgumentException (errore sicuro, senza leak cross-tenant).
 *
 * @see docs/bmad-output/stories/1.1.real-catalog-and-qr-context.story.md
 */
class ScanQrAction
{
    public function __construct(
        private readonly RestaurantGateway $restaurant,
    ) {
    }

    /**
     * @return array{
     *     type: 'table'|'product'|'category',
     *     table: ?TableData,
     *     product: ?ProductData,
     *     category: ?CategoryData,
     *     raw: string
     * }
     */
    public function execute(string $qrPayload): array
    {
        $raw = SafeStringCastAction::cast($qrPayload);
        $raw = trim($raw);

        if ($raw === '') {
            throw new InvalidArgumentException('QR payload vuoto');
        }

        [$type, $id] = $this->parse($raw);

        return match ($type) {
            'table' => $this->resolveTable($id, $raw),
            'product' => $this->resolveProduct($id, $raw),
            'category' => $this->resolveCategory($id, $raw),
        };
    }

    /**
     * @return array{0: 'table'|'product'|'category', 1: string}
     */
    private function parse(string $raw): array
    {
        if (preg_match('/^(table|product|category)\s*:\s*(.+)$/i', $raw, $m) === 1) {
            $type = strtolower($m[1]);
            $id = trim($m[2]);

            if ($id === '') {
                throw new InvalidArgumentException(sprintf('QR %s senza identificativo', $type));
            }

            /** @var 'table'|'product'|'category' $type */
            return [$type, $id];
        }

        // Solo id numerico → tavolo (QR stampati legacy)
        if (preg_match('/^\d+$/', $raw) === 1) {
            return ['table', $raw];
        }

        throw new InvalidArgumentException('Formato QR non riconosciuto');
    }

    /**
     * @return array{type: 'table', table: TableData, product: null, category: null, raw: string}
     */
    private function resolveTable(string $id, string $raw): array
    {
        $table = $this->restaurant->table($id);

        if ($table === null) {
            throw new InvalidArgumentException('Tavolo non trovato o non attivo');
        }

        if (! $this->isActiveStatus($table->status)) {
            throw new InvalidArgumentException('Tavolo non attivo');
        }

        return [
            'type' => 'table',
            'table' => $table,
            'product' => null,
            'category' => null,
            'raw' => $raw,
        ];
    }

    /**
     * @return array{type: 'product', table: null, product: ProductData, category: null, raw: string}
     */
    private function resolveProduct(string $id, string $raw): array
    {
        $product = $this->restaurant->product($id);

        if ($product === null) {
            throw new InvalidArgumentException('Prodotto non trovato o non attivo');
        }

        return [
            'type' => 'product',
            'table' => null,
            'product' => $product,
            'category' => null,
            'raw' => $raw,
        ];
    }

    /**
     * @return array{type: 'category', table: null, product: null, category: CategoryData, raw: string}
     */
    private function resolveCategory(string $id, string $raw): array
    {
        $category = $this->restaurant->category($id);

        if ($category === null) {
            throw new InvalidArgumentException('Categoria non trovata o non attiva');
        }

        return [
            'type' => 'category',
            'table' => null,
            'product' => null,
            'category' => $category,
            'raw' => $raw,
        ];
    }

    private function isActiveStatus(string $status): bool
    {
        $normalized = strtolower(trim($status));

        // Stati operativi ammessi per un tavolo scansionabile
        return in_array($normalized, ['available', 'free', 'open', 'active', 'occupied', 'busy'], true);
    }
}
