<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\Resources\Concerns\SlugFields;
use App\Services\ImageOptimizer;
use Illuminate\Http\UploadedFile;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PostForm
{
    use SlugFields;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::titleField('title', 'Заголовок'),
                self::slugField(),
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
                    ->directory('posts')
                    ->saveUploadedFileUsing(fn (UploadedFile $file) => ImageOptimizer::storeWebp($file, 'posts', 1600)),
                DateTimePicker::make('published_at')
                    ->label('Дата публикации'),
            ]);
    }
}
