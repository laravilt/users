<?php

use Laravilt\Users\Tests\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->actingAsUser();
});

describe('User Model', function () {
    it('can create a user', function () {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        expect($user)
            ->name->toBe('John Doe')
            ->email->toBe('john@example.com');

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    });

    it('can update a user', function () {
        $user = User::factory()->create();

        $user->update([
            'name' => 'Jane Doe',
        ]);

        expect($user->fresh()->name)->toBe('Jane Doe');
    });

    it('can delete a user', function () {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $this->assertDatabaseMissing('users', ['id' => $userId]);
    });

    it('can check if user email is verified', function () {
        $verifiedUser = User::factory()->create();
        $unverifiedUser = User::factory()->unverified()->create();

        expect($verifiedUser->hasVerifiedEmail())->toBeTrue();
        expect($unverifiedUser->hasVerifiedEmail())->toBeFalse();
    });
});

describe('User Roles', function () {
    it('can assign a role to user', function () {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);

        $user->assignRole($role);

        expect($user->hasRole('admin'))->toBeTrue();
    });

    it('can assign multiple roles to user', function () {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $user->assignRole(['admin', 'editor']);

        expect($user->hasRole('admin'))->toBeTrue();
        expect($user->hasRole('editor'))->toBeTrue();
        expect($user->roles)->toHaveCount(2);
    });

    it('can remove a role from user', function () {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);

        $user->assignRole($role);
        expect($user->hasRole('admin'))->toBeTrue();

        $user->removeRole($role);
        expect($user->fresh()->hasRole('admin'))->toBeFalse();
    });

    it('can sync roles for user', function () {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'editor', 'guard_name' => 'web']);
        Role::create(['name' => 'viewer', 'guard_name' => 'web']);

        $user->assignRole(['admin', 'editor']);
        expect($user->roles)->toHaveCount(2);

        $user->syncRoles(['viewer']);
        expect($user->fresh()->roles)->toHaveCount(1);
        expect($user->fresh()->hasRole('viewer'))->toBeTrue();
    });

    it('can check if user has any of given roles', function () {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $user->assignRole('admin');

        expect($user->hasAnyRole(['admin', 'editor']))->toBeTrue();
        expect($user->hasAnyRole(['editor', 'viewer']))->toBeFalse();
    });

    it('can check if user has all given roles', function () {
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $user->assignRole(['admin', 'editor']);

        expect($user->hasAllRoles(['admin', 'editor']))->toBeTrue();
        expect($user->hasAllRoles(['admin', 'viewer']))->toBeFalse();
    });
});

describe('User Permissions', function () {
    it('can give direct permission to user', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('edit articles');

        expect($user->hasPermissionTo('edit articles'))->toBeTrue();
    });

    it('can give permission through role', function () {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'editor', 'guard_name' => 'web']);
        $role->givePermissionTo('edit articles');

        $user->assignRole($role);

        expect($user->hasPermissionTo('edit articles'))->toBeTrue();
    });

    it('can revoke permission from user', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('edit articles');

        expect($user->hasPermissionTo('edit articles'))->toBeTrue();

        $user->revokePermissionTo('edit articles');

        expect($user->fresh()->hasDirectPermission('edit articles'))->toBeFalse();
    });

    it('can sync permissions for user', function () {
        $user = User::factory()->create();
        $user->givePermissionTo(['edit articles', 'delete articles']);

        expect($user->permissions)->toHaveCount(2);

        $user->syncPermissions(['publish articles']);

        expect($user->fresh()->permissions)->toHaveCount(1);
        expect($user->fresh()->hasPermissionTo('publish articles'))->toBeTrue();
    });

    it('can check if user has any permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('edit articles');

        expect($user->hasAnyPermission(['edit articles', 'delete articles']))->toBeTrue();
        expect($user->hasAnyPermission(['delete articles', 'publish articles']))->toBeFalse();
    });

    it('can check if user has all permissions', function () {
        $user = User::factory()->create();
        $user->givePermissionTo(['edit articles', 'delete articles']);

        expect($user->hasAllPermissions(['edit articles', 'delete articles']))->toBeTrue();
        expect($user->hasAllPermissions(['edit articles', 'publish articles']))->toBeFalse();
    });
});

describe('User Avatar', function () {
    it('can set avatar for user', function () {
        $user = User::factory()->withAvatar()->create();

        expect($user->avatar)->not->toBeNull();
    });

    it('can get avatar url', function () {
        $user = User::factory()->create(['name' => 'John Doe']);

        // Test the getAvatarUrl method (to be implemented)
        expect($user->getAvatarUrl())->toContain('John');
    });
});

describe('User Queries', function () {
    it('can filter users by role', function () {
        $admin = User::factory()->create();
        $editor = User::factory()->create();
        $viewer = User::factory()->create();

        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $admin->assignRole('admin');
        $editor->assignRole('editor');

        $admins = User::role('admin')->get();

        expect($admins)->toHaveCount(1);
        expect($admins->first()->id)->toBe($admin->id);
    });

    it('can filter users by permission', function () {
        $userWithPermission = User::factory()->create();
        $userWithoutPermission = User::factory()->create();

        $userWithPermission->givePermissionTo('edit articles');

        $users = User::permission('edit articles')->get();

        expect($users)->toHaveCount(1);
        expect($users->first()->id)->toBe($userWithPermission->id);
    });

    it('can filter verified users', function () {
        User::factory()->count(3)->create();
        User::factory()->unverified()->count(2)->create();

        $verifiedUsers = User::whereNotNull('email_verified_at')->get();
        $unverifiedUsers = User::whereNull('email_verified_at')->get();

        expect($verifiedUsers)->toHaveCount(4); // 3 + 1 from actingAsUser
        expect($unverifiedUsers)->toHaveCount(2);
    });
});
