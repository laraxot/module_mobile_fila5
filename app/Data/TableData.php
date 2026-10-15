<?php

declare(strict_types=1);

namespace Modules\Mobile\Data;

final readonly class TableData
{
    public function __construct(
        public int|string $tableId,
        public string $name,
        public int $capacity,
        public ?string $zone = null,
        public string $status = 'available',
    ) {
    }
}
