<?php

namespace App\Filament\Resources\Comments\Pages;

use App\Filament\Resources\Comments\CommentResource;
use App\Models\Comment;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $pending = Comment::where('is_approved', false)->count();

        return [
            'all' => Tab::make('Все'),
            'pending' => Tab::make('На модерации')
                ->badge($pending)
                ->badgeColor($pending > 0 ? 'warning' : 'gray')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('is_approved', false)),
        ];
    }
}
