<?php

namespace App\Filament\Resources\Mailings\RelationManagers;

use App\Models\Mailing;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FailedDeliveriesRelationManager extends RelationManager
{
    protected static string $relationship = 'deliveries';

    protected static ?string $title = 'Недоставленные адресаты';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereIn('status', ['failed', 'bounced', 'complained']))
            ->columns([
                TextColumn::make('email')
                    ->label('Адрес')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Итог')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bounced' => 'Отскок',
                        'complained' => 'Жалоба на спам',
                        default => 'Ошибка отправки',
                    })
                    ->color(fn (string $state): string => $state === 'failed' ? 'warning' : 'danger'),
                TextColumn::make('error')
                    ->label('Причина')
                    ->limit(70)
                    ->wrap(),
                TextColumn::make('updated_at')
                    ->label('Последняя попытка')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->headerActions([
                Action::make('resend')
                    ->label('Послать ещё раз')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->color('warning')
                    ->requiresConfirmation()
                    // Повторяем только временные ошибки отправки; отскоки и жалобы слать заново вредно.
                    ->modalDescription('Заново уйдут только письма с временной ошибкой отправки. Отскоки и жалобы на спам не переотправляются.')
                    ->visible(fn (): bool => $this->getOwnerRecord()->deliveries()->where('status', 'failed')->exists())
                    ->action(function (): void {
                        /** @var Mailing $mailing */
                        $mailing = $this->getOwnerRecord();
                        $count = $mailing->requeueFailed();

                        Notification::make()
                            ->success()
                            ->title("В очередь возвращено писем: {$count}")
                            ->send();
                    }),
            ])
            ->emptyStateHeading('Недоставленных писем нет');
    }
}
