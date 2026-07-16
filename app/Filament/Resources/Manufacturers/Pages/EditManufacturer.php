<?php

namespace App\Filament\Resources\Manufacturers\Pages;

use App\Filament\Resources\Concerns\EditPageActions;
use App\Filament\Resources\Manufacturers\ManufacturerResource;
use Filament\Resources\Pages\EditRecord;

class EditManufacturer extends EditRecord
{
    use EditPageActions;

    protected static string $resource = ManufacturerResource::class;
}
