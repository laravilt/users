<?php

namespace Laravilt\Users\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravilt\Users\Exceptions\CannotImpersonateSelfException;
use Laravilt\Users\Exceptions\CannotImpersonateSuperAdminException;

class ImpersonationService
{
    protected string $sessionKey = 'impersonator_id';

    protected string $sessionTimeKey = 'impersonated_at';

    /**
     * Impersonate the given user.
     */
    public function impersonate(Authenticatable $impersonator, Authenticatable $target): void
    {
        // Cannot impersonate yourself
        if ($impersonator->getAuthIdentifier() === $target->getAuthIdentifier()) {
            throw new CannotImpersonateSelfException;
        }

        // Check if target is a super admin
        if (method_exists($target, 'isSuperAdmin') && $target->isSuperAdmin()) {
            if (! config('laravilt-users.impersonation.allow_super_admin', false)) {
                throw new CannotImpersonateSuperAdminException;
            }
        }

        // Store the impersonator's ID in session
        Session::put($this->sessionKey, $impersonator->getAuthIdentifier());
        Session::put($this->sessionTimeKey, now()->toISOString());

        // Log in as the target user
        Auth::login($target);
    }

    /**
     * Stop impersonation and return to original user.
     */
    public function stopImpersonation(): void
    {
        $impersonatorId = Session::get($this->sessionKey);

        if ($impersonatorId) {
            $userModel = $this->getUserModel();
            $impersonator = $userModel::find($impersonatorId);

            if ($impersonator) {
                Auth::login($impersonator);
            }
        }

        $this->clearSession();
    }

    /**
     * Check if currently impersonating.
     */
    public function isImpersonating(): bool
    {
        return Session::has($this->sessionKey);
    }

    /**
     * Get the impersonator user.
     */
    public function getImpersonator(): ?Authenticatable
    {
        if (! $this->isImpersonating()) {
            return null;
        }

        $impersonatorId = Session::get($this->sessionKey);
        $userModel = $this->getUserModel();

        return $userModel::find($impersonatorId);
    }

    /**
     * Get the impersonation start time.
     */
    public function getImpersonatedAt(): ?string
    {
        return Session::get($this->sessionTimeKey);
    }

    /**
     * Check if the impersonator can impersonate the target.
     */
    public function canImpersonate(Authenticatable $impersonator, Authenticatable $target): bool
    {
        // Cannot impersonate yourself
        if ($impersonator->getAuthIdentifier() === $target->getAuthIdentifier()) {
            return false;
        }

        // Check if impersonator has custom canImpersonate method
        if (method_exists($impersonator, 'canImpersonate')) {
            return $impersonator->canImpersonate();
        }

        // Check if impersonator is super admin
        if (method_exists($impersonator, 'isSuperAdmin') && $impersonator->isSuperAdmin()) {
            return true;
        }

        // Check if impersonator has the explicit permission
        if (method_exists($impersonator, 'hasPermissionTo')) {
            try {
                if ($impersonator->hasPermissionTo('impersonate users')) {
                    return true;
                }
            } catch (\Throwable $e) {
                // Permission doesn't exist, continue with other checks
            }
        }

        // Check role-based restrictions from config
        $restrictToRoles = config('laravilt-users.impersonation.restrict_to_roles', []);
        if (! empty($restrictToRoles) && method_exists($impersonator, 'hasAnyRole')) {
            return $impersonator->hasAnyRole($restrictToRoles);
        }

        // If no restrictions configured, allow all authenticated users
        if (empty($restrictToRoles)) {
            return true;
        }

        return false;
    }

    /**
     * Clear impersonation session data.
     */
    protected function clearSession(): void
    {
        Session::forget($this->sessionKey);
        Session::forget($this->sessionTimeKey);
    }

    /**
     * Get the user model class.
     */
    protected function getUserModel(): string
    {
        return config('laravilt-users.model', config('auth.providers.users.model'));
    }
}
