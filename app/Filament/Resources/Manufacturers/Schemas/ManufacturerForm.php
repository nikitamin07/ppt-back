<?php

namespace App\Filament\Resources\Manufacturers\Schemas;

use App\Services\ImageOptimizer;
use Illuminate\Http\UploadedFile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ManufacturerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->unique(ignoreRecord: true),
                FileUpload::make('logo')
                    ->label('Логотип')
                    ->image()
                    ->disk('public')
                    ->directory('manufacturers')
                    ->saveUploadedFileUsing(fn (UploadedFile $file) => ImageOptimizer::storeWebp($file, 'manufacturers', 400)),
            ]);
    }
}
