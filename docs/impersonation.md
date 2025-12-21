# Impersonation

This guide covers the user impersonation feature in Laravilt Users.

## Overview

User impersonation allows administrators to login as another user for debugging, support, or testing purposes. The original session is preserved and can be restored at any time.

## Enabling Impersonation

Impersonation is an opt-in feature. Enable it in your panel:

```php
use Laravilt\Users\UsersPlugin;

$panel->plugins([
    UsersPlugin::make()
        ->impersonation(),
]);
```

Or via configuration:

```php
// config/laravilt-users.php
'features' => [
    'impersonation' => true,
],
```

## User Model Setup

Add impersonation methods to your User model:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravilt\Users\Concerns\HasRolesAndPermissions;

class User extends Authenticatable
{
    use HasRolesAndPermissions;

    /**
     * Determine if the user can impersonate other users.
     */
    public function canImpersonate(): bool
    {
        return $this->hasRole('super_admin') || $this->hasRole('admin');
    }

    /**
     * Determine if the user can be impersonated.
     */
    public function canBeImpersonated(): bool
    {
        return !$this->hasRole('super_admin');
    }
}
```

## How It Works

### Starting Impersonation

1. Admin clicks "Impersonate" action on a user
2. Confirmation modal appears
3. Upon confirmation:
   - Original user ID is stored in session
   - User is logged in as the target user
   - Redirect to configured URL

### During Impersonation

- A banner appears at the top of the page
- Banner shows "You are impersonating as {name}"
- "Stop Impersonation" button is available

### Stopping Impersonation

1. User clicks "Stop Impersonation" in banner
2. Or visits the leave route directly
3. Original session is restored
4. Redirect to configured URL

## Middleware Setup

Add the impersonation banner middleware to your panel:

```php
use Laravilt\Users\Http\Middleware\ImpersonationBanner;

$panel->middleware([
    ImpersonationBanner::class,
]);
```

This middleware:
- Checks if user is impersonating
- Shares impersonation data with Inertia
- Enables the impersonation banner

## Routes

The package registers these routes:

```
POST   /{panel}/impersonate/{user}   Start impersonation
GET    /{panel}/impersonation/leave  Stop impersonation
```

## Security Restrictions

### Cannot Impersonate Self

Users cannot impersonate themselves.

### Cannot Impersonate Super Admin

Super admins cannot be impersonated (by default).

### Permission Required

Only users with the `impersonate_user` permission can impersonate.

### canImpersonate Check

The `canImpersonate()` method on the impersonator is checked.

### canBeImpersonated Check

The `canBeImpersonated()` method on the target is checked.

## Configuration

### Redirect After Start

Where to redirect after starting impersonation:

```php
// config/laravilt-users.php
'impersonation' => [
    'redirect_to' => '/admin',
],
```

### Redirect After Leave

Where to redirect after stopping impersonation:

```php
'impersonation' => [
    'leave_redirect_to' => '/admin',
],
```

## Events

The package dispatches events during impersonation:

### ImpersonationStarted

```php
use Laravilt\Users\Events\ImpersonationStarted;

Event::listen(ImpersonationStarted::class, function ($event) {
    Log::info("User {$event->impersonator->name} started impersonating {$event->impersonated->name}");
});
```

### ImpersonationEnded

```php
use Laravilt\Users\Events\ImpersonationEnded;

Event::listen(ImpersonationEnded::class, function ($event) {
    Log::info("User {$event->impersonator->name} stopped impersonating {$event->impersonated->name}");
});
```

## Impersonation Service

The `ImpersonationService` handles the impersonation logic:

```php
use Laravilt\Users\Services\ImpersonationService;

$service = app(ImpersonationService::class);

// Start impersonation
$service->impersonate($impersonator, $target);

// Stop impersonation
$service->leave();

// Check if impersonating
if ($service->isImpersonating()) {
    $original = $service->getImpersonator();
}
```

## Session Data

During impersonation, the following session data is stored:

```php
session('impersonator_id')      // Original user ID
session('impersonating')        // Boolean flag
```

## Banner Component

The impersonation banner is shown at the top of the page:

```vue
<ImpersonationBanner
    :name="impersonatedUser.name"
    :leaveUrl="leaveUrl"
/>
```

## Translations

Impersonation strings are translatable:

```php
// Actions
__('laravilt-users::users.actions.impersonate')
__('laravilt-users::users.actions.impersonate_tooltip')
__('laravilt-users::users.actions.impersonate_heading')
__('laravilt-users::users.actions.impersonate_description')
__('laravilt-users::users.actions.impersonate_confirm')

// Banner
__('laravilt-users::users.impersonation.banner.message')
__('laravilt-users::users.impersonation.banner.stop')

// Messages
__('laravilt-users::users.impersonation.messages.started')
__('laravilt-users::users.impersonation.messages.stopped')
__('laravilt-users::users.impersonation.messages.cannot_impersonate_self')
__('laravilt-users::users.impersonation.messages.cannot_impersonate_super_admin')
__('laravilt-users::users.impersonation.messages.unauthorized')
```

## Customization

### Custom Impersonation Logic

Override the service:

```php
use Laravilt\Users\Services\ImpersonationService;

class CustomImpersonationService extends ImpersonationService
{
    public function impersonate($impersonator, $target): void
    {
        // Custom pre-impersonation logic
        $this->logImpersonation($impersonator, $target);

        parent::impersonate($impersonator, $target);

        // Custom post-impersonation logic
    }
}
```

Register in service provider:

```php
$this->app->bind(ImpersonationService::class, CustomImpersonationService::class);
```

### Custom Visibility

Control action visibility:

```php
ImpersonateAction::make()
    ->visible(function ($record) {
        return $record->id !== auth()->id()
            && !$record->hasRole('super_admin')
            && auth()->user()->can('impersonate_user');
    })
```

## Troubleshooting

### Banner Not Showing

1. Ensure middleware is registered
2. Check `impersonating` session key exists
3. Verify Inertia is sharing the data

### Cannot Impersonate

1. Check `canImpersonate()` returns true
2. Check target's `canBeImpersonated()` returns true
3. Verify user has `impersonate_user` permission

### Session Not Preserved

1. Ensure session driver supports this
2. Check session lifetime configuration

## Best Practices

### 1. Log All Impersonations

```php
Event::listen(ImpersonationStarted::class, function ($event) {
    AuditLog::create([
        'action' => 'impersonation_started',
        'impersonator_id' => $event->impersonator->id,
        'impersonated_id' => $event->impersonated->id,
    ]);
});
```

### 2. Limit Impersonation Duration

Consider implementing a timeout for impersonation sessions.

### 3. Restrict to Production Admins

```php
public function canImpersonate(): bool
{
    return $this->hasRole('super_admin')
        && app()->environment('production');
}
```

### 4. Notify Impersonated User

```php
Event::listen(ImpersonationStarted::class, function ($event) {
    $event->impersonated->notify(new ImpersonationNotification($event->impersonator));
});
```

## Next Steps

- [Users Resource](users.md) - Manage users
- [Roles Resource](roles.md) - Manage roles
- [Permissions](permissions.md) - Set up permissions
