<?php

namespace App\Filament\Resources\Mailings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TableSelect;
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
                    ->default('КП на строительные материалы для  отдела снабжения, закупок, ПТО от 10.09.2026 - РешениеСтройДизайн')
                    ->columnSpanFull(),
                RichEditor::make('body')
                    ->label('Тело письма')
                    ->helperText('Вставляется в шаблон письма; подпись добавляется автоматически.')
                    ->required()
                    ->default('<p>Здравствуйте!</p><p>Направляем вам актуальное коммерческое предложение на строительные материалы — ознакомиться с ним можно в прикреплённом файле.</p>')
                    ->columnSpanFull(),
                FileUpload::make('attachments')
                    ->label('Прикреплённые файлы')
                    ->multiple()
                    ->disk('local')
                    ->directory('mailing-attachments')
                    ->preserveFilenames()
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
                TableSelect::make('recipient_ids')
                    ->label('Выберите адреса')
                    ->multiple()
                    ->tableConfiguration(RecipientTableSelect::class)
                    ->visible(fn (Get $get): bool => ! $get('send_to_all'))
                    ->required(fn (Get $get): bool => ! $get('send_to_all'))
                    ->columnSpanFull(),
            ]);
    }
}
