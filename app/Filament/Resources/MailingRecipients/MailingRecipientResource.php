<?php

namespace App\Filament\Resources\MailingRecipients;

use App\Filament\Resources\MailingRecipients\Pages\ListMailingRecipients;
use App\Filament\Resources\MailingRecipients\Schemas\MailingRecipientForm;
use App\Filament\Resources\MailingRecipients\Tables\MailingRecipientsTable;
use App\Models\MailingRecipient;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MailingRecipientResource extends Resource
{
    protected static ?string $model = MailingRecipient::class;

    protected static string|UnitEnum|null $navigationGroup = 'Администрирование';

    protected static ?string $modelLabel = 'почтовый адрес';

    protected static ?string $pluralModelLabel = 'почтовые адреса';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    public static function form(Schema $schema): Schema
    {
        return MailingRecipientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MailingRecipientsTable::configure($table);
    }

    // Только список: правки в модалке и таблице
    public static function getPages(): array
    {
        return [
            'index' => ListMailingRecipients::route('/'),
        ];
    }
}
