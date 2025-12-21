<?php

namespace Laravilt\Users\Resources\Users\Tables;

use Laravilt\Actions\BulkActionGroup;
use Laravilt\Actions\DeleteAction;
use Laravilt\Actions\DeleteBulkAction;
use Laravilt\Actions\EditAction;
use Laravilt\Actions\ViewAction;
use Laravilt\Tables\Columns\ImageColumn;
use Laravilt\Tables\Columns\TextColumn;
use Laravilt\Tables\Enums\PaginationMode;
use Laravilt\Tables\Filters\SelectFilter;
use Laravilt\Tables\Table;
use Laravilt\Users\Actions\ImpersonateAction;

class UsersTable
{
    public static function make(Table $table): Table
    {
        $columns = [
            TextColumn::make('id')
                ->label(__('laravilt-users::users.fields.id'))
                ->searchable()
                ->sortable(),
        ];

        // Only add avatar column if feature is enabled
        if (config('laravilt-users.features.avatar', false)) {
            $columns[] = ImageColumn::make('avatar_url')
                ->label(__('laravilt-users::users.fields.avatar'))
                ->circular()
                ->defaultImageUrl(fn ($state, $record): string => $record ? 'https://ui-avatars.com/api/?name='.urlencode($record->name ?? 'User').'&color=7F9CF5&background=EBF4FF' : 'https://ui-avatars.com/api/?name=U&color=7F9CF5&background=EBF4FF')
                ->size(40);
        }

        $columns[] = TextColumn::make('name')
            ->label(__('laravilt-users::users.fields.name'))
            ->searchable()
            ->sortable();

        $columns[] = TextColumn::make('email')
            ->label(__('laravilt-users::users.fields.email'))
            ->searchable()
            ->sortable()
            ->copyable()
            ->copyMessage(__('laravilt-users::users.messages.email_copied'));

        $columns[] = TextColumn::make('roles.name')
            ->label(__('laravilt-users::users.fields.roles'))
            ->badge()
            ->separator(',')
            ->sortable()
            ->placeholder('-');

        $columns[] = TextColumn::make('email_verified_at')
            ->label(__('laravilt-users::users.fields.email_verified'))
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);

        $columns[] = TextColumn::make('created_at')
            ->label(__('laravilt-users::users.fields.created_at'))
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);

        $columns[] = TextColumn::make('updated_at')
            ->label(__('laravilt-users::users.fields.updated_at'))
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);

        return $table
            ->extremePaginationLinks()
            ->paginationMode(PaginationMode::Simple)
            ->defaultSort('created_at', 'desc')
            ->columns($columns)
            ->filters([
                SelectFilter::make('roles')
                    ->label(__('laravilt-users::users.filters.role'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->recordActions([
                ImpersonateAction::make(),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
