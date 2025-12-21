<?php

namespace Laravilt\Users\Resources\Roles\Tables;

use Laravilt\Actions\BulkActionGroup;
use Laravilt\Actions\DeleteAction;
use Laravilt\Actions\DeleteBulkAction;
use Laravilt\Actions\EditAction;
use Laravilt\Actions\ViewAction;
use Laravilt\Tables\Columns\TextColumn;
use Laravilt\Tables\Enums\PaginationMode;
use Laravilt\Tables\Filters\SelectFilter;
use Laravilt\Tables\Table;

class RolesTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->extremePaginationLinks()
            ->paginationMode(PaginationMode::Simple)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('laravilt-users::users.fields.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('guard_name')
                    ->label(__('laravilt-users::users.fields.guard_name'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('permissions_count')
                    ->label(__('laravilt-users::users.fields.permissions_count'))
                    ->counts('permissions')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('users_count')
                    ->label(__('laravilt-users::users.fields.users_count'))
                    ->counts('users')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('laravilt-users::users.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('laravilt-users::users.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('guard_name')
                    ->label(__('laravilt-users::users.filters.guard'))
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ]),
            ])
            ->recordActions([
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
