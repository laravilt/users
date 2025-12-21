<?php

namespace Laravilt\Users\Resources\Users\Schemas;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Laravilt\Forms\Components\FileUpload;
use Laravilt\Forms\Components\Select;
use Laravilt\Forms\Components\TextInput;
use Laravilt\Schemas\Components\Section;
use Laravilt\Schemas\Schema;

class UserForm
{
    /**
     * Check if we're in create mode based on operation context.
     */
    protected static function isCreateOperation(?string $operation): bool
    {
        return $operation === 'create';
    }

    public static function make(Schema $schema): Schema
    {
        $sections = [];

        // Avatar section - only show when avatar feature is enabled (disabled by default)
        if (config('laravilt-users.features.avatar', false)) {
            $sections[] = Section::make(__('laravilt-users::users.form.avatar_section'))
                ->icon('camera')
                ->description(__('laravilt-users::users.form.avatar_section_description'))
                ->schema([
                    FileUpload::make('avatar')
                        ->label(__('laravilt-users::users.fields.avatar'))
                        ->avatar()
                        ->image()
                        ->collection(config('laravilt-users.avatar.collection', 'avatar'))
                        ->maxSize(2048)
                        ->imageResizeTargetWidth('256')
                        ->imageResizeTargetHeight('256')
                        ->columnSpanFull()
                        ->alignCenter(),
                ])->columns(1);
        }

        // User information section
        $sections[] = Section::make(__('laravilt-users::users.form.user_information'))
            ->icon('user')
            ->description(__('laravilt-users::users.form.user_information_description'))
            ->schema([
                TextInput::make('name')
                    ->label(__('laravilt-users::users.fields.name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label(__('laravilt-users::users.fields.email'))
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ])->columns(2);

        // Password section
        $sections[] = Section::make(__('laravilt-users::users.form.password_section'))
            ->icon('lock')
            ->description(__('laravilt-users::users.form.password_section_description'))
            ->schema([
                TextInput::make('password')
                    ->label(__('laravilt-users::users.fields.password'))
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (?string $operation = null): bool => static::isCreateOperation($operation))
                    ->rules(fn (?string $operation = null): array => static::isCreateOperation($operation) ? ['required', Password::default()] : (filled(request('password')) ? [Password::default()] : []))
                    ->same('password_confirmation'),

                TextInput::make('password_confirmation')
                    ->label(__('laravilt-users::users.fields.password_confirmation'))
                    ->password()
                    ->revealable()
                    ->dehydrated(false)
                    ->required(fn (?string $operation = null): bool => static::isCreateOperation($operation)),
            ])->columns(2);

        // Roles section
        $sections[] = Section::make(__('laravilt-users::users.form.roles_section'))
            ->icon('shield')
            ->description(__('laravilt-users::users.form.roles_section_description'))
            ->schema([
                Select::make('roles')
                    ->label(__('laravilt-users::users.fields.roles'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ])->columns(1);

        return $schema
            ->schema($sections)
            ->columns(1);
    }
}
