<?php

namespace App\Filament\Resources\Concerns;

use Filament\Forms\Components\Textarea;
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

    /** Текст под <meta name="description">: в выдаче Google обрезается примерно на 160 символах. */
    private static function metaDescriptionField(): Textarea
    {
        return Textarea::make('meta_description')
            ->label('Meta-описание')
            ->helperText('Для сниппета в поиске. Оптимально 150–160 символов — длиннее Google обрежет.')
            ->maxLength(255)
            ->rows(3)
            ->columnSpanFull();
    }
}
