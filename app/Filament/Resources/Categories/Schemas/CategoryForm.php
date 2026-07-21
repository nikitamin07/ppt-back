<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Resources\Concerns\SlugFields;
use App\Models\Category;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
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
                // Настройка корневой категории — подкатегории наследуют её значение
                Toggle::make('show_calculator')
                    ->label('Выводить калькулятор')
                    ->helperText('Калькулятор объёма на карточках товаров этой категории и её подкатегорий.')
                    ->default(true)
                    ->inline(false)
                    ->visible(fn (?Category $record): bool => $record === null || $record->parent_id === null)
                    ->columnSpanFull(),
            ]);
    }
}
