<?php

namespace App\Filament\Resources\Mailings\Schemas;

use App\Models\MailingRecipient;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RecipientTableSelect
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(MailingRecipient::query()->active()->orderBy('email'))
            ->columns([
                TextColumn::make('email')->label('Адрес')->searchable(),
            ])
            ->defaultPaginationPageOption(25);
    }
}
