<?php

namespace App\Filament\Resources\Comments\Pages;

use App\Filament\Resources\Comments\CommentResource;
use App\Filament\Resources\Concerns\EditPageActions;
use Filament\Resources\Pages\EditRecord;

class EditComment extends EditRecord
{
    use EditPageActions;

    protected static string $resource = CommentResource::class;
}
