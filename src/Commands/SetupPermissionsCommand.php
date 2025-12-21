<?php

namespace Laravilt\Users\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Laravilt\Panel\PanelRegistry;
use Laravilt\Panel\Resources\Resource;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

class SetupPermissionsCommand extends Command
{
    protected $signature = 'laravilt:secure
                            {--fresh : Delete all existing permissions and roles before creating}
                            {--super-admin : Create the super admin role}
                            {--generate-seeder : Generate a seeder file for production deployment}
                            {--panel= : Only generate permissions for a specific panel}
                            {--exclude=* : Exclude specific resources from permission generation}
                            {--only=* : Only generate permissions for specific resources}
                            {--dry-run : Show what would be created without actually creating}';

    protected $description = 'Setup security permissions and roles for the application by discovering resources';

    protected array $defaultPermissionPrefixes = [
        'view_any',
        'view',
        'create',
        'update',
        'delete',
        'restore',
        'force_delete',
        'replicate',
        'reorder',
    ];

    protected array $defaultRoles = [
        'super_admin' => '*',
        'admin' => [],  // Will be populated with all view/create/update permissions
        'moderator' => [], // Will be populated with view permissions only
    ];

    protected array $discoveredResources = [];

    protected array $generatedPermissions = [];

    public function handle(): int
    {
        // Check if permissions table exists
        if (! $this->option('dry-run') && ! $this->ensureTablesExist()) {
            return self::FAILURE;
        }

        $guardName = config('laravilt-users.guard_name', 'web');

        // Discover resources
        info('Discovering resources...');
        $this->discoverResources();

        if (empty($this->discoveredResources)) {
            warning('No resources found. Make sure you have registered resources in your panels.');

            return self::SUCCESS;
        }

        // Show discovered resources
        $this->displayDiscoveredResources();

        // Generate permissions from resources
        $this->generatePermissionsFromResources();

        // Add custom permissions from config
        $customPermissions = config('laravilt-users.permissions.custom', []);
        $this->generatedPermissions = array_unique(array_merge($this->generatedPermissions, $customPermissions));

        // Display generated permissions
        $this->displayGeneratedPermissions();

        if ($this->option('dry-run')) {
            info('Dry run completed. No changes were made.');

            return self::SUCCESS;
        }

        // Ask for confirmation
        if (! confirm('Do you want to create these permissions?', true)) {
            info('Operation cancelled.');

            return self::SUCCESS;
        }

        if ($this->option('fresh')) {
            spin(
                fn () => $this->freshSetup($guardName),
                'Removing existing permissions and roles...'
            );
            info('Existing permissions and roles removed.');
        }

        // Create permissions
        spin(
            fn () => $this->createPermissions($guardName),
            'Creating permissions...'
        );

        info('Permissions created successfully.');

        // Create roles
        spin(
            fn () => $this->createRoles($guardName),
            'Creating roles...'
        );

        info('Roles created successfully.');

        // Clear cache
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        info('Permission cache cleared.');

        // Generate seeder if requested
        if ($this->option('generate-seeder')) {
            $this->generateSeeder($guardName);
        }

        info('Security permissions and roles have been set up successfully!');

        return self::SUCCESS;
    }

