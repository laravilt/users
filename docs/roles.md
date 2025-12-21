# Roles Resource

This guide covers the Role management features in Laravilt Users.

## Overview

The RoleResource provides complete CRUD operations for managing roles including:

- Creating new roles
- Editing role permissions
- Viewing role details
- Deleting roles (except system roles)

## Table Columns

The roles table displays:

| Column | Description |
|--------|-------------|
| ID | Role identifier |
| Name | Role name |
| Guard | Authentication guard |
| Permissions | Number of permissions |
| Users | Number of users with this role |
| Created At | Creation date (toggleable) |

## Table Features

### Filtering

Filter roles by:
- Guard name

### Sorting

Sort by:
- ID
- Name
- Created At

### Actions

- **View** - View role details
- **Edit** - Edit role and permissions
- **Delete** - Delete role (disabled for system roles)

### Bulk Actions

- **Delete Selected** - Delete multiple roles

## Form Fields

### Role Information Section

```php
Section::make('Role Information')
    ->schema([
        TextInput::make('name')
            ->required()
            ->unique(ignoreRecord: true)
            ->maxLength(255),

        Select::make('guard_name')
            ->options(['web' => 'Web', 'api' => 'API'])
            ->default('web')
            ->required(),
    ])
```

### Permissions Section

```php
Section::make('Permissions')
    ->schema([
        CheckboxList::make('permissions')
            ->relationship('permissions', 'name')
            ->groupByResource()
            ->columns(4)
            ->searchable()
            ->bulkToggleable(),
    ])
```

## Permission Groups

Permissions are grouped by resource for easier management:

```
Users
├── □ View Any
├── □ View
├── □ Create
├── □ Update
├── □ Delete
└── □ Impersonate

Roles
├── □ View Any
├── □ View
├── □ Create
├── □ Update
└── □ Delete

... (other resources)
```

Each group has a "Select All" toggle to quickly assign all permissions.

## View (Infolist)

The role view page displays:

### Role Information
- Role name
- Guard name

### Statistics
- Number of permissions
- Number of users

### Permissions
- List of all assigned permissions grouped by resource

## System Roles

System roles cannot be deleted:

- `super_admin` - Has all permissions
- `admin` - Administrative role
- `user` - Basic user role

## Customization

### Extending the Resource

```php
<?php

namespace App\Laravilt\Resources;

use Laravilt\Users\Resources\Roles\RoleResource as BaseRoleResource;

class RoleResource extends BaseRoleResource
{
    // Override methods as needed
}
```

### Custom Permission Actions

Add custom permission actions:

```php
protected array $permissionActions = [
    'view_any',
    'view',
    'create',
    'update',
    'delete',
    'restore',
    'force_delete',
    'export',      // Custom action
    'import',      // Custom action
];
```

## Permissions

The following permissions control access:

| Permission | Description |
|------------|-------------|
| `view_any_role` | List roles |
| `view_role` | View a role |
| `create_role` | Create roles |
| `update_role` | Edit roles |
| `delete_role` | Delete roles |

## Translations

All labels are translatable:

```php
// Field labels
__('laravilt-users::users.fields.name')
__('laravilt-users::users.fields.guard_name')
__('laravilt-users::users.fields.permissions')
__('laravilt-users::users.fields.permissions_count')
__('laravilt-users::users.fields.users_count')

// Form sections
__('laravilt-users::users.form.role_information')
__('laravilt-users::users.form.permissions_section')

// Messages
__('laravilt-users::users.messages.cannot_delete_system')
```

## CheckboxList Features

The permission selector uses CheckboxList with these features:

### Searchable

Search permissions by name:
```php
->searchable()
```

### Bulk Toggle

Select/deselect all permissions:
```php
->bulkToggleable()
```

### Group by Resource

Group permissions by resource:
```php
->groupByResource()
```

### Columns

Display in multiple columns:
```php
->columns(4)
```

## Guards

Roles can be scoped to specific authentication guards:

```php
// Web guard (default)
$role = Role::create(['name' => 'editor', 'guard_name' => 'web']);

// API guard
$role = Role::create(['name' => 'api-user', 'guard_name' => 'api']);
```

Filter roles by guard in the table.

## Next Steps

- [Permissions](permissions.md) - Set up permissions
- [Users Resource](users.md) - Manage users
- [Impersonation](impersonation.md) - User impersonation
