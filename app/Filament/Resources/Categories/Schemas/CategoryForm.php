<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Resources\Concerns\SlugFields;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CategoryForm
{
    use SlugFields;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::titleField('name', 'Название'),
                self::slugField(),
                // Дерево строго одноуровневое: родителем может быть только корневая категория (и не она сама),
                // а категории, у которой уже есть дети, родителя назначить нельзя.
                Select::make('parent_id')
                    ->label('Родительская категория')
                    ->relationship(
                        'parent',
                        'name',
                        fn (Builder $query, ?Model $record) => $query
                            ->whereNull('parent_id')
                            ->when($record, fn (Builder $q) => $q->whereKeyNot($record->getKey())),
                    )
                    ->hidden(fn (?Model $record): bool => $record !== null && $record->children()->exists()),
                Textarea::make('description')
                    ->label('Описание')
                    ->columnSpanFull(),
            ]);
    }
}
