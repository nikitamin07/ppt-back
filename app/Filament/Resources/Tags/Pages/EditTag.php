<?php

namespace App\Filament\Resources\Tags\Pages;

use App\Filament\Resources\Concerns\EditPageActions;
use App\Filament\Resources\Tags\TagResource;
use Filament\Resources\Pages\EditRecord;

class EditTag extends EditRecord
{
    use EditPageActions;

    protected static string $resource = TagResource::class;
}
