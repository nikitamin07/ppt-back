<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Concerns\EditPageActions;
use App\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    use EditPageActions;

    protected static string $resource = PostResource::class;
}
