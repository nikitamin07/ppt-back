<?php

namespace App\Filament\Resources\Mailings\Pages;

use App\Filament\Resources\Mailings\MailingResource;
use App\Models\Mailing;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ViewMailing extends ViewRecord
{
    protected static string $resource = MailingResource::class;

    protected static ?string $title = 'Результаты рассылки';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pause')
                ->label('Приостановить')
                ->icon(Heroicon::OutlinedPause)
                ->color('gray')
                ->visible(fn (): bool => $this->getRecord()->status === 'sending')
                ->action(fn () => $this->getRecord()->pause()),
            Action::make('resume')
                ->label('Возобновить')
                ->icon(Heroicon::OutlinedPlay)
                ->color('success')
                ->visible(fn (): bool => $this->getRecord()->status === 'paused')
                ->action(fn () => $this->getRecord()->resume()),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        /** @var Mailing $mailing */
        $mailing = $this->getRecord();
        $track = (bool) config('mailing.track_delivery');
        $counts = $mailing->deliveryCounts();

        // Без учёта доставки — только ошибки отправки
        $notDelivered = $track
            ? $counts['bounced'] + $counts['complained'] + $counts['failed']
            : $counts['failed'];

        $components = [
            TextEntry::make('subject')->label('Тема письма')->state($mailing->subject)->columnSpanFull(),
            TextEntry::make('status')
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
            TextEntry::make('total')->label('Всего адресатов')->badge()->color('gray')->state($mailing->total),
            TextEntry::make('accepted')->label('Отправлено')->badge()->color('info')->state($mailing->sent_count),
        ];

        if ($track) {
            $components[] = TextEntry::make('delivered')->label('Доставлено')->badge()->color('success')->state($counts['delivered']);
            $components[] = TextEntry::make('bounced')->label('Отскочило')->badge()->color('warning')->state($counts['bounced']);
            $components[] = TextEntry::make('complained')->label('Жалобы на спам')->badge()->color('danger')->state($counts['complained']);
        }

        $components[] = TextEntry::make('not_delivered')
            ->label('Не доставлено')
            ->badge()
            ->color($notDelivered > 0 ? 'danger' : 'gray')
            ->state($notDelivered);

        return $schema->components($components)->columns(3);
    }
}
