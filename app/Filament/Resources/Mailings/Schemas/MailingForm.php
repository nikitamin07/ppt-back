<?php

namespace App\Filament\Resources\Mailings\Schemas;

use App\Models\MailingRecipient;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class MailingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('subject')
                    ->label('Тема письма')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                RichEditor::make('body')
                    ->label('Тело письма')
                    ->helperText('Вставляется в шаблон письма; подпись добавляется автоматически.')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('attachments')
                    ->label('Прикреплённые файлы')
                    ->multiple()
                    ->disk('local')
                    ->directory('mailing-attachments')
                    ->downloadable()
                    ->openable()
                    ->reorderable()
                    ->columnSpanFull(),
                Toggle::make('send_to_all')
                    ->label('Разослать всем')
                    ->helperText('Включено — по всем активным адресам базы. Выключено — только выбранным ниже.')
                    ->default(true)
                    ->live()
                    ->columnSpanFull(),
                Select::make('recipient_ids')
                    ->label('Выберите адреса')
                    ->multiple()
                    ->searchable()
                    // Серверный поиск: ~900 адресов не грузим
                    ->getSearchResultsUsing(fn (string $search): array => MailingRecipient::query()->active()
                        ->where('email', 'ilike', "%{$search}%")->orderBy('email')->limit(50)->pluck('email', 'id')->all())
                    ->getOptionLabelsUsing(fn (array $values): array => MailingRecipient::query()
                        ->whereIn('id', $values)->pluck('email', 'id')->all())
                    ->visible(fn (Get $get): bool => ! $get('send_to_all'))
                    ->required(fn (Get $get): bool => ! $get('send_to_all'))
                    ->columnSpanFull(),
            ]);
    }
}
