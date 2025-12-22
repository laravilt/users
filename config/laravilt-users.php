<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Plugin Enabled
    |--------------------------------------------------------------------------
    |
    | Enable or disable the users plugin entirely.
    |
    */
    'enabled' => env('LARAVILT_USERS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The user model that should be used for the users resource.
    | This should be a model that uses the HasRolesAndPermissions trait.
    |
    */
    'model' => \App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Resources Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which resources should be registered.
    |
    */
    'resources' => [
        'user' => true,
        'role' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Features Configuration
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features of the users plugin.
    |
    */
    'features' => [
        'impersonation' => false,
        'avatar' => false,
        'teams' => false,
        'email_verification' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation Configuration
    |--------------------------------------------------------------------------
    |
    | Configure navigation settings for the users resources.
    |
    */
    'navigation' => [
        'group' => 'Users & Roles',
        'icon' => 'Users',
        'sort' => 10,
        'user' => [
            'label' => 'Users',
            'icon' => 'Users',
            'sort' => 1,
        ],
        'role' => [
            'label' => 'Roles',
            'icon' => 'Shield',
            'sort' => 2,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Super Admin Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the super admin role behavior.
    |
    | - enabled: Whether the super admin role feature is enabled
    | - role: The name of the super admin role
    | - bypass_permissions: When true, super_admin bypasses ALL permission checks
    |   When false (default), super_admin respects assigned permissions like any other role
    | - define_via_gate: Whether to use Laravel Gate::before for the bypass
    |
    */
    'super_admin' => [
        'enabled' => true,
        'role' => 'super_admin',
        'bypass_permissions' => false, // Set to true to give super_admin full access regardless of permissions
        'define_via_gate' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Impersonation Configuration
    |--------------------------------------------------------------------------
    |
    | Configure impersonation behavior.
    |
    */
    'impersonation' => [
        'enabled' => true,
        'redirect_to' => '/admin',
        'back_to' => '/admin',
        'guard' => 'web',
        'allow_super_admin' => false,
        'protected_roles' => ['super_admin'],
        'restrict_to_roles' => ['super_admin', 'admin'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions Configuration
    |--------------------------------------------------------------------------
    |
    | Configure permission generation and naming.
    |
    */
    'permissions' => [
        'separator' => '_',
        'case' => 'snake', // snake, kebab, camel, pascal
        'prefixes' => [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
            'restore',
            'force_delete',
            'replicate',
            'reorder',
        ],
        'custom' => [
            'impersonate users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Model
    |--------------------------------------------------------------------------
    |
    | The role model to use. Defaults to Spatie's Role model.
    |
    */
    'role_model' => \Spatie\Permission\Models\Role::class,

    /*
    |--------------------------------------------------------------------------
    | Permission Model
    |--------------------------------------------------------------------------
    |
    | The permission model to use. Defaults to Spatie's Permission model.
    |
    */
    'permission_model' => \Spatie\Permission\Models\Permission::class,

    /*
    |--------------------------------------------------------------------------
    | Guard Name
    |--------------------------------------------------------------------------
    |
    | The default guard for roles and permissions.
    |
    */
    'guard_name' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Avatar Configuration
    |--------------------------------------------------------------------------
    |
    | Configure avatar settings.
    |
    */
    'avatar' => [
        'enabled' => true,
        'collection' => 'avatar',
        'disk' => 'public',
        'directory' => 'avatars',
        'fallback' => 'https://ui-avatars.com/api/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Configuration
    |--------------------------------------------------------------------------
    |
    | Configure table display settings.
    |
    */
    'table' => [
        'default_sort' => 'created_at',
        'default_sort_direction' => 'desc',
        'per_page' => 25,
        'searchable' => ['name', 'email'],
    ],
];
