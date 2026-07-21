<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Resources\Concerns\SlugFields;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CategoryForm
{
    use SlugFields;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::titleField('name', 'Название'),
                self::slugField(),
                Textarea::make('description')
                    ->label('Описание')
                    ->helperText('Продающий текст категории — показывается на странице каталога и работает на поиск.')
                    ->rows(5)
                    ->columnSpanFull(),
                self::metaDescriptionField(),
            ]);
    }
}
