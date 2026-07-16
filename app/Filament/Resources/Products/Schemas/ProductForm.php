<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
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
                // Двухступенчатый выбор: сначала корневая категория, затем (если есть) её подкатегория.
                // В category_id сохраняется подкатегория, а если она не выбрана — сама корневая.
                Select::make('root_category')
                    ->label('Категория')
                    ->options(fn () => Category::whereNull('parent_id')->orderBy('id')->pluck('name', 'id'))
                    ->dehydrated(false)
                    ->live()
                    ->afterStateHydrated(function (Select $component, ?Product $record): void {
                        $category = $record?->category;
                        $component->state($category?->parent_id ?? $category?->id);
                    })
                    ->afterStateUpdated(fn (Set $set) => $set('category_id', null)),
                Select::make('category_id')
                    ->label('Подкатегория')
                    ->options(fn (Get $get) => Category::where('parent_id', $get('root_category'))->orderBy('id')->pluck('name', 'id'))
                    ->visible(fn (Get $get): bool => filled($get('root_category'))
                        && Category::where('parent_id', $get('root_category'))->exists())
                    // Товар, висящий прямо на корне: подкатегория в форме пустая
                    ->afterStateHydrated(function (Select $component, ?Product $record): void {
                        if ($record?->category !== null && $record->category->parent_id === null) {
                            $component->state(null);
                        }
                    })
                    // Не выбрана подкатегория (или поле скрыто — у корня нет детей) — сохраняем корневую
                    ->dehydratedWhenHidden()
                    ->dehydrateStateUsing(fn ($state, Get $get) => $state ?? $get('root_category')),
                Select::make('manufacturer_id')
                    ->label('Производитель')
                    ->relationship('manufacturer', 'name')
                    ->searchable()
                    ->preload(),
                Textarea::make('description')
                    ->label('Описание')
                    ->rows(12)
                    ->columnSpanFull(),
                // Режим цены: либо обычная цена (+скидка), либо объёмные тарифы (low и medium обязательны)
                Toggle::make('is_volume_price')
                    ->label('Цена зависит от объема')
                    ->inline(false)
                    ->live()
                    ->columnSpanFull(),
                self::money('price')
                    ->label('Цена')
                    ->required()
                    ->default(0)
                    ->hidden(fn (Get $get): bool => (bool) $get('is_volume_price')),
                self::money('discount_price')
                    ->label('Цена со скидкой')
                    ->hidden(fn (Get $get): bool => (bool) $get('is_volume_price')),
                // Три тарифа парами «цена слева — пояснение справа»; required действует
                // только когда поле видимо, т.е. в объёмном режиме
                self::money('volume_price_low')
                    ->label('Цена за маленький объем (Low volume price)')
                    ->required()
                    ->visible(fn (Get $get): bool => (bool) $get('is_volume_price')),
                self::volumeLabel('volume_price_low_label', 'например: до 10 кубов'),
                self::money('volume_price_medium')
                    ->label('Цена за средний объем (Medium volume price)')
                    ->required()
                    ->visible(fn (Get $get): bool => (bool) $get('is_volume_price')),
                self::volumeLabel('volume_price_medium_label', 'например: от 10 до 20 кубов'),
                self::money('volume_price_high')
                    ->label('Цена за большой объем (High volume price)')
                    ->visible(fn (Get $get): bool => (bool) $get('is_volume_price')),
                self::volumeLabel('volume_price_high_label', 'например: от 20 кубов'),
                TextInput::make('price_unit')
                    ->label('Единица измерения')
                    ->required()
                    ->default('куб'),
                FileUpload::make('image')
                    ->label('Изображение')
                    ->image()
                    ->disk('public')
                    ->directory('products'),
                // Лимит Product::FEATURED_LIMIT: при заполненном блоке переключатель блокируется
                // (выключить уже популярный товар можно всегда). Страховка — saving-хук модели.
                Toggle::make('is_featured')
                    ->label('Показывать в популярных')
                    ->inline(false)
                    ->live()
                    ->disabled(fn (?Product $record, Get $get): bool => ! $get('is_featured')
                        && Product::where('is_featured', true)->whereKeyNot($record?->getKey())->count() >= Product::FEATURED_LIMIT)
                    ->helperText(fn (?Product $record, Get $get): ?string => ! $get('is_featured')
                        && Product::where('is_featured', true)->whereKeyNot($record?->getKey())->count() >= Product::FEATURED_LIMIT
                            ? 'Максимум популярных товаров, сначала снимите выбор с другого популярного товара'
                            : null)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Активен (показывать на сайте)')
                    ->inline(false)
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }

    /** Свободный текст-пояснение к объёмному тарифу (не всегда кубы — вводит менеджер). */
    private static function volumeLabel(string $name, string $placeholder): TextInput
    {
        return TextInput::make($name)
            ->label('Пояснение')
            ->placeholder($placeholder)
            ->visible(fn (Get $get): bool => (bool) $get('is_volume_price'));
    }

    /** В БД цены в копейках, админ вводит рубли. */
    private static function money(string $name): TextInput
    {
        return TextInput::make($name)
            ->numeric()
            ->suffix('руб')
            ->formatStateUsing(fn (?int $state): ?float => $state !== null ? $state / 100 : null)
            ->dehydrateStateUsing(fn ($state): ?int => $state !== null && $state !== '' ? (int) round((float) $state * 100) : null);
    }
}
