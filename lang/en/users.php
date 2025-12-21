<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Users Plugin Language Lines
    |--------------------------------------------------------------------------
    */

    // Navigation
    'navigation' => [
        'group' => 'Users & Roles',
        'users' => 'Users',
        'roles' => 'Roles',
    ],

    // Resource labels
    'resource' => [
        'user' => 'User',
        'users' => 'Users',
        'role' => 'Role',
        'roles' => 'Roles',
    ],

    // Page titles
    'pages' => [
        'list_users' => 'Users',
        'create_user' => 'Create User',
        'edit_user' => 'Edit User',
        'view_user' => 'View User',
        'list_roles' => 'Roles',
        'create_role' => 'Create Role',
        'edit_role' => 'Edit Role',
        'view_role' => 'View Role',
    ],

    // Form sections
    'form' => [
        'avatar_section' => 'Profile Picture',
        'avatar_section_description' => 'Upload a profile picture for this user',
        'user_information' => 'User Information',
        'user_information_description' => 'Basic user information',
        'password_section' => 'Password',
        'password_section_description' => 'Set the user password (leave empty to keep current)',
        'roles_section' => 'Roles & Permissions',
        'roles_section_description' => 'Assign roles to this user',
        'timestamps' => 'Timestamps',
        'role_information' => 'Role Information',
        'role_information_description' => 'Basic role information',
        'permissions_section' => 'Permissions',
        'permissions_section_description' => 'Select permissions for this role',
    ],

    // Fields
    'fields' => [
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'password_confirmation' => 'Confirm Password',
        'avatar' => 'Avatar',
        'roles' => 'Roles',
        'email_verified' => 'Email Verified',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'guard_name' => 'Guard',
        'permissions' => 'Permissions',
        'permissions_count' => 'Permissions',
        'users_count' => 'Users',
    ],

    // Filters
    'filters' => [
        'role' => 'Filter by Role',
        'guard' => 'Filter by Guard',
    ],

    // Messages
    'messages' => [
        'email_copied' => 'Email copied to clipboard',
        'not_verified' => 'Not verified',
        'no_roles' => 'No roles assigned',
        'no_permissions' => 'No permissions assigned',
        'created' => 'Created successfully.',
        'updated' => 'Updated successfully.',
        'deleted' => 'Deleted successfully.',
        'cannot_delete_system' => 'Cannot delete system roles.',
    ],

    // Actions
    'actions' => [
        'impersonate' => 'Impersonate',
        'impersonate_tooltip' => 'Login as this user',
        'impersonate_heading' => 'Impersonate User',
        'impersonate_description' => 'You are about to login as this user. Your current session will be saved.',
        'impersonate_confirm' => 'Start Impersonation',
    ],

    // Notifications
    'notifications' => [
        'impersonating' => 'You are now impersonating this user',
        'stopped_impersonating' => 'You have stopped impersonating',
    ],

    // Impersonation
    'impersonation' => [
        'banner' => [
            'message' => 'You are impersonating as :name',
            'stop' => 'Stop Impersonation',
        ],
        'messages' => [
            'started' => 'You are now impersonating :name',
            'stopped' => 'You have stopped impersonating',
            'cannot_impersonate_self' => 'You cannot impersonate yourself.',
            'cannot_impersonate_super_admin' => 'You cannot impersonate a super admin.',
            'unauthorized' => 'You are not authorized to impersonate users.',
        ],
    ],

    // Permissions
    'permissions' => [
        'view_any' => 'View Any',
        'view' => 'View',
        'create' => 'Create',
        'update' => 'Update',
        'delete' => 'Delete',
        'restore' => 'Restore',
        'force_delete' => 'Force Delete',
        'replicate' => 'Replicate',
        'reorder' => 'Reorder',
        'impersonate' => 'Impersonate',
    ],

    // Commands
    'commands' => [
        'install' => [
            'installing' => 'Installing Laravilt Users plugin...',
            'success' => 'Laravilt Users plugin installed successfully!',
        ],
        'secure' => [
            'creating_permissions' => 'Creating permissions...',
            'creating_roles' => 'Creating roles...',
            'success' => 'Security permissions and roles have been set up successfully!',
        ],
    ],
];
