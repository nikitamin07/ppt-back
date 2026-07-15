<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Concerns\EditPageActions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    use EditPageActions;

    protected static string $resource = CategoryResource::class;
}
