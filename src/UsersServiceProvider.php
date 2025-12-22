<?php

namespace Laravilt\Users;

use Illuminate\Support\ServiceProvider;
use Laravilt\Users\Commands\DebugPermissionsCommand;
use Laravilt\Users\Commands\InstallUsersCommand;
use Laravilt\Users\Commands\SetupPermissionsCommand;
use Laravilt\Users\Services\ImpersonationService;

class UsersServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravilt-users.php',
            'laravilt-users'
        );

        // Register the impersonation service
        $this->app->singleton(ImpersonationService::class, function ($app) {
            return new ImpersonationService;
        });

        // Register alias for easier access
        $this->app->alias(ImpersonationService::class, 'laravilt.impersonation');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'laravilt-users');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__.'/../config/laravilt-users.php' => config_path('laravilt-users.php'),
            ], 'laravilt-users-config');

            // Publish migrations
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'laravilt-users-migrations');

            // Publish translations
            $this->publishes([
                __DIR__.'/../lang' => lang_path('vendor/users'),
            ], 'laravilt-users-translations');

            // Register commands
            $this->commands([
                DebugPermissionsCommand::class,
                InstallUsersCommand::class,
                SetupPermissionsCommand::class,
            ]);
        }

        // Register the permission seeder for super admin
        $this->registerSuperAdminGate();
    }

    /**
     * Register the super admin gate.
     * Only registers if both bypass_permissions and define_via_gate are enabled.
     */
    protected function registerSuperAdminGate(): void
    {
        // Only apply Gate bypass if bypass_permissions is explicitly enabled
        $bypassPermissions = config('laravilt-users.super_admin.bypass_permissions', false);
        $useGate = config('laravilt-users.super_admin.define_via_gate', false);

        if (! $bypassPermissions || ! $useGate) {
            return;
        }

        $superAdminRole = config('laravilt-users.super_admin.role', 'super_admin');

        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) use ($superAdminRole) {
            if (method_exists($user, 'hasRole') && $user->hasRole($superAdminRole)) {
                return true;
            }
        });
    }
}
