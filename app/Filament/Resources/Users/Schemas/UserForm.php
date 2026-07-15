<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Имя')
                    ->required(),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                // Хеширует каст 'password' => 'hashed' на модели.
                // При редактировании пустое поле = оставить старый пароль.
                TextInput::make('password')
                    ->label('Пароль')
                    ->helperText('При редактировании: оставьте пустым, чтобы не менять')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                Select::make('role')
                    ->label('Роль')
                    ->options([
                        'admin' => 'администратор',
                        'manager' => 'менеджер',
                    ])
                    ->required()
                    ->default('manager'),
            ]);
    }
}
