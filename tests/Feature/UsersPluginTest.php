<?php

use Laravilt\Users\UsersPlugin;

describe('Users Plugin', function () {
    it('can create plugin instance', function () {
        $plugin = UsersPlugin::make();

        expect($plugin)->toBeInstanceOf(UsersPlugin::class);
    });

    it('has correct plugin id', function () {
        $plugin = UsersPlugin::make();

        expect($plugin->getId())->toBe('users');
    });

    it('can enable user resource', function () {
        $plugin = UsersPlugin::make()
            ->userResource(true);

        expect($plugin->hasUserResource())->toBeTrue();
    });

    it('can disable user resource', function () {
        $plugin = UsersPlugin::make()
            ->userResource(false);

        expect($plugin->hasUserResource())->toBeFalse();
    });

    it('can enable role resource', function () {
        $plugin = UsersPlugin::make()
            ->roleResource(true);

        expect($plugin->hasRoleResource())->toBeTrue();
    });

    it('can disable role resource', function () {
        $plugin = UsersPlugin::make()
            ->roleResource(false);

        expect($plugin->hasRoleResource())->toBeFalse();
    });

    it('can enable impersonation', function () {
        $plugin = UsersPlugin::make()
            ->impersonation(true);

        expect($plugin->hasImpersonation())->toBeTrue();
    });

    it('can disable impersonation', function () {
        $plugin = UsersPlugin::make()
            ->impersonation(false);

        expect($plugin->hasImpersonation())->toBeFalse();
    });

    it('can enable avatar', function () {
        $plugin = UsersPlugin::make()
            ->avatar(true);

        expect($plugin->hasAvatar())->toBeTrue();
    });

    it('can set navigation group', function () {
        $plugin = UsersPlugin::make()
            ->navigationGroup('Access Control');

        expect($plugin->getNavigationGroup())->toBe('Access Control');
    });

    it('can set navigation sort', function () {
        $plugin = UsersPlugin::make()
            ->navigationSort(5);

        expect($plugin->getNavigationSort())->toBe(5);
    });

    it('can set custom user model', function () {
        $plugin = UsersPlugin::make()
            ->userModel(\App\Models\User::class);

        expect($plugin->getUserModel())->toBe(\App\Models\User::class);
    });

    it('uses default user model from config', function () {
        config()->set('laravilt-users.model', \App\Models\CustomUser::class);

        $plugin = UsersPlugin::make();

        expect($plugin->getUserModel())->toBe(\App\Models\CustomUser::class);
    });
});

describe('Plugin Configuration', function () {
    it('can load configuration from file', function () {
        expect(config('laravilt-users.enabled'))->toBeTrue();
    });

    it('can configure features', function () {
        expect(config('laravilt-users.features.impersonation'))->toBeTrue();
        expect(config('laravilt-users.features.avatar'))->toBeTrue();
    });

    it('can configure navigation', function () {
        expect(config('laravilt-users.navigation.group'))->toBe('Users & Roles');
        expect(config('laravilt-users.navigation.icon'))->toBe('Users');
    });
});
