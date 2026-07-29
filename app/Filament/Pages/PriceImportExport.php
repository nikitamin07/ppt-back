<?php

namespace App\Filament\Pages;

use App\Services\PriceSheet;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

/**
 * @property-read Schema $form
 */
class PriceImportExport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $navigationLabel = 'Импорт/экспорт цен';
    
    protected static string|UnitEnum|null $navigationGroup = 'Администрирование';

    protected static ?string $title = 'Импорт/экспорт цен';

    protected string $view = 'filament.pages.price-import-export';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('file')
                    ->label('Excel-таблица с ценами')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->storeFiles(false) // держим как временную загрузку — читаем файл на месте, на диск не кладём
                    ->required(),
            ])
            ->statePath('data');
    }

    /** Отдать XLSX со всеми товарами и текущими ценами. */
    public function export()
    {
        $path = tempnam(sys_get_temp_dir(), 'prices');
        app(PriceSheet::class)->write($path);

        return response()
            ->download($path, 'ceny-tovarov-'.date('Y-m-d').'.xlsx')
            ->deleteFileAfterSend();
    }

    /** Прочитать загруженный файл и применить цены; о пропущенных товарах — предупреждение. */
    public function import(): void
    {
        $file = collect(Arr::wrap($this->form->getState()['file']))->first();

        $result = app(PriceSheet::class)->import($file->getRealPath());

        $this->form->fill(); // очищаем поле загрузки

        $this->notifyResult($result['applied'], $result['skipped']);
    }

    /** @param string[] $skipped */
    private function notifyResult(int $applied, array $skipped): void
    {
        if ($skipped === []) {
            Notification::make()
                ->success()
                ->title('Импорт завершён')
                ->body("Обновлено товаров: {$applied}.")
                ->send();

            return;
        }

        // Длинный список цен режем, чтобы уведомление не разрослось на весь экран.
        $shown = array_slice($skipped, 0, 25);
        $lines = array_map(fn (string $s): string => '• '.e($s), $shown);
        if (count($skipped) > count($shown)) {
            $lines[] = '• …и ещё '.(count($skipped) - count($shown));
        }

        Notification::make()
            ->warning()
            ->title("Импорт завершён: обновлено {$applied}, пропущено ".count($skipped))
            ->body(new HtmlString(implode('<br>', $lines)))
            ->persistent()
            ->send();
    }
}