    protected function discoverResources(): void
    {
        if (! class_exists(PanelRegistry::class)) {
            return;
        }

        $registry = app(PanelRegistry::class);
        $panels = $registry->all();

        $panelFilter = $this->option('panel');
        $excludeResources = $this->option('exclude') ?: [];
        $onlyResources = $this->option('only') ?: [];

        foreach ($panels as $panel) {
            // Filter by panel if specified
            if ($panelFilter && $panel->getId() !== $panelFilter) {
                continue;
            }

            $resources = $panel->getResources();

            foreach ($resources as $resourceClass) {
                if (! is_subclass_of($resourceClass, Resource::class)) {
                    continue;
                }

                $resourceSlug = $resourceClass::getSlug();

                // Apply exclusions
                if (in_array($resourceSlug, $excludeResources) || in_array($resourceClass, $excludeResources)) {
                    continue;
                }

                // Apply only filter
                if (! empty($onlyResources) && ! in_array($resourceSlug, $onlyResources) && ! in_array($resourceClass, $onlyResources)) {
                    continue;
                }

                // Get resource info
                $this->discoveredResources[$resourceClass] = [
                    'class' => $resourceClass,
                    'slug' => $resourceSlug,
                    'label' => $resourceClass::getLabel(),
                    'panel' => $panel->getId(),
                    'permission_prefix' => method_exists($resourceClass, 'getPermissionPrefix')
                        ? $resourceClass::getPermissionPrefix()
                        : str(class_basename($resourceClass::getModel()))->snake()->toString(),
                ];
            }
        }
    }

    protected function displayDiscoveredResources(): void
    {
        $rows = [];
        foreach ($this->discoveredResources as $resource) {
            $rows[] = [
                $resource['panel'],
                $resource['label'],
                $resource['slug'],
                $resource['permission_prefix'],
            ];
        }

        table(
            ['Panel', 'Resource', 'Slug', 'Permission Prefix'],
            $rows
        );

        info(count($this->discoveredResources).' resources discovered.');
    }

    protected function generatePermissionsFromResources(): void
    {
        $separator = config('laravilt-users.permissions.separator', '_');
        $prefixes = config('laravilt-users.permissions.prefixes', $this->defaultPermissionPrefixes);

        foreach ($this->discoveredResources as $resource) {
            $permissionPrefix = $resource['permission_prefix'];

            foreach ($prefixes as $prefix) {
                $permission = $prefix.$separator.$permissionPrefix;
                $this->generatedPermissions[] = $permission;
            }
        }

        // Sort permissions for readability
        sort($this->generatedPermissions);
    }

    protected function displayGeneratedPermissions(): void
    {
        info('Generated permissions ('.count($this->generatedPermissions).'):');

        // Group by resource prefix for better display
        $grouped = [];
        foreach ($this->generatedPermissions as $permission) {
            $parts = explode('_', $permission, 3);
            $resource = end($parts);
            if (! isset($grouped[$resource])) {
                $grouped[$resource] = [];
            }
            $grouped[$resource][] = $permission;
        }

        foreach ($grouped as $resource => $permissions) {
            $this->line("  <comment>{$resource}:</comment> ".implode(', ', $permissions));
        }
    }

    protected function ensureTablesExist(): bool
    {
        $permissionsTable = config('permission.table_names.permissions', 'permissions');
        $rolesTable = config('permission.table_names.roles', 'roles');

        if (Schema::hasTable($permissionsTable) && Schema::hasTable($rolesTable)) {
            return true;
        }

        warning('The permissions and roles tables do not exist.');

        if (confirm('Would you like to publish and run the Spatie Permission migrations?', true)) {
            // Publish Spatie Permission migrations
            spin(
                fn () => Artisan::call('vendor:publish', [
                    '--provider' => 'Spatie\Permission\PermissionServiceProvider',
                    '--tag' => 'permission-migrations',
                ]),
                'Publishing Spatie Permission migrations...'
            );

            // Run migrations
            spin(
                fn () => Artisan::call('migrate'),
                'Running migrations...'
            );

            info('Migrations completed.');

            return true;
        }

        error('Cannot setup permissions without the required tables. Please run migrations first.');

        return false;
    }

    protected function freshSetup(string $guardName): void
    {
        $permissionModel = $this->getPermissionModel();
        $roleModel = $this->getRoleModel();

        $permissionModel::where('guard_name', $guardName)->delete();
        $roleModel::where('guard_name', $guardName)->delete();
    }

