<?php

namespace App\Filament\Resources\Mailings\Tables;

use App\Filament\Resources\Mailings\MailingResource;
use App\Models\Mailing;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MailingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('subject')
                    ->label('Тема письма')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->date('d.m.Y')
                    ->sortable(),
                TextColumn::make('send_to_all')
                    ->label('Кому')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Всем' : 'Выбранным')
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray'),
                TextColumn::make('progress')
                    ->label('Отправлено')
                    ->state(fn (Mailing $record): string => "{$record->sent_count} / {$record->total}"
                        .($record->failed_count > 0 ? " (ошибок: {$record->failed_count})" : '')),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sent' => 'Завершена',
                        'paused' => 'Приостановлена',
                        default => 'Идёт',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'success',
                        'paused' => 'gray',
                        default => 'info',
                    }),
            ])
            ->recordActions([
                Action::make('pause')
                    ->label('Приостановить')
                    ->icon(Heroicon::OutlinedPause)
                    ->color('gray')
                    ->visible(fn (Mailing $record): bool => $record->status === 'sending')
                    ->action(fn (Mailing $record) => $record->pause()),
                Action::make('resume')
                    ->label('Возобновить')
                    ->icon(Heroicon::OutlinedPlay)
                    ->color('success')
                    ->visible(fn (Mailing $record): bool => $record->status === 'paused')
                    ->action(fn (Mailing $record) => $record->resume()),
                Action::make('results')
                    ->label('Смотреть результаты')
                    ->icon(Heroicon::OutlinedChartBar)
                    ->url(fn (Mailing $record): string => MailingResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
