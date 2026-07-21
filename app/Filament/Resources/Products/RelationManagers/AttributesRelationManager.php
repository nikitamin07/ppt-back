<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\Attribute;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

/**
 * Характеристики товара: pivot attribute_product (value, position).
 * Порядок меняется перетаскиванием строк (пишет pivot.position),
 * вывод сортирует orderByPivot('position') в Product::attributes().
 */
class AttributesRelationManager extends RelationManager
{
    protected static string $relationship = 'attributes';

    protected static ?string $title = 'Характеристики';

    // Без этого AttachAction не знает, какое поле показывать в списке выбора,
    // и подставляет название модели («Attribute») одинаковое для каждой строки.
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('value')
                    ->label('Значение')
                    ->required()
                    ->maxLength(20),
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
                    ->label('Добавить хар-ку')
                    ->modalHeading('Добавить хар-ку')
                    ->preloadRecordSelect()
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Выбрать существующую')
                            ->hiddenLabel(false)
                            ->live()
                            ->required(fn (Get $get): bool => blank($get('new_attribute_name'))),
                        TextInput::make('new_attribute_name')
                            ->label('Или добавить новую')
                            ->maxLength(80)
                            ->live()
                            ->required(fn (Get $get): bool => blank($get('recordId'))),
                        TextInput::make('value')
                            ->label('Значение')
                            ->required()
                            ->maxLength(20),
                    ])
                    ->mutateDataUsing(function (array $data): array {
                        // Ввели новое имя — заводим (или переиспользуем по слагу) Attribute и подставляем его id
                        if (filled($data['new_attribute_name'] ?? null)) {
                            $data['recordId'] = Attribute::firstOrCreate(
                                ['slug' => Str::slug($data['new_attribute_name'])],
                                ['name' => $data['new_attribute_name']],
                            )->getKey();
                        }
                        unset($data['new_attribute_name']);

                        // Новая характеристика встаёт в конец списка
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
