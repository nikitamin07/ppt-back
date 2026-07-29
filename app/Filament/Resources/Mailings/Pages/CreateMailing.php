<?php

namespace App\Filament\Resources\Mailings\Pages;

use App\Filament\Resources\Mailings\MailingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMailing extends CreateRecord
{
    protected static string $resource = MailingResource::class;

    /** @var int[]|null null → всем активным адресам */
    protected ?array $recipientIds = null;

    /**
     * recipient_ids — поле формы, не колонка.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->recipientIds = ($data['send_to_all'] ?? true)
            ? null
            : array_map('intval', $data['recipient_ids'] ?? []);

        unset($data['recipient_ids']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->buildDeliveries($this->recipientIds);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Рассылка создана — письма уходят постепенно';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
