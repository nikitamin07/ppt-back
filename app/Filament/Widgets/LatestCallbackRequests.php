<?php

namespace App\Filament\Widgets;

use App\Models\CallbackRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestCallbackRequests extends TableWidget
{
    protected static ?int $sort = 0;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Новые заявки')
            ->query(fn (): Builder => CallbackRequest::query()->where('is_processed', false))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Имя'),
                TextColumn::make('phone')->label('Телефон')->copyable(),
                TextColumn::make('created_at')->label('Получена')->dateTime()->sortable(),
            ])
            ->paginated(false);
    }
}
