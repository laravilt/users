<?php

namespace Laravilt\Users\Resources\Roles;

use Laravilt\Panel\Resources\Resource;
use Laravilt\Schemas\Schema;
use Laravilt\Tables\Table;
use Laravilt\Users\Resources\Roles\Pages\CreateRole;
use Laravilt\Users\Resources\Roles\Pages\EditRole;
use Laravilt\Users\Resources\Roles\Pages\ListRoles;
use Laravilt\Users\Resources\Roles\Pages\ViewRole;
use Laravilt\Users\Resources\Roles\Schemas\RoleForm;
use Laravilt\Users\Resources\Roles\Schemas\RoleInfolist;
use Laravilt\Users\Resources\Roles\Tables\RolesTable;
use Laravilt\Users\UsersPlugin;

class RoleResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'Shield';

    public static function getModel(): string
    {
        return config('laravilt-users.role_model', \Spatie\Permission\Models\Role::class);
    }

    public static function getNavigationGroup(): ?string
    {
        return app(UsersPlugin::class)->getNavigationGroup();
    }

    public static function getNavigationSort(): int
    {
        return config('laravilt-users.navigation.role.sort', 2);
    }

    public static function getNavigationLabel(): string
    {
        return __('laravilt-users::users.navigation.roles');
    }

    public static function getModelLabel(): string
    {
        return __('laravilt-users::users.resource.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('laravilt-users::users.resource.roles');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return RoleForm::make($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoleInfolist::make($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::make($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'view' => ViewRole::route('/{record}'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
