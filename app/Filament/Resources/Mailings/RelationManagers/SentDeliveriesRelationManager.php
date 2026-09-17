<?php

namespace App\Filament\Resources\Mailings\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SentDeliveriesRelationManager extends RelationManager
{
    protected static string $relationship = 'deliveries';

    protected static ?string $title = 'Отправленные адресаты';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereNotIn('status', ['pending', 'failed']))
            ->defaultSort('sent_at', 'desc')
            ->columns([
                TextColumn::make('email')
                    ->label('Адрес')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'delivered' => 'Доставлено',
                        'bounced' => 'Отскочило',
                        'complained' => 'Жалоба на спам',
                        default => 'Отправлено',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'delivered' => 'success',
                        'bounced' => 'warning',
                        'complained' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('sent_at')
                    ->label('Отправлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->emptyStateHeading('Пока ничего не отправлено');
    }
}
