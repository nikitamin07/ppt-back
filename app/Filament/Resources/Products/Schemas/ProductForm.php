<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Resources\Concerns\SlugFields;
use App\Models\Category;
use App\Models\Product;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProductForm
{
    use SlugFields;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::titleField('name', 'Название'),
                self::slugField(),
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
                self::metaDescriptionField(),
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
                    ->hidden(self::inVolumeMode()),
                self::money('discount_price')
                    ->label('Цена со скидкой')
                    ->hidden(self::inVolumeMode()),
                // Три тарифа парами «цена слева — пояснение справа»; required действует
                // только когда поле видимо, т.е. в объёмном режиме
                self::money('volume_price_low')
                    ->label('Цена за маленький объем (Low volume price)')
                    ->required()
                    ->visible(self::inVolumeMode()),
                self::volumeLabel('volume_price_low_label', 'например: до 10 кубов'),
                self::money('volume_price_medium')
                    ->label('Цена за средний объем (Medium volume price)')
                    ->required()
                    ->visible(self::inVolumeMode()),
                self::volumeLabel('volume_price_medium_label', 'например: от 10 до 20 кубов'),
                self::money('volume_price_high')
                    ->label('Цена за большой объем (High volume price)')
                    ->visible(self::inVolumeMode()),
                self::volumeLabel('volume_price_high_label', 'например: от 20 кубов'),
                Select::make('price_unit')
                    ->label('Единица измерения')
                    ->options(Product::PRICE_UNITS)
                    ->required()
                    ->default('куб')
                    ->selectablePlaceholder(false)
                    ->live(),
                // Товар продаётся не кубами, но калькулятор в категории включён —
                // без этого коэффициента посчитать объём нечем
                TextInput::make('cubes_per_pack')
                    ->label('Кубов в упаковке (для калькулятора)')
                    ->helperText('Сколько кубометров в одной единице товара, например 0,288.')
                    ->numeric()
                    ->minValue(0.0001)
                    ->step(0.0001)
                    // Поле показано ровно тогда, когда без него калькулятор не посчитает
                    ->required()
                    ->visible(fn (Get $get): bool => self::needsCubesPerPack($get('price_unit'), $get('root_category')))
                    ->dehydratedWhenHidden(false),
                FileUpload::make('images')
                    ->label('Изображения')
                    ->helperText('Первая картинка идёт на карточку товара и в соцсети. Порядок меняется перетаскиванием.')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->appendFiles()
                    ->disk('public')
                    ->directory('products')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Активен (показывать на сайте)')
                    ->inline(false)
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }

    /** Единица не «куб», а калькулятор у корневой категории включён: нужен коэффициент пересчёта. */
    private static function needsCubesPerPack(?string $priceUnit, mixed $rootCategoryId): bool
    {
        if ($priceUnit === 'куб' || blank($rootCategoryId)) {
            return false;
        }

        return (bool) Category::whereKey($rootCategoryId)->value('show_calculator');
    }

    /** Включён ли режим объёмных цен — от него зависит видимость всего ценового блока. */
    private static function inVolumeMode(): Closure
    {
        return fn (Get $get): bool => (bool) $get('is_volume_price');
    }

    /** Свободный текст-пояснение к объёмному тарифу (не всегда кубы — вводит менеджер). */
    private static function volumeLabel(string $name, string $placeholder): TextInput
    {
        return TextInput::make($name)
            ->label('Пояснение')
            ->placeholder($placeholder)
            ->visible(self::inVolumeMode());
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
