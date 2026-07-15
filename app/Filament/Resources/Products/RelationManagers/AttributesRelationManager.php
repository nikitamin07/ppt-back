<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Характеристики товара: pivot attribute_product (value, position).
 * Порядок меняется перетаскиванием строк (пишет pivot.position),
 * вывод сортирует orderByPivot('position') в Product::attributes().
 */
class AttributesRelationManager extends RelationManager
{
    protected static string $relationship = 'attributes';

    protected static ?string $title = 'Характеристики';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('value')
                    ->label('Значение')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('position')
            ->columns([
                TextColumn::make('name')
                    ->label('Характеристика'),
                TextColumn::make('value')
                    ->label('Значение'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('value')
                            ->label('Значение')
                            ->required(),
                    ])
                    // Новая характеристика встаёт в конец списка
                    ->mutateDataUsing(function (array $data): array {
                        $data['position'] = $this->getOwnerRecord()->attributes()->max('attribute_product.position') + 1;

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
            ]);
    }
}
