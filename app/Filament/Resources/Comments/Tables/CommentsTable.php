<?php

namespace App\Filament\Resources\Comments\Tables;

use App\Models\Comment;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
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
                self::toggleApprovedAction(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    self::approveBulkAction(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /** Основное действие модерации: одной кнопкой пустить отзыв на сайт или снять с него. */
    private static function toggleApprovedAction(): Action
    {
        return Action::make('toggleApproved')
            ->label(fn (Comment $record): string => $record->is_approved ? 'Снять с публикации' : 'Опубликовать')
            ->icon(fn (Comment $record): Heroicon => $record->is_approved ? Heroicon::OutlinedEyeSlash : Heroicon::OutlinedCheck)
            ->color(fn (Comment $record): string => $record->is_approved ? 'gray' : 'success')
            ->action(fn (Comment $record) => $record->update(['is_approved' => ! $record->is_approved]));
    }

    private static function approveBulkAction(): BulkAction
    {
        return BulkAction::make('approve')
            ->label('Опубликовать')
            ->icon(Heroicon::OutlinedCheck)
            ->color('success')
            ->requiresConfirmation()
            ->deselectRecordsAfterCompletion()
            ->action(fn (Collection $records) => Comment::whereKey($records->modelKeys())->update(['is_approved' => true]));
    }
}
