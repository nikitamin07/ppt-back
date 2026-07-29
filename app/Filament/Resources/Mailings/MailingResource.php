<?php

namespace App\Filament\Resources\Mailings;

use App\Filament\Resources\Mailings\Pages\CreateMailing;
use App\Filament\Resources\Mailings\Pages\ListMailings;
use App\Filament\Resources\Mailings\Pages\ViewMailing;
use App\Filament\Resources\Mailings\RelationManagers\FailedDeliveriesRelationManager;
use App\Filament\Resources\Mailings\Schemas\MailingForm;
use App\Filament\Resources\Mailings\Tables\MailingsTable;
use App\Models\Mailing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MailingResource extends Resource
{
    protected static ?string $model = Mailing::class;

    protected static string|UnitEnum|null $navigationGroup = 'Администрирование';

    protected static ?string $modelLabel = 'рассылка';

    protected static ?string $pluralModelLabel = 'рассылки';

    protected static ?string $navigationLabel = 'Рассылки';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperAirplane;

    public static function form(Schema $schema): Schema
    {
        return MailingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MailingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            FailedDeliveriesRelationManager::class,
        ];
    }

    // Edit-страницы нет: рассылку не правят
    public static function getPages(): array
    {
        return [
            'index' => ListMailings::route('/'),
            'create' => CreateMailing::route('/create'),
            'view' => ViewMailing::route('/{record}'),
        ];
    }
}
