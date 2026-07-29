<?php

namespace App\Filament\Resources\MailingRecipients\Pages;

use App\Filament\Resources\MailingRecipients\MailingRecipientResource;
use App\Models\MailingRecipient;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ListMailingRecipients extends ListRecords
{
    protected static string $resource = MailingRecipientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Экспорт Excel')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action(fn (): BinaryFileResponse => $this->export()),
            CreateAction::make()
                ->label('Добавить адрес'),
        ];
    }

    /** Выгрузка адресов в XLSX */
    private function export(): BinaryFileResponse
    {
        $path = tempnam(sys_get_temp_dir(), 'emails');

        $writer = new Writer();
        $writer->openToFile($path);
        $writer->addRow(Row::fromValues(['№', 'Почтовый адрес']));

        $i = 0;
        foreach (MailingRecipient::orderBy('email')->cursor() as $recipient) {
            $writer->addRow(Row::fromValues([++$i, $recipient->email]));
        }

        $writer->close();

        return response()
            ->download($path, 'pochtovye-adresa-'.date('Y-m-d').'.xlsx')
            ->deleteFileAfterSend();
    }
}
