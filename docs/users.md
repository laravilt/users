# Users Resource

This guide covers the User management features in Laravilt Users.

## Overview

The UserResource provides complete CRUD operations for managing users including:

- Creating new users
- Editing user details
- Viewing user profiles
- Deleting users
- Assigning roles
- Managing avatars
- Tracking email verification

## Table Columns

The users table displays:

| Column | Description |
|--------|-------------|
| ID | User identifier |
| Avatar | User avatar (if enabled) |
| Name | User's full name |
| Email | User's email address |
| Roles | Assigned roles (badges) |
| Email Verified | Verification timestamp |
| Created At | Creation date (toggleable) |
| Updated At | Last update date (toggleable) |

## Table Features

### Searching

Users can be searched by:
- Name
- Email

### Filtering

Filter users by:
- Role (multi-select)

### Sorting

Sort by:
- ID
- Name
- Email
- Created At
- Updated At

### Actions

- **View** - View user details
- **Edit** - Edit user information
- **Delete** - Delete user
- **Impersonate** - Login as user (if enabled)

### Bulk Actions

- **Delete Selected** - Delete multiple users

## Form Fields

### User Information Section

```php
Section::make('User Information')
    ->schema([
        TextInput::make('name')
            ->required()
            ->maxLength(255),

        TextInput::make('email')
            ->email()
            ->required()
            ->unique(ignoreRecord: true),
    ])
```

### Avatar Section (Optional)

When avatar feature is enabled:

```php
Section::make('Avatar')
    ->schema([
        FileUpload::make('avatar')
            ->image()
            ->avatar()
            ->directory('avatars'),
    ])
```

### Password Section

```php
Section::make('Password')
    ->schema([
        TextInput::make('password')
            ->password()
            ->confirmed()
            ->required(fn (string $operation) => $operation === 'create'),

        TextInput::make('password_confirmation')
            ->password()
            ->required(fn (string $operation) => $operation === 'create'),
    ])
```

### Roles Section

```php
Section::make('Roles')
    ->schema([
        CheckboxList::make('roles')
            ->relationship('roles', 'name')
            ->columns(2),
    ])
```

## View (Infolist)

The user view page displays:

### User Information
- Avatar (with fallback)
- Name
- Email
- Email Verified status

### Roles
- List of assigned roles as badges

### Timestamps
- Created At
- Updated At

## Customization

### Extending the Resource

Create your own UserResource that extends the base:

```php
<?php

namespace App\Laravilt\Resources;

use Laravilt\Users\Resources\Users\UserResource as BaseUserResource;

class UserResource extends BaseUserResource
{
    // Override methods as needed
}
```

### Custom Form Fields

Override the form method:

```php
public static function form(Schema $schema): Schema
{
    return parent::form($schema)
        ->schema([
            // Add custom fields
            TextInput::make('phone')
                ->tel(),
        ]);
}
```

### Custom Table Columns

Override the table method:

```php
public static function table(Table $table): Table
{
    return parent::table($table)
        ->columns([
            // Add custom columns
            TextColumn::make('phone'),
        ]);
}
```

## Permissions

The following permissions control access:

| Permission | Description |
|------------|-------------|
| `view_any_user` | List users |
| `view_user` | View a user |
| `create_user` | Create users |
| `update_user` | Edit users |
| `delete_user` | Delete users |
| `impersonate_user` | Impersonate users |

## Translations

All labels are translatable:

```php
// Field labels
__('laravilt-users::users.fields.name')
__('laravilt-users::users.fields.email')
__('laravilt-users::users.fields.password')
__('laravilt-users::users.fields.roles')
__('laravilt-users::users.fields.avatar')

// Form sections
__('laravilt-users::users.form.user_information')
__('laravilt-users::users.form.password_section')
__('laravilt-users::users.form.roles_section')
__('laravilt-users::users.form.avatar_section')

// Messages
__('laravilt-users::users.messages.created')
__('laravilt-users::users.messages.updated')
__('laravilt-users::users.messages.deleted')
```

## Avatar Feature

When avatar is enabled:

### Fallback Avatar

If no avatar is uploaded, a fallback is generated using UI Avatars:

```
https://ui-avatars.com/api/?name=John+Doe&color=7F9CF5&background=EBF4FF
```

### HasAvatar Trait

Add to your User model:

```php
use Laravilt\Users\Concerns\HasAvatar;

class User extends Authenticatable
{
    use HasAvatar;

    // Optionally override the avatar attribute name
    protected string $avatarAttribute = 'avatar';
}
```

### Avatar URL

Get the avatar URL:

```php
$user->avatar_url  // Returns avatar URL or fallback
```

## Next Steps

- [Roles Resource](roles.md) - Manage roles
- [Permissions](permissions.md) - Set up permissions
- [Impersonation](impersonation.md) - User impersonation
