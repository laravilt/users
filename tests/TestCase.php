<?php

namespace Laravilt\Users\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravilt\Users\Tests\Models\User;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Permission\PermissionServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Laravilt\\Users\\Tests\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            PermissionServiceProvider::class,
            \Laravilt\Support\SupportServiceProvider::class,
            \Laravilt\Forms\FormsServiceProvider::class,
            \Laravilt\Tables\TablesServiceProvider::class,
            \Laravilt\Actions\ActionsServiceProvider::class,
            \Laravilt\Users\UsersServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));

        // Configure auth to use our test User model
        config()->set('auth.providers.users.model', User::class);

        // Configure users plugin
        config()->set('laravilt-users', [
            'enabled' => true,
            'model' => User::class,
            'resources' => [
                'user' => true,
                'role' => true,
            ],
            'features' => [
                'impersonation' => true,
                'avatar' => true,
                'teams' => false,
            ],
            'navigation' => [
                'group' => 'Users & Roles',
                'icon' => 'Users',
                'sort' => 10,
            ],
        ]);

        // Configure permission package
        config()->set('permission.models.permission', \Spatie\Permission\Models\Permission::class);
        config()->set('permission.models.role', \Spatie\Permission\Models\Role::class);
        config()->set('permission.column_names.role_pivot_key', 'role_id');
        config()->set('permission.column_names.permission_pivot_key', 'permission_id');
        config()->set('permission.column_names.model_morph_key', 'model_id');
        config()->set('permission.column_names.team_foreign_key', 'team_id');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    protected function actingAsUser(?User $user = null): User
    {
        $user = $user ?? $this->createUser();
        $this->actingAs($user);

        return $user;
    }
}
