<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Заголовок')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set, string $operation): void {
                        if ($operation === 'create') {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),
                TextInput::make('slug')
                    ->label('Слаг (адрес страницы)')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('tags')
                    ->label('Теги')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
                Textarea::make('excerpt')
                    ->label('Анонс (краткое описание)')
                    ->columnSpanFull(),
                RichEditor::make('content')
                    ->label('Текст статьи')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('cover_image')
                    ->label('Обложка')
                    ->image()
                    ->disk('public')
                    ->directory('posts'),
                DateTimePicker::make('published_at')
                    ->label('Дата публикации'),
            ]);
    }
}
