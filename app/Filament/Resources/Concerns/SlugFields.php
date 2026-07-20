<?php

namespace App\Filament\Resources\Concerns;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

/** Пара «название + слаг» с автозаполнением: одинакова у товаров, категорий, статей и тегов. */
trait SlugFields
{
    /** Поле-заголовок; слаг перегенерируется транслитом на каждое изменение — и при создании, и при редактировании. */
    private static function titleField(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->required()
            ->live(onBlur: true)
            ->afterStateUpdated(fn (?string $state, Set $set) => $set('slug', Str::slug((string) $state)));
    }

    /** Только отображение — редактируется не руками, а через titleField() выше. */
    private static function slugField(): TextInput
    {
        return TextInput::make('slug')
            ->label('Слаг (адрес страницы)')
            ->required()
            ->readOnly()
            ->unique(ignoreRecord: true);
    }
}
