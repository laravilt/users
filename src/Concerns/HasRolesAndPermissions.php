<?php

namespace Laravilt\Users\Concerns;

use Spatie\Permission\Traits\HasRoles;

trait HasRolesAndPermissions
{
    use HasRoles;

    /**
     * Check if the user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        $superAdminRole = config('laravilt-users.super_admin.role', 'super_admin');

        return $this->hasRole($superAdminRole);
    }

    /**
     * Check if the user can be impersonated.
     */
    public function canBeImpersonated(): bool
    {
        // Super admins cannot be impersonated by default
        if ($this->isSuperAdmin()) {
            return config('laravilt-users.impersonation.allow_super_admin', false);
        }

        // Check if user has any protected roles
        $protectedRoles = config('laravilt-users.impersonation.protected_roles', ['super_admin']);

        return ! $this->hasAnyRole($protectedRoles);
    }

    /**
     * Check if the user can impersonate others.
     */
    public function canImpersonate(): bool
    {
        // Super admins can always impersonate
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Check if user has impersonate permission
        if ($this->hasPermissionTo('impersonate users')) {
            return true;
        }

        // Check if user has any of the allowed roles
        $allowedRoles = config('laravilt-users.impersonation.restrict_to_roles', []);

        if (empty($allowedRoles)) {
            return false;
        }

        return $this->hasAnyRole($allowedRoles);
    }

    /**
     * Get all permission names for this user.
     */
    public function getAllPermissionNames(): array
    {
        return $this->getAllPermissions()->pluck('name')->toArray();
    }

    /**
     * Get all role names for this user as an array.
     */
    public function getRoleNamesArray(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Scope to filter users that are verified.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope to filter users that are unverified.
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }
}
