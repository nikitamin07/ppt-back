<?php

namespace App\Filament\Resources\CallbackRequests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

/**
 * Общий набор колонок для заявок — больше не привязан к отдельному Resource (см.
 * app/Filament/Pages/Statistics/AllCallbackRequestsTable.php, единственный потребитель).
 */
class CallbackRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Имя')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Телефон')
                    ->copyable(),
                TextColumn::make('comment')
                    ->label('Комментарий')
                    ->limit(60)
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('source_url')
                    ->label('Страница')
                    ->limit(40)
                    ->url(fn (?string $state): ?string => $state)
                    ->openUrlInNewTab()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Получена')
                    ->dateTime()
                    ->sortable(),
                ToggleColumn::make('is_processed')
                    ->label('Обработана'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_processed')
                    ->label('Обработана'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
