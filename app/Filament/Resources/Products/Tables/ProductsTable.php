<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    /** Копейки из БД -> рубли в таблице. */
    private static function rubles(?int $state): ?string
    {
        return $state !== null ? number_format($state / 100, 2, ',', ' ').' руб' : null;
    }

    /** Вкладка «Популярные товары» на странице списка (см. ListProducts::getTabs). */
    private static function onFeaturedTab(mixed $livewire): bool
    {
        return ($livewire->activeTab ?? null) === 'featured';
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id')
            // Порядок блока «Популярные» задаётся перетаскиванием, но только на своей вкладке:
            // в общем списке тащить нечего — у непопулярных товаров позиции нет.
            ->reorderable('featured_position', fn ($livewire): bool => self::onFeaturedTab($livewire))
            ->columns([
                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Категория')
                    ->sortable(),
                TextColumn::make('manufacturer.name')
                    ->label('Производитель')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('slug')
                    ->label('Слаг')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('price')
                    ->label('Цена')
                    ->formatStateUsing(fn (?int $state): ?string => self::rubles($state))
                    ->sortable(),
                TextColumn::make('discount_price')
                    ->label('Цена со скидкой')
                    ->formatStateUsing(fn (?int $state): ?string => self::rubles($state))
                    ->sortable(),
                TextColumn::make('price_unit')
                    ->label('Ед. изм.'),
                IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Категория')
                    ->relationship('category', 'name'),
                TernaryFilter::make('is_active')
                    ->label('Активен'),
            ])
            ->recordActions([
                self::toggleFeaturedAction(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /** Единственный способ управлять блоком «Популярные»: в форме товара флага больше нет. */
    private static function toggleFeaturedAction(): Action
    {
        return Action::make('toggleFeatured')
            ->label(fn (Product $record): string => $record->is_featured ? 'Убрать из популярных' : 'В популярные')
            ->icon(fn (Product $record): Heroicon => $record->is_featured ? Heroicon::Star : Heroicon::OutlinedStar)
            ->color(fn (Product $record): string => $record->is_featured ? 'warning' : 'gray')
            ->action(function (Product $record): void {
                if (! $record->is_featured && Product::where('is_featured', true)->count() >= Product::FEATURED_LIMIT) {
                    Notification::make()
                        ->danger()
                        ->title('Максимум популярных товаров: '.Product::FEATURED_LIMIT)
                        ->body('Сначала уберите из популярных другой товар.')
                        ->send();

                    return;
                }

                $record->update(['is_featured' => ! $record->is_featured]);
            });
    }
}
