<?php

namespace App\Filament\Resources\Categories\RelationManagers;

use App\Filament\Resources\Concerns\SlugFields;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/** Подкатегории родительской категории: parent_id проставляет сама связь children(). */
class ChildrenRelationManager extends RelationManager
{
    use SlugFields;

    protected static string $relationship = 'children';

    protected static ?string $title = 'Подкатегории';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Название')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('Слаг'),
                TextColumn::make('products_count')
                    ->label('Товаров')
                    ->counts('products'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Добавить подкатегорию')
                    ->modalHeading('Добавить подкатегорию'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    // Дерево строго одноуровневое: у подкатегории своих детей быть не может
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->parent_id === null;
    }
}
