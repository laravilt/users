<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Users Plugin Language Lines (Arabic)
    |--------------------------------------------------------------------------
    */

    // Navigation
    'navigation' => [
        'group' => 'المستخدمون والأدوار',
        'users' => 'المستخدمون',
        'roles' => 'الأدوار',
    ],

    // Resource labels
    'resource' => [
        'user' => 'مستخدم',
        'users' => 'المستخدمون',
        'role' => 'دور',
        'roles' => 'الأدوار',
    ],

    // Page titles
    'pages' => [
        'list_users' => 'المستخدمون',
        'create_user' => 'إنشاء مستخدم',
        'edit_user' => 'تعديل مستخدم',
        'view_user' => 'عرض مستخدم',
        'list_roles' => 'الأدوار',
        'create_role' => 'إنشاء دور',
        'edit_role' => 'تعديل دور',
        'view_role' => 'عرض دور',
    ],

    // Form sections
    'form' => [
        'avatar_section' => 'الصورة الشخصية',
        'avatar_section_description' => 'قم بتحميل صورة شخصية لهذا المستخدم',
        'user_information' => 'معلومات المستخدم',
        'user_information_description' => 'المعلومات الأساسية للمستخدم',
        'password_section' => 'كلمة المرور',
        'password_section_description' => 'تعيين كلمة مرور المستخدم (اتركها فارغة للإبقاء على الحالية)',
        'roles_section' => 'الأدوار والصلاحيات',
        'roles_section_description' => 'تعيين الأدوار لهذا المستخدم',
        'timestamps' => 'التواريخ',
        'role_information' => 'معلومات الدور',
        'role_information_description' => 'المعلومات الأساسية للدور',
        'permissions_section' => 'الصلاحيات',
        'permissions_section_description' => 'اختر الصلاحيات لهذا الدور',
    ],

    // Fields
    'fields' => [
        'id' => 'المعرف',
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'avatar' => 'الصورة الشخصية',
        'roles' => 'الأدوار',
        'email_verified' => 'تم التحقق من البريد',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
        'guard_name' => 'الحارس',
        'permissions' => 'الصلاحيات',
        'permissions_count' => 'عدد الصلاحيات',
        'users_count' => 'عدد المستخدمين',
    ],

    // Filters
    'filters' => [
        'role' => 'تصفية حسب الدور',
        'guard' => 'تصفية حسب الحارس',
    ],

    // Messages
    'messages' => [
        'email_copied' => 'تم نسخ البريد الإلكتروني',
        'not_verified' => 'لم يتم التحقق',
        'no_roles' => 'لا توجد أدوار مخصصة',
        'no_permissions' => 'لا توجد صلاحيات مخصصة',
        'created' => 'تم الإنشاء بنجاح.',
        'updated' => 'تم التحديث بنجاح.',
        'deleted' => 'تم الحذف بنجاح.',
        'cannot_delete_system' => 'لا يمكن حذف أدوار النظام.',
    ],

    // Actions
    'actions' => [
        'impersonate' => 'انتحال الهوية',
        'impersonate_tooltip' => 'تسجيل الدخول كهذا المستخدم',
        'impersonate_heading' => 'انتحال هوية المستخدم',
        'impersonate_description' => 'أنت على وشك تسجيل الدخول كهذا المستخدم. سيتم حفظ جلستك الحالية.',
        'impersonate_confirm' => 'بدء الانتحال',
    ],

    // Notifications
    'notifications' => [
        'impersonating' => 'أنت الآن تنتحل هوية هذا المستخدم',
        'stopped_impersonating' => 'لقد توقفت عن انتحال الهوية',
    ],

    // Impersonation
    'impersonation' => [
        'banner' => [
            'message' => 'أنت تنتحل هوية :name',
            'stop' => 'إيقاف الانتحال',
        ],
        'messages' => [
            'started' => 'أنت الآن تنتحل هوية :name',
            'stopped' => 'لقد توقفت عن انتحال الهوية',
            'cannot_impersonate_self' => 'لا يمكنك انتحال هويتك.',
            'cannot_impersonate_super_admin' => 'لا يمكنك انتحال هوية المسؤول الأعلى.',
            'unauthorized' => 'غير مصرح لك بانتحال هوية المستخدمين.',
        ],
    ],

    // Permissions
    'permissions' => [
        'view_any' => 'عرض الكل',
        'view' => 'عرض',
        'create' => 'إنشاء',
        'update' => 'تحديث',
        'delete' => 'حذف',
        'restore' => 'استعادة',
        'force_delete' => 'حذف نهائي',
        'replicate' => 'نسخ',
        'reorder' => 'إعادة ترتيب',
        'impersonate' => 'انتحال الهوية',
    ],

    // Commands
    'commands' => [
        'install' => [
            'installing' => 'جاري تثبيت إضافة المستخدمين...',
            'success' => 'تم تثبيت إضافة المستخدمين بنجاح!',
        ],
        'secure' => [
            'creating_permissions' => 'جاري إنشاء الصلاحيات...',
            'creating_roles' => 'جاري إنشاء الأدوار...',
            'success' => 'تم إعداد صلاحيات وأدوار الأمان بنجاح!',
        ],
    ],
];
