<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Concerns\EditPageActions;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditProduct extends EditRecord
{
    use EditPageActions {
        getHeaderActions as baseHeaderActions;
    }

    protected static string $resource = ProductResource::class;

    // Включаем слежение за несохранёнными правками (в панели оно выключено):
    // без него savedDataHash не заполняется и сравнивать не с чем.
    protected function hasUnsavedDataChangesAlert(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->baseHeaderActions(),
            $this->duplicateAction(),
        ];
    }

    /** Копия товара под другую толщину — типовой сценарий: линейка плит отличается только ей. */
    private function duplicateAction(): Action
    {
        return Action::make('duplicate')
            ->label('Дублировать')
            ->icon(Heroicon::OutlinedDocumentDuplicate)
            ->modalHeading('Дублировать товар')
            ->modalSubmitActionLabel('Дублировать')
            ->schema(fn (): array => [
                TextInput::make('thickness')
                    ->label('Толщина дубликата, мм')
                    ->helperText('Подставится в название, характеристику «Толщина» и калькулятор.')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(9999),
                Radio::make('save_original')
                    ->label('В текущем товаре есть несохранённые изменения')
                    ->boolean('Сохранить их (дубликат снимется с сохранённого)', 'Отбросить')
                    ->default(1)
                    ->required()
                    ->visible($this->hasUnsavedChanges()),
            ])
            ->action(function (array $data): void {
                if ($data['save_original'] ?? false) {
                    $this->save(shouldRedirect: false);
                }

                $copy = $this->getRecord()->duplicateWithThickness((int) $data['thickness']);

                // Форма считается чистой — иначе браузер переспросит про правки, которые мы уже отработали
                $this->rememberData();
                $this->redirect(static::getResource()::getUrl('edit', ['record' => $copy]));
            });
    }

    /** Правки в форме есть, но не сохранены: сверяем текущее состояние с хэшем последнего сохранения. */
    private function hasUnsavedChanges(): bool
    {
        $saved = $this->savedDataHash;
        $this->rememberData();
        $changed = $this->savedDataHash !== $saved;
        $this->savedDataHash = $saved;

        return $changed;
    }
}
