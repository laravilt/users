# Permissions

This guide covers the permission system in Laravilt Users.

## Overview

Laravilt Users uses [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) for RBAC (Role-Based Access Control).

## Setting Up Permissions

### Auto-Generate Permissions

Run the setup command to generate permissions for all resources:

```bash
php artisan laravilt:secure
```

This creates permissions in the format: `{action}_{resource}`

Example permissions created:
- `view_any_user`
- `view_user`
- `create_user`
- `update_user`
- `delete_user`

### Permission Actions

Default permission actions:

| Action | Description |
|--------|-------------|
| `view_any` | List/index records |
| `view` | View a single record |
| `create` | Create new records |
| `update` | Edit existing records |
| `delete` | Delete records |
| `restore` | Restore soft-deleted records |
| `force_delete` | Permanently delete records |
| `replicate` | Duplicate records |
| `reorder` | Reorder records |
| `impersonate` | Impersonate users |

## User Model Setup

Add the trait to your User model:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravilt\Users\Concerns\HasRolesAndPermissions;

class User extends Authenticatable
{
    use HasRolesAndPermissions;
}
```

This trait provides:
- Spatie Permission integration
- Helper methods for permission checks
- Role management methods

## Working with Permissions

### Assigning Permissions

```php
// Assign permission to user
$user->givePermissionTo('edit_articles');

// Assign multiple permissions
$user->givePermissionTo(['edit_articles', 'delete_articles']);

// Remove permission
$user->revokePermissionTo('edit_articles');
```

### Assigning Roles

```php
// Assign role
$user->assignRole('editor');

// Assign multiple roles
$user->assignRole(['editor', 'writer']);

// Remove role
$user->removeRole('editor');
```

### Checking Permissions

```php
// Check single permission
if ($user->can('edit_articles')) {
    // User can edit articles
}

// Check multiple permissions (all required)
if ($user->hasAllPermissions(['edit_articles', 'publish_articles'])) {
    // User has both permissions
}

// Check multiple permissions (any)
if ($user->hasAnyPermission(['edit_articles', 'publish_articles'])) {
    // User has at least one permission
}
```

### Checking Roles

```php
// Check single role
if ($user->hasRole('admin')) {
    // User is admin
}

// Check multiple roles (any)
if ($user->hasAnyRole(['admin', 'super_admin'])) {
    // User has at least one role
}

// Check multiple roles (all required)
if ($user->hasAllRoles(['writer', 'editor'])) {
    // User has both roles
}
```

## Resource Authorization

### Using Shield Trait

Resources can use the `HasResourceAuthorization` trait for automatic policy generation:

```php
<?php

namespace App\Laravilt\Resources;

use Laravilt\Panel\Resources\Resource;
use Laravilt\Shield\Concerns\HasResourceAuthorization;

class ArticleResource extends Resource
{
    use HasResourceAuthorization;

    // Permission prefix defaults to resource slug
    // e.g., 'view_any_article', 'create_article'
}
```

### Custom Permission Prefix

```php
class ArticleResource extends Resource
{
    use HasResourceAuthorization;

    protected static function getPermissionPrefix(): string
    {
        return 'post'; // Uses 'view_any_post', 'create_post', etc.
    }
}
```

## Default Roles

The installation creates these default roles:

### Super Admin
- Has all permissions (wildcard)
- Cannot be impersonated
- Cannot be deleted

### Admin
- Administrative access
- Can manage users and roles
- Can impersonate other users

### User
- Basic user role
- Limited permissions

## Permission Groups

In the role form, permissions are grouped by resource:

```
┌─────────────────────────────────────┐
│ Users                          [✓] │
├─────────────────────────────────────┤
│ ✓ View Any   ✓ View   ✓ Create    │
│ ✓ Update     ✓ Delete  □ Impersonate│
└─────────────────────────────────────┘
```

Each group has a "Select All" toggle.

## Translations

Permission labels are translated:

```php
// In lang/en/users.php
'permissions' => [
    'view_any' => 'View Any',
    'view' => 'View',
    'create' => 'Create',
    'update' => 'Update',
    'delete' => 'Delete',
    'restore' => 'Restore',
    'force_delete' => 'Force Delete',
    'replicate' => 'Replicate',
    'reorder' => 'Reorder',
    'impersonate' => 'Impersonate',
],
```

## Caching

Permissions are cached for performance. Clear the cache when needed:

```php
// Clear permission cache
app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
```

Or via artisan:

```bash
php artisan permission:cache-reset
```

## Best Practices

### 1. Use Roles for Groups of Permissions

```php
// Good - role-based
$user->assignRole('editor');

// Avoid - individual permissions
$user->givePermissionTo(['edit_articles', 'delete_articles', ...]);
```

### 2. Check Permissions, Not Roles

```php
// Good - permission-based check
if ($user->can('edit_articles')) { ... }

// Avoid - role-based check
if ($user->hasRole('editor')) { ... }
```

### 3. Use Super Admin for Full Access

The `super_admin` role has all permissions automatically.

### 4. Cache Permissions in Production

```bash
php artisan permission:cache-reset
```

## Next Steps

- [Roles Resource](roles.md) - Manage roles
- [Users Resource](users.md) - Manage users
- [Impersonation](impersonation.md) - User impersonation
