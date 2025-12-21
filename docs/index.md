# Laravilt Users Documentation

Complete User and Role management plugin for Laravilt with full RBAC system and impersonation support.

## Table of Contents

1. [Installation](installation.md)
2. [Configuration](configuration.md)
3. [Users Resource](users.md)
4. [Roles Resource](roles.md)
5. [Permissions](permissions.md)
6. [Impersonation](impersonation.md)

## Overview

Laravilt Users provides a complete user management solution including:

- **User Management** - Full CRUD operations for users with avatars, roles, and email verification
- **Role Management** - Complete RBAC system with permissions grouped by resource
- **Impersonation** - Login as any user for debugging and support purposes
- **Localization** - Full English and Arabic translations with RTL support

## Quick Start

### 1. Install the Package

```bash
composer require laravilt/users
```

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Install the Plugin

```bash
php artisan laravilt:users:install
```

### 4. Register with Panel

```php
use Laravilt\Users\UsersPlugin;

$panel->plugins([
    UsersPlugin::make()
        ->avatar()
        ->impersonation(),
]);
```

## Features

### User Resource
- Create, edit, view, and delete users
- Avatar upload with fallback to UI Avatars
- Password management
- Role assignment
- Email verification tracking

### Role Resource
- Create and manage roles
- Assign permissions to roles
- Permissions grouped by resource
- Bulk permission selection

### Impersonation
- Login as any user (opt-in feature)
- Session preservation during impersonation
- Visual banner when impersonating
- Security controls (cannot impersonate self or super admins)

### Translations
- Full English translations
- Full Arabic translations
- RTL layout support

## System Requirements

- PHP 8.3+
- Laravel 12+
- Laravilt 1.0+
- Spatie Laravel Permission 6.0+

## Support

- GitHub Issues: [github.com/laravilt/users](https://github.com/laravilt/users)
- Documentation: [docs.laravilt.com](https://docs.laravilt.com)

## Next Steps

- [Installation Guide](installation.md) - Detailed installation instructions
- [Configuration](configuration.md) - Configure the plugin
- [Users Resource](users.md) - User management features
- [Impersonation](impersonation.md) - Set up user impersonation
