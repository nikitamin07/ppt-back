<?php

namespace App\Filament\Resources\Mailings\Pages;

use App\Filament\Resources\Mailings\MailingResource;
use App\Models\Mailing;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewMailing extends ViewRecord
{
    protected static string $resource = MailingResource::class;

    protected static ?string $title = 'Результаты рассылки';

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
