<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('Товар')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('author')
                    ->label('Имя автора')
                    ->required()
                    ->maxLength(80),
                Select::make('rating')
                    ->label('Оценка')
                    ->options([5 => '5', 4 => '4', 3 => '3', 2 => '2', 1 => '1'])
                    ->default(5)
                    ->required()
                    ->selectablePlaceholder(false),
                Textarea::make('body')
                    ->label('Текст')
                    ->helperText('Можно поправить опечатки перед публикацией.')
                    ->required()
                    ->rows(10)
                    ->columnSpanFull(),
                Toggle::make('is_approved')
                    ->label('Опубликован на сайте')
                    ->inline(false)
                    ->columnSpanFull(),
            ]);
    }
}
