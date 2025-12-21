<?php

namespace Laravilt\Users\Resources\Roles\Schemas;

use Laravilt\Forms\Components\CheckboxList;
use Laravilt\Infolists\Entries\TextEntry;
use Laravilt\Schemas\Components\Section;
use Laravilt\Schemas\Schema;

class RoleInfolist
{
    public static function make(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('laravilt-users::users.form.role_information'))
                    ->icon('shield')
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('laravilt-users::users.fields.name')),

                        TextEntry::make('guard_name')
                            ->label(__('laravilt-users::users.fields.guard_name'))
                            ->badge(),

                        TextEntry::make('users_count')
                            ->label(__('laravilt-users::users.fields.users_count'))
                            ->state(fn ($record): int => $record->users()->count())
                            ->badge()
                            ->color('success'),
                    ])->columns(3),

                Section::make(__('laravilt-users::users.form.permissions_section'))
                    ->icon('key')
                    ->description(__('laravilt-users::users.form.permissions_section_description'))
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label(__('laravilt-users::users.fields.permissions'))
                            ->relationship('permissions', 'name')
                            ->groupByResource()
                            ->defaultGroup(config('laravilt-users.guard_name', 'web'))
                            ->columns(4)
                            ->gridDirection('row')
                            ->disabled(),
                    ])->columns(1),

                Section::make(__('laravilt-users::users.form.timestamps'))
                    ->icon('clock')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('laravilt-users::users.fields.created_at'))
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label(__('laravilt-users::users.fields.updated_at'))
                            ->dateTime(),
                    ])->columns(2),
            ]);
    }
}
