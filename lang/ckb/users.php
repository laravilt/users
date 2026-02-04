<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Users Plugin Language Lines
    |--------------------------------------------------------------------------
    */

    // Navigation
    'navigation' => [
        'group' => 'بەکارهێنەران & ڕۆڵەکان',
        'users' => 'بەکارهێنەران',
        'roles' => 'ڕۆڵەکان',
    ],

    // Resource labels
    'resource' => [
        'user' => 'بەکارهێنەر',
        'users' => 'بەکارهێنەران',
        'role' => 'ڕۆڵ',
        'roles' => 'ڕۆڵەکان',
    ],

    // Page titles
    'pages' => [
        'list_users' => 'لیستی بەکارهێنەران',
        'create_user' => 'دروستکردنی بەکارهێنەر',
        'edit_user' => 'دەستکاریکردنی بەکارهێنەر',
        'view_user' => 'بینینی بەکارهێنەر',
        'list_roles' => 'لیستی ڕۆڵەکان',
        'create_role' => 'دروستکردنی ڕۆڵ',
        'edit_role' => 'دەستکاریکردنی ڕۆڵ',
        'view_role' => 'بینینی ڕۆڵ',
    ],

    // Form sections
    'form' => [
        'avatar_section' => 'وێنەی پرۆفایل',
        'avatar_section_description' => 'وێنەیەکی پرۆفایل بۆ ئەم بەکارهێنەرە باربکە',
        'user_information' => 'زانیاری بەکارهێنەر',
        'user_information_description' => 'زانیارییە بنەڕەتییەکانی بەکارهێنەر',
        'password_section' => 'تێپەڕەوشە',
        'password_section_description' => 'تێپەڕەوشەی بەکارهێنەر دابنێ (بۆ هێشتنەوەی ئەوەی ئێستا بە بەتاڵی بەجێی بهێڵە)',
        'roles_section' => 'ڕۆڵەکان & دەسەڵاتەکان',
        'roles_section_description' => 'ڕۆڵەکان بۆ ئەم بەکارهێنەرە دیاری بکە',
        'timestamps' => 'مۆری کات',
        'role_information' => 'زانیاری ڕۆڵ',
        'role_information_description' => 'زانیارییە بنەڕەتییەکانی ڕۆڵ',
        'permissions_section' => 'دەسەڵاتەکان',
        'permissions_section_description' => 'دەسەڵاتەکان بۆ ئەم ڕۆڵە هەڵبژێرە',
    ],

    // Fields
    'fields' => [
        'id' => 'ژ.',
        'name' => 'ناو',
        'email' => 'ئیمەیڵ',
        'password' => 'تێپەڕەوشە',
        'password_confirmation' => 'پشتڕاستکردنەوەی تێپەڕەوشە',
        'avatar' => 'وێنەی کەسی',
        'roles' => 'ڕۆڵەکان',
        'email_verified' => 'ئیمەیڵ پشتڕاستکراوەتەوە',
        'created_at' => 'دروستکرا لە',
        'updated_at' => 'نوێکرایەوە لە',
        'guard_name' => 'پارێزەر',
        'permissions' => 'دەسەڵاتەکان',
        'permissions_count' => 'ژمارەی دەسەڵاتەکان',
        'users_count' => 'ژمارەی بەکارهێنەران',
    ],

    // Filters
    'filters' => [
        'role' => 'پاڵاوتن بەپێی ڕۆڵ',
        'guard' => 'پاڵاوتن بەپێی پارێزەر',
    ],

    // Messages
    'messages' => [
        'email_copied' => 'ئیمەیڵ بۆ کلیپبۆرد کۆپی کرا',
        'not_verified' => 'پشتڕاستنەکراوەتەوە',
        'no_roles' => 'هیچ ڕۆڵێک دیاری نەکراوە',
        'no_permissions' => 'هیچ دەسەڵاتێک دیاری نەکراوە',
        'created' => 'بە سەرکەوتوویی دروستکرا.',
        'updated' => 'بە سەرکەوتوویی نوێکرایەوە.',
        'deleted' => 'بە سەرکەوتوویی سڕایەوە.',
        'cannot_delete_system' => 'ناتوانیت ڕۆڵەکانی سیستەم بسڕیتەوە.',
    ],

    // Actions
    'actions' => [
        'impersonate' => 'خۆنواندن',
        'impersonate_tooltip' => 'چوونەژوورەوە وەک ئەم بەکارهێنەرە',
        'impersonate_heading' => 'خۆنواندن وەک بەکارهێنەر',
        'impersonate_description' => 'تۆ خەریکە وەک ئەم بەکارهێنەرە دەچیتە ژوورەوە. دانیشتنی ئێستات پاشەکەوت دەکرێت.',
        'impersonate_confirm' => 'دەستپێکردنی خۆنواندن',
    ],

    // Notifications
    'notifications' => [
        'impersonating' => 'تۆ ئێستا خۆت وەک ئەم بەکارهێنەرە دەنوێنیت',
        'stopped_impersonating' => 'تۆ وازت لە خۆنواندن هێنا',
    ],

    // Impersonation
    'impersonation' => [
        'banner' => [
            'message' => 'تۆ خۆت وەک :name دەنوێنیت',
            'stop' => 'وازهێنان لە خۆنواندن',
        ],
        'messages' => [
            'started' => 'تۆ ئێستا خۆت وەک :name دەنوێنیت',
            'stopped' => 'تۆ وازت لە خۆنواندن هێنا',
            'cannot_impersonate_self' => 'ناتوانیت خۆت وەک خۆت بنوێنیت.',
            'cannot_impersonate_super_admin' => 'ناتوانیت خۆت وەک بەڕێوەبەری سەرەکی بنوێنیت.',
            'unauthorized' => 'تۆ دەسەڵاتی خۆنواندنی بەکارهێنەرانت نییە.',
        ],
    ],

    // Permissions
    'permissions' => [
        'view_any' => 'بینینی هەمووی',
        'view' => 'بینین',
        'create' => 'دروستکردن',
        'update' => 'نوێکردنەوە',
        'delete' => 'سڕینەوە',
        'restore' => 'گەڕاندنەوە',
        'force_delete' => 'سڕینەوەی بەزۆر',
        'replicate' => 'دووبارەکردنەوە',
        'reorder' => 'ڕێکخستنەوە',
        'impersonate' => 'خۆنواندن',
    ],

    // Commands
    'commands' => [
        'install' => [
            'installing' => 'دامەزراندنی پێوەکراوی Laravilt Users...',
            'success' => 'پێوەکراوی Laravilt Users بە سەرکەوتوویی دامەزرا!',
        ],
        'secure' => [
            'creating_permissions' => 'دروستکردنی دەسەڵاتەکان...',
            'creating_roles' => 'دروستکردنی ڕۆڵەکان...',
            'success' => 'دەسەڵاتەکان و ڕۆڵەکانی ئاسایش بە سەرکەوتوویی دانران!',
        ],
    ],
];
