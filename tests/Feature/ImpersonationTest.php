<?php

use Laravilt\Users\Services\ImpersonationService;
use Laravilt\Users\Tests\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->admin = $this->actingAsUser();
    $this->impersonationService = app(ImpersonationService::class);

    // Create test permissions
    Permission::findOrCreate('impersonate users', 'web');
});

describe('Impersonation Service', function () {
    it('can impersonate another user', function () {
        $targetUser = User::factory()->create();

        $this->impersonationService->impersonate($this->admin, $targetUser);

        expect(auth()->id())->toBe($targetUser->id);
        expect(session()->has('impersonator_id'))->toBeTrue();
        expect(session('impersonator_id'))->toBe($this->admin->id);
    });

    it('can stop impersonation', function () {
        $targetUser = User::factory()->create();

        $this->impersonationService->impersonate($this->admin, $targetUser);
        expect(auth()->id())->toBe($targetUser->id);

        $this->impersonationService->stopImpersonation();

        expect(auth()->id())->toBe($this->admin->id);
        expect(session()->has('impersonator_id'))->toBeFalse();
    });

    it('can check if currently impersonating', function () {
        $targetUser = User::factory()->create();

        expect($this->impersonationService->isImpersonating())->toBeFalse();

        $this->impersonationService->impersonate($this->admin, $targetUser);

        expect($this->impersonationService->isImpersonating())->toBeTrue();
    });

    it('can get impersonator', function () {
        $targetUser = User::factory()->create();

        $this->impersonationService->impersonate($this->admin, $targetUser);

        $impersonator = $this->impersonationService->getImpersonator();

        expect($impersonator)->toBeInstanceOf(User::class);
        expect($impersonator->id)->toBe($this->admin->id);
    });

    it('cannot impersonate self', function () {
        expect(fn () => $this->impersonationService->impersonate($this->admin, $this->admin))
            ->toThrow(\Laravilt\Users\Exceptions\CannotImpersonateSelfException::class);
    });

    it('prevents impersonating super admin by default', function () {
        $superAdmin = User::factory()->create();
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->assignRole('super_admin');

        expect(fn () => $this->impersonationService->impersonate($this->admin, $superAdmin))
            ->toThrow(\Laravilt\Users\Exceptions\CannotImpersonateSuperAdminException::class);
    });
});

describe('Impersonation Authorization', function () {
    it('allows impersonation for users with permission', function () {
        $this->admin->givePermissionTo('impersonate users');
        $targetUser = User::factory()->create();

        expect($this->impersonationService->canImpersonate($this->admin, $targetUser))->toBeTrue();
    });

    it('denies impersonation for users without permission', function () {
        $targetUser = User::factory()->create();

        expect($this->impersonationService->canImpersonate($this->admin, $targetUser))->toBeFalse();
    });

    it('allows impersonation for super admin', function () {
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $this->admin->assignRole('super_admin');
        $targetUser = User::factory()->create();

        expect($this->impersonationService->canImpersonate($this->admin, $targetUser))->toBeTrue();
    });
});

describe('Impersonation Session', function () {
    it('stores impersonation data in session', function () {
        $targetUser = User::factory()->create();

        $this->impersonationService->impersonate($this->admin, $targetUser);

        expect(session('impersonator_id'))->toBe($this->admin->id);
        expect(session('impersonated_at'))->not->toBeNull();
    });

    it('clears impersonation data from session on stop', function () {
        $targetUser = User::factory()->create();

        $this->impersonationService->impersonate($this->admin, $targetUser);
        $this->impersonationService->stopImpersonation();

        expect(session('impersonator_id'))->toBeNull();
        expect(session('impersonated_at'))->toBeNull();
    });
});

describe('Impersonation Restrictions', function () {
    it('can configure impersonation restrictions', function () {
        config()->set('laravilt-users.impersonation.restrict_to_roles', ['admin', 'super_admin']);

        $allowedRoles = config('laravilt-users.impersonation.restrict_to_roles');

        expect($allowedRoles)->toContain('admin', 'super_admin');
    });

    it('can configure protected roles', function () {
        config()->set('laravilt-users.impersonation.protected_roles', ['super_admin']);

        $protectedRoles = config('laravilt-users.impersonation.protected_roles');

        expect($protectedRoles)->toContain('super_admin');
    });
});
