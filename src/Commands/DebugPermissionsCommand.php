<?php

namespace Laravilt\Users\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

class DebugPermissionsCommand extends Command
{
    protected $signature = 'laravilt:debug-permissions {user? : User ID or email to check}';

    protected $description = 'Debug permission setup and check user permissions';

    public function handle(): int
    {
        $guardName = config('laravilt-users.guard_name', 'web');
        $userModel = config('laravilt-users.model', config('auth.providers.users.model', 'App\\Models\\User'));

        info('Permission System Debug');
        info('=======================');

        // Check if Spatie Permission is installed
        if (! class_exists(\Spatie\Permission\Models\Permission::class)) {
            warning('Spatie Permission package is NOT installed!');

            return self::FAILURE;
        }

        info('Spatie Permission: Installed ✓');
        info("Guard Name: {$guardName}");
        info("User Model: {$userModel}");

        // Check if User model has HasRoles trait
        if (! method_exists($userModel, 'hasRole')) {
            warning("User model does NOT have HasRoles trait!");
            warning("Add 'use Spatie\\Permission\\Traits\\HasRoles;' to your User model");

            return self::FAILURE;
        }

        info('User Model HasRoles Trait: ✓');

        // Count permissions and roles
        $permissionModel = config('permission.models.permission', \Spatie\Permission\Models\Permission::class);
        $roleModel = config('permission.models.role', \Spatie\Permission\Models\Role::class);

        $permissionCount = $permissionModel::where('guard_name', $guardName)->count();
        $roleCount = $roleModel::where('guard_name', $guardName)->count();

        info("Permissions (guard={$guardName}): {$permissionCount}");
        info("Roles (guard={$guardName}): {$roleCount}");

        if ($permissionCount === 0) {
            warning('No permissions found! Run: php artisan laravilt:secure');

            return self::FAILURE;
        }

        // List roles with their permissions
        $roles = $roleModel::where('guard_name', $guardName)->get();

        $roleData = [];
        foreach ($roles as $role) {
            $permCount = $role->permissions()->count();
            $userCount = $userModel::role($role->name)->count();
            $roleData[] = [$role->name, $permCount, $userCount];
        }

        $this->newLine();
        info('Roles:');
        table(['Role', 'Permissions', 'Users'], $roleData);

        // Check specific user if provided
        $userId = $this->argument('user');
        if ($userId) {
            $user = is_numeric($userId)
                ? $userModel::find($userId)
                : $userModel::where('email', $userId)->first();

            if (! $user) {
                warning("User not found: {$userId}");

                return self::FAILURE;
            }

            $this->newLine();
            info("User: {$user->name} ({$user->email})");

            // Get user's roles
            $userRoles = $user->roles->pluck('name')->toArray();
            info('Roles: '.($userRoles ? implode(', ', $userRoles) : 'None'));

            // Get user's permissions (direct + through roles)
            $allPermissions = $user->getAllPermissions()->pluck('name')->toArray();
            $directPermissions = $user->getDirectPermissions()->pluck('name')->toArray();

            info('Direct Permissions: '.count($directPermissions));
            info('Total Permissions (incl. via roles): '.count($allPermissions));

            // Test some common permissions
            $testPermissions = ['view_any_user', 'create_user', 'view_any_role', 'create_role'];

            $this->newLine();
            info('Permission Check:');

            $checkData = [];
            foreach ($testPermissions as $perm) {
                try {
                    $has = $user->hasPermissionTo($perm, $guardName);
                    $checkData[] = [$perm, $has ? '✓ Yes' : '✗ No'];
                } catch (\Exception $e) {
                    $checkData[] = [$perm, '⚠ Not found'];
                }
            }

            table(['Permission', 'Has Access'], $checkData);
        }

        // Clear permission cache
        $this->newLine();
        info('Clearing permission cache...');
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        info('Permission cache cleared! ✓');

        return self::SUCCESS;
    }
}
