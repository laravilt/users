# Installation

This guide covers the installation process for Laravilt Users.

## Requirements

Before installing, ensure you have:

- PHP 8.3 or higher
- Laravel 12 or higher
- Laravilt 1.0 or higher
- Composer 2+

## Installation Steps

### Step 1: Install via Composer

```bash
composer require laravilt/users
```

The service provider is auto-discovered and will register automatically.

### Step 2: Run Migrations

The package uses Spatie Laravel Permission which requires its migrations:

```bash
php artisan migrate
```

This will create the following tables:
- `roles` - Stores role definitions
- `permissions` - Stores permission definitions
- `model_has_roles` - Pivot table for user-role relationships
- `model_has_permissions` - Pivot table for user-permission relationships
- `role_has_permissions` - Pivot table for role-permission relationships

### Step 3: Install the Plugin

Run the installation command to set up default permissions and roles:

```bash
php artisan laravilt:users:install
```

This command will:
- Publish the configuration file
- Create default permissions for all resources
- Create default roles (super_admin, admin, user)

### Step 4: Configure User Model

Add the required traits to your User model:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravilt\Users\Concerns\HasRolesAndPermissions;
use Laravilt\Users\Concerns\HasAvatar;

class User extends Authenticatable
{
    use HasRolesAndPermissions;
    use HasAvatar;  // Optional, only if using avatars

    // ... rest of your model
}
```

### Step 5: Register with Panel

Register the plugin in your panel provider:

```php
<?php

namespace App\Providers\Laravilt;

use Laravilt\Panel\PanelProvider;
use Laravilt\Panel\Panel;
use Laravilt\Users\UsersPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->plugins([
                UsersPlugin::make(),
            ]);
    }
}
```

## Optional Configuration

### Enable Features

Features are opt-in by default:

```php
UsersPlugin::make()
    ->avatar()           // Enable user avatars
    ->impersonation()    // Enable user impersonation
```

### Customize Navigation

```php
UsersPlugin::make()
    ->navigationGroup('Settings')
    ->navigationSort(10)
```

### Publish Configuration

To customize the configuration:

```bash
php artisan vendor:publish --tag=laravilt-users-config
```

This publishes `config/laravilt-users.php`.

## Verification

After installation, verify everything is working:

1. Visit your admin panel (e.g., `/admin`)
2. You should see "Users" and "Roles" in the navigation
3. Try creating a new user
4. Try assigning roles to users

## Troubleshooting

### Permissions Not Working

Ensure you've run the setup permissions command:

```bash
php artisan laravilt:users:setup-permissions
```

### Missing Traits

If you see errors about missing methods, ensure your User model uses the correct traits:

```php
use HasRolesAndPermissions;
use HasAvatar;  // If using avatars
```

### Migration Issues

If migrations fail, ensure Spatie Permission is properly installed:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

## Next Steps

- [Configuration](configuration.md) - Configure the plugin
- [Users Resource](users.md) - Learn about user management
- [Roles Resource](roles.md) - Learn about role management
