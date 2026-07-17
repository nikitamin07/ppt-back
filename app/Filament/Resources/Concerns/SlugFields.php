<?php

namespace App\Filament\Resources\Concerns;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

/** Пара «название + слаг» с автозаполнением: одинакова у товаров, категорий, статей и тегов. */
trait SlugFields
{
    /** Поле-заголовок; при создании записи заполняет слаг транслитом. */
    private static function titleField(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->required()
            ->live(onBlur: true)
            ->afterStateUpdated(function (?string $state, Set $set, string $operation): void {
                if ($operation === 'create') {
                    $set('slug', Str::slug((string) $state));
                }
            });
    }

    private static function slugField(): TextInput
    {
        return TextInput::make('slug')
            ->label('Слаг (адрес страницы)')
            ->required()
            ->unique(ignoreRecord: true);
    }
}
