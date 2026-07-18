<?php

namespace App\Filament\Pages\Statistics;

use App\Filament\Resources\CallbackRequests\Tables\CallbackRequestsTable;
use App\Models\CallbackRequest;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * Полная история заявок для страницы «Статистика» — в отличие от дашбордского
 * LatestCallbackRequests (только необработанные), здесь весь список без фильтра.
 * Namespace вне app/Filament/Widgets намеренно: discoverWidgets() сканирует только
 * ту папку, а этот виджет не должен всплывать на дашборде сам по себе.
 */
class AllCallbackRequestsTable extends TableWidget
{
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return CallbackRequestsTable::configure(
            $table->query(CallbackRequest::query()),
        )->heading('Все заявки');
    }
}
