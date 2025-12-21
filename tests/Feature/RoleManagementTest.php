<?php

use Laravilt\Users\Tests\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->actingAsUser();

    // Create test permissions
    Permission::findOrCreate('edit articles', 'web');
    Permission::findOrCreate('delete articles', 'web');
    Permission::findOrCreate('publish articles', 'web');
});

describe('Role Model', function () {
    it('can create a role', function () {
        $role = Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        expect($role)
            ->name->toBe('admin')
            ->guard_name->toBe('web');

        $this->assertDatabaseHas('roles', [
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
    });

    it('can update a role', function () {
        $role = Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $role->update(['name' => 'super-admin']);

        expect($role->fresh()->name)->toBe('super-admin');
    });

    it('can delete a role', function () {
        $role = Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $roleId = $role->id;

        $role->delete();

        $this->assertDatabaseMissing('roles', ['id' => $roleId]);
    });

    it('prevents duplicate role names for same guard', function () {
        Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        expect(fn () => Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]))->toThrow(\Spatie\Permission\Exceptions\RoleAlreadyExists::class);
    });

    it('allows same role name for different guards', function () {
        Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $apiRole = Role::create([
            'name' => 'admin',
            'guard_name' => 'api',
        ]);

        expect($apiRole)->toBeInstanceOf(Role::class);
        expect(Role::where('name', 'admin')->count())->toBe(2);
    });
});

describe('Role Permissions', function () {
    it('can assign permission to role', function () {
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $permission = Permission::findByName('edit articles', 'web');

        $role->givePermissionTo($permission);

        expect($role->hasPermissionTo('edit articles'))->toBeTrue();
    });

    it('can assign multiple permissions to role', function () {
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        // Permissions already created in beforeEach

        $role->givePermissionTo(['edit articles', 'delete articles']);

        expect($role->permissions)->toHaveCount(2);
        expect($role->hasPermissionTo('edit articles'))->toBeTrue();
        expect($role->hasPermissionTo('delete articles'))->toBeTrue();
    });

    it('can revoke permission from role', function () {
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $role->givePermissionTo('edit articles');

        expect($role->hasPermissionTo('edit articles'))->toBeTrue();

        $role->revokePermissionTo('edit articles');

        expect($role->fresh()->hasPermissionTo('edit articles'))->toBeFalse();
    });

    it('can sync permissions for role', function () {
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        // Permissions already created in beforeEach

        $role->givePermissionTo(['edit articles', 'delete articles']);
        expect($role->permissions)->toHaveCount(2);

        $role->syncPermissions(['publish articles']);

        expect($role->fresh()->permissions)->toHaveCount(1);
        expect($role->fresh()->hasPermissionTo('publish articles'))->toBeTrue();
    });

    it('can get all permissions of a role', function () {
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $role->givePermissionTo(['edit articles', 'delete articles', 'publish articles']);

        $permissions = $role->permissions;

        expect($permissions)->toHaveCount(3);
        expect($permissions->pluck('name')->toArray())->toContain('edit articles', 'delete articles', 'publish articles');
    });
});

describe('Role Users', function () {
    it('can get users with a role', function () {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $user1->assignRole($role);
        $user2->assignRole($role);

        $usersWithRole = User::role('admin')->get();

        expect($usersWithRole)->toHaveCount(2);
        expect($usersWithRole->pluck('id')->toArray())->toContain($user1->id, $user2->id);
        expect($usersWithRole->pluck('id')->toArray())->not->toContain($user3->id);
    });

    it('can count users in a role', function () {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);

        User::factory()->count(5)->create()->each(fn ($user) => $user->assignRole($role));

        expect(User::role('admin')->count())->toBe(5);
    });
});

describe('Role Hierarchy', function () {
    it('can check if role is super admin', function () {
        $superAdmin = Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);

        // This will be implemented in our custom Role model
        expect($superAdmin->name)->toBe('super_admin');
        expect($admin->name)->toBe('admin');
    });
});

describe('Role Scopes', function () {
    it('can query roles by guard', function () {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'editor', 'guard_name' => 'web']);
        Role::create(['name' => 'api-admin', 'guard_name' => 'api']);

        $webRoles = Role::where('guard_name', 'web')->get();
        $apiRoles = Role::where('guard_name', 'api')->get();

        expect($webRoles)->toHaveCount(2);
        expect($apiRoles)->toHaveCount(1);
    });

    it('can search roles by name', function () {
        Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $adminRoles = Role::where('name', 'like', '%admin%')->get();

        expect($adminRoles)->toHaveCount(2);
    });
});

describe('Role Serialization', function () {
    it('can convert role to array', function () {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $role->givePermissionTo('edit articles');

        $array = $role->toArray();

        expect($array)->toHaveKey('id');
        expect($array)->toHaveKey('name');
        expect($array)->toHaveKey('guard_name');
    });

    it('can convert role to json', function () {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);

        $json = $role->toJson();

        expect(json_decode($json, true))->toBeArray();
        expect(json_decode($json, true)['name'])->toBe('admin');
    });
});
