<?php

declare(strict_types=1);

namespace Modules\Mobile\Filament;

use Filament\Panel;
use Modules\Xot\Filament\XotBasePanelProvider;
use Filament\Support\Colors\Color;

class AdminPanelProvider extends XotBasePanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('Mobile_admin')
            ->path('Mobile/admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: __DIR__.'/Resources', for: 'Modules\\Mobile\\Filament\\Resources')
            ->discoverPages(in: __DIR__.'/Pages', for: 'Modules\\Mobile\\Filament\\Pages')
            ->discoverWidgets(in: __DIR__.'/Widgets', for: 'Modules\\Mobile\\Filament\\Widgets')
            ->discoverClusters(in: __DIR__.'/Clusters', for: 'Modules\\Mobile\\Filament\\Clusters');
    }
}
