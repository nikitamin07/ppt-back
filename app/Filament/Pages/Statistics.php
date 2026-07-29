<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Statistics\AllCallbackRequestsTable;
use App\Filament\Widgets\SiteVisitsChart;
use App\Models\CallbackRequest;
use BackedEnum;
use UnitEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class Statistics extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Администрирование';

    protected static ?string $navigationLabel = 'Заявки и статистика';

    protected static ?string $title = 'Заявки и статистика';

    public static function getNavigationBadge(): ?string
    {
        $count = CallbackRequest::where('is_processed', false)->count();

        return $count > 0 ? (string) $count : null;
    }

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
