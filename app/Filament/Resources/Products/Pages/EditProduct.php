<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Concerns\EditPageActions;
use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    use EditPageActions;

    protected static string $resource = ProductResource::class;
}
