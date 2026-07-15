<?php

namespace App\Filament\Resources\Concerns;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;

/**
 * Edit-страницы: в шапке вместо Delete — «назад к списку»,
 * Delete — в подвале формы справа (Save/Cancel слева).
 *
 * @mixin \Filament\Resources\Pages\EditRecord
 */
trait EditPageActions
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToList')
                ->label('Ко всем записям: '.static::getResource()::getPluralModelLabel())
                ->url(static::getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            ...parent::getFormActions(),
            DeleteAction::make()
                ->extraAttributes(['style' => 'margin-inline-start: auto']),
        ];
    }
}
