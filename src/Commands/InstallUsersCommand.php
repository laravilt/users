<?php

namespace Laravilt\Users\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

class InstallUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'laravilt:users:install
                            {--force : Overwrite existing files}
                            {--with-permissions : Also run permission setup}';

    /**
     * The console command description.
     */
    protected $description = 'Install the Laravilt Users plugin';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        info('Installing Laravilt Users plugin...');

        // Publish config
        spin(
            fn () => $this->publishConfig(),
            'Publishing configuration...'
        );

        // Publish Spatie Permission migrations first
        spin(
            fn () => $this->publishSpatiePermissionMigrations(),
            'Publishing Spatie Permission migrations...'
        );

        // Publish our migrations
        spin(
            fn () => $this->publishMigrations(),
            'Publishing migrations...'
        );

        // Run migrations
        if (confirm('Would you like to run the migrations now?', true)) {
            spin(
                fn () => Artisan::call('migrate', [], $this->output),
                'Running migrations...'
            );
        }

        // Add HasRoles trait to User model
        if (confirm('Would you like to add the HasRoles trait to your User model?', true)) {
            spin(
                fn () => $this->addHasRolesToUserModel(),
                'Adding HasRoles trait to User model...'
            );
        }

        // Check if tables exist now
        $permissionsTable = config('permission.table_names.permissions', 'permissions');

        if (! Schema::hasTable($permissionsTable)) {
            warning('Permissions tables not found. Please run migrations before setting up permissions.');

            return self::SUCCESS;
        }

        // Setup permissions
        if ($this->option('with-permissions') || confirm('Would you like to setup default permissions and roles?', true)) {
            $this->setupPermissions();
        }

        info('Laravilt Users plugin installed successfully!');

        $this->newLine();
        info('Next steps:');
        info('1. Register the UsersPlugin in your panel configuration');
        info('2. Customize the config/laravilt-users.php file as needed');

        return self::SUCCESS;
    }

    /**
     * Publish configuration file.
     */
    protected function publishConfig(): void
    {
        $params = ['--tag' => 'laravilt-users-config'];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        Artisan::call('vendor:publish', $params, $this->output);
    }

    /**
     * Publish Spatie Permission migrations.
     */
    protected function publishSpatiePermissionMigrations(): void
    {
        Artisan::call('vendor:publish', [
            '--provider' => 'Spatie\Permission\PermissionServiceProvider',
            '--tag' => 'permission-migrations',
        ], $this->output);
    }

    /**
     * Publish migrations.
     */
    protected function publishMigrations(): void
    {
        $params = ['--tag' => 'laravilt-users-migrations'];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        Artisan::call('vendor:publish', $params, $this->output);
    }

    /**
     * Add HasRoles trait to the User model.
     */
    protected function addHasRolesToUserModel(): void
    {
        $userModelClass = config('laravilt-users.model', \App\Models\User::class);
        $reflection = new \ReflectionClass($userModelClass);
        $filePath = $reflection->getFileName();

        if (! $filePath || ! File::exists($filePath)) {
            warning('Could not find User model file.');

            return;
        }

        $content = File::get($filePath);

        // Check if trait is already present
        if (str_contains($content, 'HasRoles') || str_contains($content, 'Spatie\Permission\Traits\HasRoles')) {
            info('HasRoles trait already exists in User model.');

            return;
        }

        // Add the use statement for the trait
        $useStatement = 'use Spatie\Permission\Traits\HasRoles;';

        // Find the namespace declaration and add import after it
        if (preg_match('/^(namespace\s+[^;]+;\s*\n)/m', $content, $matches)) {
            $namespaceDeclaration = $matches[1];
            $existingUseStatements = '';

            // Check if there are existing use statements
            if (preg_match('/^use\s+[^;]+;/m', $content)) {
                // Add after existing use statements
                $content = preg_replace(
                    '/(^use\s+[^;]+;\s*\n)(?!use\s+)/m',
                    "$1{$useStatement}\n",
                    $content,
                    1
                );
            } else {
                // Add after namespace
                $content = str_replace(
                    $namespaceDeclaration,
                    $namespaceDeclaration."\n{$useStatement}\n",
                    $content
                );
            }
        }

        // Add the trait to the class
        // Find the class declaration and its opening brace
        if (preg_match('/class\s+\w+[^{]*\{/', $content, $matches)) {
            $classDeclaration = $matches[0];

            // Check if there are existing traits
            if (preg_match('/use\s+\w+[^;]*;/m', $content, $traitMatches, 0, strpos($content, $classDeclaration))) {
                // Add our trait to the existing trait list
                $existingTrait = $traitMatches[0];

                // Check if it's a multi-trait declaration
                if (str_contains($existingTrait, ',')) {
                    $newTrait = str_replace(';', ', HasRoles;', $existingTrait);
                } else {
                    $newTrait = str_replace(';', ";\n    use HasRoles;", $existingTrait);
                }

                $content = str_replace($existingTrait, $newTrait, $content);
            } else {
                // No existing traits, add after the opening brace
                $content = str_replace(
                    $classDeclaration,
                    $classDeclaration."\n    use HasRoles;\n",
                    $content
                );
            }
        }

        File::put($filePath, $content);

        info('HasRoles trait added to User model.');
    }

    /**
     * Setup permissions and roles.
     */
    protected function setupPermissions(): void
    {
        $guardName = config('laravilt-users.guard_name', 'web');
        $permissionModel = config('permission.models.permission', \Spatie\Permission\Models\Permission::class);
        $roleModel = config('permission.models.role', \Spatie\Permission\Models\Role::class);

        $defaultPermissions = [
            'view_any_users',
            'view_users',
            'create_users',
            'update_users',
            'delete_users',
            'restore_users',
            'force_delete_users',
            'view_any_roles',
            'view_roles',
            'create_roles',
            'update_roles',
            'delete_roles',
            'view_any_permissions',
            'view_permissions',
            'create_permissions',
            'update_permissions',
            'delete_permissions',
            'impersonate_users',
        ];

        $defaultRoles = [
            'super_admin' => '*',
            'admin' => [
                'view_any_users',
                'view_users',
                'create_users',
                'update_users',
                'delete_users',
                'view_any_roles',
                'view_roles',
            ],
            'moderator' => [
                'view_any_users',
                'view_users',
                'update_users',
            ],
        ];

        spin(function () use ($permissionModel, $guardName, $defaultPermissions) {
            foreach ($defaultPermissions as $permission) {
                $permissionModel::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => $guardName,
                ]);
            }
        }, 'Creating permissions...');

        spin(function () use ($roleModel, $permissionModel, $guardName, $defaultRoles) {
            foreach ($defaultRoles as $roleName => $permissions) {
                $role = $roleModel::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => $guardName,
                ]);

                if ($permissions === '*') {
                    $role->syncPermissions($permissionModel::where('guard_name', $guardName)->get());
                } else {
                    $role->syncPermissions($permissions);
                }
            }
        }, 'Creating roles...');

        // Clear cache
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        info('Permissions and roles created successfully.');
    }
}
