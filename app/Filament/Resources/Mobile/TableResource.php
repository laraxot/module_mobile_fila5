<?php

declare(strict_types=1);

namespace Modules\Mobile\Filament\Resources\Mobile;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
// RIMOSSO: dipendenza Mobile -> Restaurant proibita
<<<<<<< HEAD
use Modules\Mobile\Models\OrderQueue;
=======
>>>>>>> laraxot/dev
use Modules\Xot\Filament\Resources\XotBaseResource;

class TableResource extends XotBaseResource
{
<<<<<<< HEAD
    protected static ?string $model = OrderQueue::class;
=======
    protected static ?string $model = Module::class; // TODO: modello locale Mobile
>>>>>>> laraxot/dev

    public static function getRelations(): array
    {
        return [];
    }
}