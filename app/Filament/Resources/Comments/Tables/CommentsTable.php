<?php

namespace App\Filament\Resources\Comments\Tables;

use App\Models\Comment;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Дата')
                    ->date('d.m.Y')
                    ->sortable(),
                TextColumn::make('author')
                    ->label('Автор')
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label('Товар')
                    ->limit(35)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rating')
                    ->label('Оценка')
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state))
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('body')
                    ->label('Текст')
                    ->limit(90)
                    ->wrap(),
                // Свитчер модерации: переключает публикацию отзыва прямо в таблице
                ToggleColumn::make('is_approved')
                    ->label('Опубликован')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->label('Товар')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('rating')
                    ->label('Оценка')
                    ->options([5 => '5', 4 => '4', 3 => '3', 2 => '2', 1 => '1']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    self::setApprovedBulkAction('approve', 'Опубликовать', true, Heroicon::OutlinedCheck, 'success'),
                    self::setApprovedBulkAction('unapprove', 'Снять с публикации', false, Heroicon::OutlinedEyeSlash, 'gray'),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /** Массовая смена статуса модерации: и «Опубликовать», и «Снять с публикации». */
    private static function setApprovedBulkAction(string $name, string $label, bool $approved, Heroicon $icon, string $color): BulkAction
    {
        return BulkAction::make($name)
            ->label($label)
            ->icon($icon)
            ->color($color)
            ->requiresConfirmation()
            ->deselectRecordsAfterCompletion()
            ->action(fn (Collection $records) => Comment::whereKey($records->modelKeys())->update(['is_approved' => $approved]));
    }
}
