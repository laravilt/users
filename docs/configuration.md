# Configuration

This guide covers all configuration options for Laravilt Users.

## Configuration File

Publish the configuration file:

```bash
php artisan vendor:publish --tag=laravilt-users-config
```

This creates `config/laravilt-users.php`:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Guard Name
    |--------------------------------------------------------------------------
    |
    | The default guard to use for permissions.
    |
    */
    'guard_name' => env('LARAVILT_USERS_GUARD', 'web'),

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable optional features. All features are opt-in.
    |
    */
    'features' => [
        'impersonation' => false,
        'avatar' => false,
        'teams' => false,
        'email_verification' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    |
    | Configure how the plugin appears in the navigation.
    |
    */
    'navigation' => [
        'group' => 'Users & Roles',
        'sort' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Impersonation
    |--------------------------------------------------------------------------
    |
    | Configure impersonation behavior.
    |
    */
    'impersonation' => [
        'redirect_to' => '/admin',
        'leave_redirect_to' => '/admin',
    ],
];
```

## Plugin Configuration

You can also configure the plugin directly in your panel provider:

```php
use Laravilt\Users\UsersPlugin;

UsersPlugin::make()
    // Navigation
    ->navigationGroup('Settings')
    ->navigationSort(10)

    // Features
    ->avatar()
    ->impersonation()
```

## Configuration Options

### Guard Name

The authentication guard to use for permissions:

```php
'guard_name' => 'web',
```

Common values:
- `web` - Default web guard
- `api` - API guard
- `admin` - Custom admin guard

### Features

#### Impersonation

Enable the ability to login as other users:

```php
'features' => [
    'impersonation' => true,
],

// Or via plugin
UsersPlugin::make()->impersonation()
```

When enabled:
- An "Impersonate" action appears on the users table
- Admins can login as other users
- A banner shows when impersonating

#### Avatar

Enable user avatar support:

```php
'features' => [
    'avatar' => true,
],

// Or via plugin
UsersPlugin::make()->avatar()
```

When enabled:
- Avatar upload field appears in user form
- Avatar column shows in users table
- Falls back to UI Avatars when no avatar is set

#### Teams

Enable team support (for multi-tenant applications):

```php
'features' => [
    'teams' => true,
],
```

#### Email Verification

Track email verification status:

```php
'features' => [
    'email_verification' => true,
],
```

### Navigation

#### Group

Set the navigation group:

```php
'navigation' => [
    'group' => 'Users & Roles',
],

// Or via plugin
UsersPlugin::make()->navigationGroup('Settings')
```

Use translation key:
```php
->navigationGroup(__('laravilt-users::users.navigation.group'))
```

#### Sort Order

Set the navigation sort order:

```php
'navigation' => [
    'sort' => 1,
],

// Or via plugin
UsersPlugin::make()->navigationSort(10)
```

### Impersonation Settings

#### Redirect After Impersonation

Where to redirect after starting impersonation:

```php
'impersonation' => [
    'redirect_to' => '/admin',
],
```

#### Redirect After Leaving

Where to redirect after stopping impersonation:

```php
'impersonation' => [
    'leave_redirect_to' => '/admin',
],
```

## Environment Variables

You can use environment variables for sensitive settings:

```env
LARAVILT_USERS_GUARD=web
LARAVILT_USERS_IMPERSONATION=false
LARAVILT_USERS_AVATAR=false
```

Then in config:

```php
'guard_name' => env('LARAVILT_USERS_GUARD', 'web'),
'features' => [
    'impersonation' => env('LARAVILT_USERS_IMPERSONATION', false),
    'avatar' => env('LARAVILT_USERS_AVATAR', false),
],
```

## Configuration Priority

Settings are applied in this order (later overrides earlier):

1. Default values in config file
2. Environment variables
3. Plugin methods (e.g., `->impersonation()`)

## Example Configurations

### Basic Setup

```php
UsersPlugin::make()
```

### Full Featured

```php
UsersPlugin::make()
    ->navigationGroup('Administration')
    ->navigationSort(1)
    ->avatar()
    ->impersonation()
```

### Custom Navigation

```php
UsersPlugin::make()
    ->navigationGroup('System')
    ->navigationSort(99)
```

## Next Steps

- [Users Resource](users.md) - Configure user management
- [Roles Resource](roles.md) - Configure role management
- [Impersonation](impersonation.md) - Set up impersonation
