<?php

use Laravilt\Users\Tests\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->actingAsUser();
});

describe('Permission Model', function () {
    it('can create a permission', function () {
        $permission = Permission::create([
            'name' => 'edit articles',
            'guard_name' => 'web',
        ]);

        expect($permission)
            ->name->toBe('edit articles')
            ->guard_name->toBe('web');

        $this->assertDatabaseHas('permissions', [
            'name' => 'edit articles',
            'guard_name' => 'web',
        ]);
    });

    it('can update a permission', function () {
        $permission = Permission::create([
            'name' => 'edit articles',
            'guard_name' => 'web',
        ]);

        $permission->update(['name' => 'modify articles']);

        expect($permission->fresh()->name)->toBe('modify articles');
    });

    it('can delete a permission', function () {
        $permission = Permission::create([
            'name' => 'edit articles',
            'guard_name' => 'web',
        ]);
        $permissionId = $permission->id;

        $permission->delete();

        $this->assertDatabaseMissing('permissions', ['id' => $permissionId]);
    });

    it('prevents duplicate permission names for same guard', function () {
        Permission::create([
            'name' => 'edit articles',
            'guard_name' => 'web',
        ]);

        expect(fn () => Permission::create([
            'name' => 'edit articles',
            'guard_name' => 'web',
        ]))->toThrow(\Spatie\Permission\Exceptions\PermissionAlreadyExists::class);
    });

    it('allows same permission name for different guards', function () {
        Permission::create([
            'name' => 'edit articles',
            'guard_name' => 'web',
        ]);

        $apiPermission = Permission::create([
            'name' => 'edit articles',
            'guard_name' => 'api',
        ]);

        expect($apiPermission)->toBeInstanceOf(Permission::class);
        expect(Permission::where('name', 'edit articles')->count())->toBe(2);
    });
});

describe('Permission Roles', function () {
    it('can get roles that have a permission', function () {
        $permission = Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);

        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $editorRole = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $viewerRole = Role::create(['name' => 'viewer', 'guard_name' => 'web']);

        $adminRole->givePermissionTo($permission);
        $editorRole->givePermissionTo($permission);

        $rolesWithPermission = $permission->roles;

        expect($rolesWithPermission)->toHaveCount(2);
        expect($rolesWithPermission->pluck('name')->toArray())->toContain('admin', 'editor');
        expect($rolesWithPermission->pluck('name')->toArray())->not->toContain('viewer');
    });

    it('can count roles with a permission', function () {
        $permission = Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);

        Role::create(['name' => 'admin', 'guard_name' => 'web'])->givePermissionTo($permission);
        Role::create(['name' => 'editor', 'guard_name' => 'web'])->givePermissionTo($permission);
        Role::create(['name' => 'author', 'guard_name' => 'web'])->givePermissionTo($permission);

        expect($permission->roles()->count())->toBe(3);
    });
});

describe('Permission Users', function () {
    it('can get users with direct permission', function () {
        $permission = Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $user1->givePermissionTo($permission);
        $user2->givePermissionTo($permission);

        $usersWithPermission = User::permission('edit articles')->get();

        expect($usersWithPermission)->toHaveCount(2);
        expect($usersWithPermission->pluck('id')->toArray())->toContain($user1->id, $user2->id);
    });

    it('can get users with permission through role', function () {
        $permission = Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $role->givePermissionTo($permission);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $user1->assignRole($role);

        $usersWithPermission = User::permission('edit articles')->get();

        expect($usersWithPermission)->toHaveCount(1);
        expect($usersWithPermission->first()->id)->toBe($user1->id);
    });
});

describe('Permission Groups', function () {
    it('can group permissions by resource', function () {
        // Create permissions following a naming convention
        Permission::create(['name' => 'view_users', 'guard_name' => 'web']);
        Permission::create(['name' => 'create_users', 'guard_name' => 'web']);
        Permission::create(['name' => 'update_users', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete_users', 'guard_name' => 'web']);
        Permission::create(['name' => 'view_articles', 'guard_name' => 'web']);
        Permission::create(['name' => 'create_articles', 'guard_name' => 'web']);

        $userPermissions = Permission::where('name', 'like', '%_users')->get();
        $articlePermissions = Permission::where('name', 'like', '%_articles')->get();

        expect($userPermissions)->toHaveCount(4);
        expect($articlePermissions)->toHaveCount(2);
    });

    it('can get all unique permission prefixes', function () {
        Permission::create(['name' => 'view_users', 'guard_name' => 'web']);
        Permission::create(['name' => 'create_users', 'guard_name' => 'web']);
        Permission::create(['name' => 'view_articles', 'guard_name' => 'web']);

        $permissions = Permission::all();
        $prefixes = $permissions->map(function ($permission) {
            return explode('_', $permission->name)[0] ?? null;
        })->unique()->values();

        expect($prefixes)->toContain('view', 'create');
    });
});

describe('Permission Scopes', function () {
    it('can query permissions by guard', function () {
        Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete articles', 'guard_name' => 'web']);
        Permission::create(['name' => 'api:read', 'guard_name' => 'api']);

        $webPermissions = Permission::where('guard_name', 'web')->get();
        $apiPermissions = Permission::where('guard_name', 'api')->get();

        expect($webPermissions)->toHaveCount(2);
        expect($apiPermissions)->toHaveCount(1);
    });

    it('can search permissions by name', function () {
        Permission::create(['name' => 'view articles', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete users', 'guard_name' => 'web']);

        $articlePermissions = Permission::where('name', 'like', '%articles%')->get();

        expect($articlePermissions)->toHaveCount(2);
    });
});

describe('Permission Serialization', function () {
    it('can convert permission to array', function () {
        $permission = Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);

        $array = $permission->toArray();

        expect($array)->toHaveKey('id');
        expect($array)->toHaveKey('name');
        expect($array)->toHaveKey('guard_name');
    });

    it('can convert permission to json', function () {
        $permission = Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);

        $json = $permission->toJson();

        expect(json_decode($json, true))->toBeArray();
        expect(json_decode($json, true)['name'])->toBe('edit articles');
    });
});

describe('Permission Cache', function () {
    it('can refresh permission cache', function () {
        // Create permission
        $permission = Permission::create(['name' => 'cached permission', 'guard_name' => 'web']);

        // Clear cache
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Permission should still be retrievable
        expect(Permission::findByName('cached permission'))->toBeInstanceOf(Permission::class);
    });
});
