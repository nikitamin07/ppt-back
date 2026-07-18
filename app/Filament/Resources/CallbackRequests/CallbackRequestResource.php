<?php

namespace App\Filament\Resources\CallbackRequests;

use App\Filament\Resources\CallbackRequests\Pages\ListCallbackRequests;
use App\Filament\Resources\CallbackRequests\Tables\CallbackRequestsTable;
use App\Models\CallbackRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CallbackRequestResource extends Resource
{
    protected static ?string $model = CallbackRequest::class;

    protected static ?string $modelLabel = 'заявка';

    protected static ?string $pluralModelLabel = 'заявки';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    public static function table(Table $table): Table
    {
        return CallbackRequestsTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = CallbackRequest::where('is_processed', false)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCallbackRequests::route('/'),
        ];
    }
}
