<?php

namespace Laravilt\Users\Resources\Roles\Schemas;

use Laravilt\Forms\Components\CheckboxList;
use Laravilt\Forms\Components\Select;
use Laravilt\Forms\Components\TextInput;
use Laravilt\Schemas\Components\Section;
use Laravilt\Schemas\Schema;

class RoleForm
{
    public static function make(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('laravilt-users::users.form.role_information'))
                    ->icon('shield')
                    ->description(__('laravilt-users::users.form.role_information_description'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('laravilt-users::users.fields.name'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('guard_name')
                            ->label(__('laravilt-users::users.fields.guard_name'))
                            ->options(collect(config('auth.guards'))->keys()->mapWithKeys(fn ($guard) => [$guard => ucfirst($guard)])->toArray())
                            ->default(config('laravilt-users.guard_name', 'web'))
                            ->required(),
                    ])->columns(2),

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
                            ->searchable()
                            ->bulkToggleable()
                            ->gridDirection('row'),
                    ])->columns(1),
            ])->columns(1);
    }
}
