<?php

namespace App\Filament\Resources\MailingRecipients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class MailingRecipientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('email')
            ->columns([
                TextColumn::make('index')
                    ->label('№')
                    ->rowIndex(),
                // Адрес правится прямо в строке
                TextInputColumn::make('email')
                    ->label('Почтовый адрес')
                    ->searchable()
                    ->sortable()
                    ->rules(fn ($record): array => [
                        'required',
                        'email',
                        'max:255',
                        Rule::unique('mailing_recipients', 'email')->ignore($record),
                    ]),
                ToggleColumn::make('is_active')
                    ->label('Активен')
                    ->sortable(),
                TextColumn::make('error')
                    ->label('Ошибка доставки')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
