<?php

declare(strict_types=1);

namespace Modules\Mobile\Filament\Resources\Mobile;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Modules\Restaurant\Models\DiningTable;
use Modules\Xot\Filament\Resources\XotBaseResource;

class TableResource extends XotBaseResource
{
    protected static ?string $model = DiningTable::class;

    public static function getRelations(): array
    {
        return [];
    }
}