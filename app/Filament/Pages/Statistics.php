<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Statistics\AllCallbackRequestsTable;
use App\Filament\Widgets\SiteVisitsChart;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Statistics extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Статистика';

    protected static ?string $title = 'Статистика';

    protected function getHeaderWidgets(): array
    {
        return [
            SiteVisitsChart::class,
            AllCallbackRequestsTable::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}