    protected function createPermissions(string $guardName): void
    {
        $permissionModel = $this->getPermissionModel();

        foreach ($this->generatedPermissions as $permission) {
            $permissionModel::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ]);
        }
    }

    protected function createRoles(string $guardName): void
    {
        $roleModel = $this->getRoleModel();
        $permissionModel = $this->getPermissionModel();

        // Build admin and moderator permissions
        $adminPermissions = array_filter($this->generatedPermissions, function ($permission) {
            // Admin gets all except force_delete
            return ! str_starts_with($permission, 'force_delete_');
        });

        $moderatorPermissions = array_filter($this->generatedPermissions, function ($permission) {
            // Moderator only gets view permissions
            return str_starts_with($permission, 'view_');
        });

        $roles = [
            'super_admin' => '*',
            'admin' => $adminPermissions,
            'moderator' => $moderatorPermissions,
        ];

        foreach ($roles as $roleName => $permissions) {
            if ($roleName === 'super_admin' && ! $this->option('super-admin') && ! config('laravilt-users.super_admin.enabled', true)) {
                warning('Skipping super_admin role (disabled in config or --super-admin not provided)');

                continue;
            }

            $role = $roleModel::firstOrCreate([
                'name' => $roleName,
                'guard_name' => $guardName,
            ]);

            if ($permissions === '*') {
                // Give all permissions to super admin
                $role->syncPermissions($permissionModel::where('guard_name', $guardName)->get());
            } else {
                $role->syncPermissions($permissions);
            }
        }
    }

    protected function generateSeeder(string $guardName): void
    {
        $seederPath = database_path('seeders/PermissionsSeeder.php');

        // Build permissions array for seeder
        $permissionsArray = var_export($this->generatedPermissions, true);

        // Build roles array for seeder
        $adminPermissions = array_values(array_filter($this->generatedPermissions, function ($permission) {
            return ! str_starts_with($permission, 'force_delete_');
        }));

        $moderatorPermissions = array_values(array_filter($this->generatedPermissions, function ($permission) {
            return str_starts_with($permission, 'view_');
        }));

        $rolesCode = <<<PHP
        \$roles = [
            'super_admin' => '*',
            'admin' => {$this->arrayToCode($adminPermissions)},
            'moderator' => {$this->arrayToCode($moderatorPermissions)},
        ];
PHP;

        $seederContent = <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Permission Seeder generated by laravilt:secure command.
 *
 * Run this seeder in production: php artisan db:seed --class=PermissionsSeeder
 */
class PermissionsSeeder extends Seeder
{
    /**
     * All permissions for the application.
     */
    protected array \$permissions = {$permissionsArray};

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        \$guardName = config('laravilt-users.guard_name', 'web');

        // Create permissions
        foreach (\$this->permissions as \$permission) {
            Permission::firstOrCreate([
                'name' => \$permission,
                'guard_name' => \$guardName,
            ]);
        }

        // Create roles and assign permissions
{$rolesCode}

        foreach (\$roles as \$roleName => \$permissions) {
            \$role = Role::firstOrCreate([
                'name' => \$roleName,
                'guard_name' => \$guardName,
            ]);

            if (\$permissions === '*') {
                // Give all permissions to super admin
                \$role->syncPermissions(Permission::where('guard_name', \$guardName)->get());
            } else {
                \$role->syncPermissions(\$permissions);
            }
        }

        \$this->command->info('Permissions and roles seeded successfully!');
    }
}
PHP;

        File::ensureDirectoryExists(dirname($seederPath));
        File::put($seederPath, $seederContent);

        info("Seeder generated: {$seederPath}");
        info('Run in production: php artisan db:seed --class=PermissionsSeeder');
    }

    protected function arrayToCode(array $array): string
    {
        if (empty($array)) {
            return '[]';
        }

        $items = array_map(fn ($item) => "            '{$item}'", $array);

        return "[\n".implode(",\n", $items).",\n        ]";
    }

    protected function getPermissionModel(): string
    {
        return config('permission.models.permission', \Spatie\Permission\Models\Permission::class);
    }

    protected function getRoleModel(): string
    {
        return config('permission.models.role', \Spatie\Permission\Models\Role::class);
    }
}
