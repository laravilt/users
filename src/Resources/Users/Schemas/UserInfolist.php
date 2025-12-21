<?php

namespace Laravilt\Users\Resources\Users\Schemas;

use Laravilt\Infolists\Entries\ImageEntry;
use Laravilt\Infolists\Entries\TextEntry;
use Laravilt\Schemas\Components\Section;
use Laravilt\Schemas\Schema;

class UserInfolist
{
    public static function make(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('laravilt-users::users.form.user_information'))
                    ->icon('user')
                    ->schema([
                        ImageEntry::make('avatar_url')
                            ->label(__('laravilt-users::users.fields.avatar'))
                            ->circular()
                            ->defaultImageUrl(fn ($record): string => $record ? 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=7F9CF5&background=EBF4FF' : '')
                            ->size(80)
                            ->visible(fn (): bool => config('laravilt-users.features.avatar', false)),

                        TextEntry::make('name')
                            ->label(__('laravilt-users::users.fields.name')),

                        TextEntry::make('email')
                            ->label(__('laravilt-users::users.fields.email'))
                            ->copyable(),

                        TextEntry::make('email_verified_at')
                            ->label(__('laravilt-users::users.fields.email_verified'))
                            ->dateTime()
                            ->placeholder(__('laravilt-users::users.messages.not_verified')),
                    ])->columns(2),

                Section::make(__('laravilt-users::users.form.roles_section'))
                    ->icon('shield')
                    ->schema([
                        TextEntry::make('roles.name')
                            ->label(__('laravilt-users::users.fields.roles'))
                            ->badge()
                            ->separator(',')
                            ->placeholder(__('laravilt-users::users.messages.no_roles')),
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
