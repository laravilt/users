<?php

namespace Laravilt\Users\Resources\Users;

use Laravilt\Panel\Resources\Resource;
use Laravilt\Schemas\Schema;
use Laravilt\Tables\Table;
use Laravilt\Users\Resources\Users\Pages\CreateUser;
use Laravilt\Users\Resources\Users\Pages\EditUser;
use Laravilt\Users\Resources\Users\Pages\ListUsers;
use Laravilt\Users\Resources\Users\Pages\ViewUser;
use Laravilt\Users\Resources\Users\Schemas\UserForm;
use Laravilt\Users\Resources\Users\Schemas\UserInfolist;
use Laravilt\Users\Resources\Users\Tables\UsersTable;
use Laravilt\Users\UsersPlugin;

class UserResource extends Resource
{
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'Users';

    public static function getModel(): string
    {
        return app(UsersPlugin::class)->getUserModel();
    }

    public static function getNavigationGroup(): ?string
    {
        return app(UsersPlugin::class)->getNavigationGroup();
    }

    public static function getNavigationSort(): int
    {
        return config('laravilt-users.navigation.user.sort', 1);
    }

    public static function getNavigationLabel(): string
    {
        return __('laravilt-users::users.navigation.users');
    }

    public static function getModelLabel(): string
    {
        return __('laravilt-users::users.resource.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('laravilt-users::users.resource.users');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::make($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::make($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::make($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
