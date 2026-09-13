<?php

declare(strict_types=1);

namespace Modules\Mobile\Filament\Resources\Mobile;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
// RIMOSSO: dipendenza Mobile -> Restaurant proibita
use Modules\Xot\Filament\Resources\XotBaseResource;

class TableResource extends XotBaseResource
{
    protected static ?string $model = Module::class; // TODO: modello locale Mobile

    public static function getRelations(): array
    {
        return [];
    }
}