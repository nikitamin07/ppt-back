<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * Подраздел «Популярные товары»: тот же список, суженный до блока на главной.
     * Порядок задаётся перетаскиванием, включённым только здесь (см. ProductsTable).
     */
    public function getTabs(): array
    {
        $featured = Product::where('is_featured', true)->count();

        return [
            'all' => Tab::make('Все товары'),
            'featured' => Tab::make('Популярные товары')
                ->badge($featured.' из '.Product::FEATURED_LIMIT)
                ->badgeColor($featured >= Product::FEATURED_LIMIT ? 'warning' : 'primary')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->featured()),
        ];
    }
}
