<?php

namespace Laravilt\Users;

use Closure;
use Laravilt\Panel\Panel;
use Laravilt\Plugins\PluginProvider;
use Laravilt\Users\Resources\Roles\RoleResource;
use Laravilt\Users\Resources\Users\UserResource;

class UsersPlugin extends PluginProvider
{
    /**
     * The plugin ID (must be unique).
     */
    protected static string $id = 'users';

    /**
     * The plugin name.
     */
    protected static string $name = 'Users & Roles';

    /**
     * The plugin version.
     */
    protected static string $version = '1.0.0';

    /**
     * The plugin description.
     */
    protected static string $description = 'User and Role management plugin for Laravilt with RBAC and impersonation support';

    /**
     * The plugin author.
     */
    protected static string $author = 'Fady Mondy';

    protected bool $userResource = true;

    protected bool $roleResource = true;

    /**
     * Features are disabled by default - must be explicitly enabled.
     * Call ->impersonation() to enable impersonation feature.
     */
    protected bool $impersonation = false;

    /**
     * Features are disabled by default - must be explicitly enabled.
     * Call ->avatar() to enable avatar feature.
     */
    protected bool $avatar = false;

    protected ?string $navigationGroup = null;

    protected int $navigationSort = 10;

    protected ?string $userModel = null;

    protected ?Closure $userResourceClass = null;

    protected ?Closure $roleResourceClass = null;

    /**
     * Create a new plugin instance.
     */
    public static function make(): static
    {
        return new static;
    }

    /**
     * Get the plugin ID.
     */
    public function getId(): string
    {
        return static::$id;
    }

    /**
     * Enable or disable user resource.
     */
    public function userResource(bool $condition = true): static
    {
        $this->userResource = $condition;

        return $this;
    }

    /**
     * Check if user resource is enabled.
     */
    public function hasUserResource(): bool
    {
        return $this->userResource;
    }

    /**
     * Enable or disable role resource.
     */
    public function roleResource(bool $condition = true): static
    {
        $this->roleResource = $condition;

        return $this;
    }

    /**
     * Check if role resource is enabled.
     */
    public function hasRoleResource(): bool
    {
        return $this->roleResource;
    }

    /**
     * Enable or disable impersonation.
     */
    public function impersonation(bool $condition = true): static
    {
        $this->impersonation = $condition;

        return $this;
    }

    /**
     * Check if impersonation is enabled.
     */
    public function hasImpersonation(): bool
    {
        return $this->impersonation;
    }

    /**
     * Enable or disable avatar.
     */
    public function avatar(bool $condition = true): static
    {
        $this->avatar = $condition;

        return $this;
    }

    /**
     * Check if avatar is enabled.
     */
    public function hasAvatar(): bool
    {
        return $this->avatar;
    }

    /**
     * Set the navigation group.
     */
    public function navigationGroup(?string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    /**
     * Get the navigation group.
     */
    public function getNavigationGroup(): ?string
    {
        // If explicitly set via plugin method, use that
        if ($this->navigationGroup !== null) {
            return $this->navigationGroup;
        }

        // Otherwise use translation as default
        return __('laravilt-users::users.navigation.group');
    }

    /**
     * Set the navigation sort order.
     */
    public function navigationSort(int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    /**
     * Get the navigation sort order.
     */
    public function getNavigationSort(): int
    {
        return $this->navigationSort;
    }

    /**
     * Set the user model.
     */
    public function userModel(string $model): static
    {
        $this->userModel = $model;

        return $this;
    }

    /**
     * Get the user model.
     */
    public function getUserModel(): string
    {
        return $this->userModel
            ?? config('laravilt-users.model')
            ?? config('auth.providers.users.model');
    }

    /**
     * Set custom user resource class.
     */
    public function userResourceClass(Closure $callback): static
    {
        $this->userResourceClass = $callback;

        return $this;
    }

    /**
     * Get the user resource class.
     */
    public function getUserResourceClass(): string
    {
        if ($this->userResourceClass) {
            return ($this->userResourceClass)();
        }

        return UserResource::class;
    }

    /**
     * Set custom role resource class.
     */
    public function roleResourceClass(Closure $callback): static
    {
        $this->roleResourceClass = $callback;

        return $this;
    }

    /**
     * Get the role resource class.
     */
    public function getRoleResourceClass(): string
    {
        if ($this->roleResourceClass) {
            return ($this->roleResourceClass)();
        }

        return RoleResource::class;
    }

    /**
     * Register the plugin with a Filament panel.
     */
    public function register(Panel $panel): void
    {
        $resources = [];

        if ($this->hasUserResource()) {
            $resources[] = $this->getUserResourceClass();
        }

        if ($this->hasRoleResource()) {
            $resources[] = $this->getRoleResourceClass();
        }

        if (! empty($resources)) {
            $panel->resources($resources);
        }
    }

    /**
     * Boot the plugin for a panel.
     */
    public function boot(Panel $panel): void
    {
        // Always set the feature configs based on plugin settings
        // This ensures the config reflects the plugin's configuration
        config()->set('laravilt-users.features.impersonation', $this->hasImpersonation());
        config()->set('laravilt-users.features.avatar', $this->hasAvatar());

        // Also set navigation settings
        if ($this->navigationGroup !== null) {
            config()->set('laravilt-users.navigation.group', $this->navigationGroup);
        }
        config()->set('laravilt-users.navigation.sort', $this->navigationSort);
    }
}
