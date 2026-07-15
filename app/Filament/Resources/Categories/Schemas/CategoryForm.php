<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Название')
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
