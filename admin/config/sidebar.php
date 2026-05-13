<?php

return [

    [
        'key' => 'dashboard',
        'title' => 'Dashboard',
        'icon' => 'fa fa-desktop',
        'url' => SITE_URL,
        'routes' => ['home', ''],
        'permission' => ['view_dashboard'],
        'permission_mode' => '',
        'student_allowed' => true
    ],

    [
        'key' => 'franchise',
        'title' => 'Franchises',
        'icon' => 'fa fa-university',
        'routes' => [
            'view_franchises',
            'add_franchise',
            'edit_franchise'
        ],
        'permission' => ['view_franchise'],
        'permission_mode' => '',

        'children' => [

            [
                'title' => 'Franchise List',
                'icon' => 'fa fa-list',
                'url' => '?route=view_franchises',
                'route' => 'view_franchises',
                'permission' => 'view_franchise'
            ],

            [
                'title' => 'Add New Franchise',
                'icon' => 'fa fa-plus-circle',
                'url' => '?route=add_franchise',
                'route' => 'add_franchise',
                'permission' => 'create_franchise'
            ]

        ]
    ],

    [
        'key' => 'courses',
        'title' => 'Courses',
        'icon' => 'fa fa-laptop',
        'routes' => [
            'view_courses',
            'add_course',
            'edit_course'
        ],
        'permission' => ['view_course'],
        'permission_mode' => '',

        'children' => [

            [
                'title' => 'Course List',
                'icon' => 'fa fa-list',
                'url' => '?route=view_courses',
                'route' => 'view_courses',
                'permission' => 'view_course'
            ],

            [
                'title' => 'Add New Course',
                'icon' => 'fa fa-plus-circle',
                'url' => '?route=add_course',
                'route' => 'add_course',
                'permission' => 'create_course'
            ]

        ]
    ],

    [
        'key' => 'students',
        'title' => 'Students',
        'icon' => 'fa fa-mortar-board',
        'routes' => [
            'view_students',
            'add_student',
            'edit_student',
            'student_admission',
            'manage_temp_students'
        ],
        'permission' => ['view_student'],
        'permission_mode' => '',

        'children' => [

            [
                'title' => 'Student List',
                'icon' => 'fa fa-list',
                'url' => '?route=view_students',
                'route' => 'view_students',
                'permission' => 'view_student'
            ],

            [
                'title' => 'Temporary Students',
                'icon' => 'fa fa-list',
                'url' => '?route=manage_temp_students',
                'route' => 'manage_temp_students',
                'permission' => 'view_student',

                'custom_condition' => function () {
                    return $_SESSION['user_type'] == 'franchise'
                        ? ($_SESSION['owned_status'] == 'yes')
                        : true;
                }
            ],

            [
                'title' => 'Add New Student',
                'icon' => 'fa fa-plus-circle',
                'url' => '?route=add_student',
                'route' => 'add_student',
                'permission' => 'create_student'
            ]

        ]
    ],

    [
        'key' => 'receipt',
        'title' => 'Receipts',
        'icon' => 'fa fa-money',
        'routes' => [
            'view_receipts',
            'view_due_students',
        ],
        'permission' => ['view_receipt'],
        'permission_mode' => '',

        'children' => [

            [
                'title' => 'Regular Receipts',
                'icon' => 'fa fa-list',
                'url' => '?route=view_receipts',
                'route' => 'view_receipts',
                'permission' => 'view_receipt'
            ],

            [
                'title' => 'View Due Students',
                'icon' => 'fa fa-plus-users',
                'url' => '?route=view_due_students',
                'route' => 'view_due_students',
                'permission' => 'view_due_students'
            ]

        ]
    ],

    [
        'key' => 'students',
        'title' => 'Students',
        'icon' => 'fa fa-mortar-board',
        'routes' => [
            'view_exams',
            'add_exam',
            'edit_exam',
            'manage_questions',
        ],
        'permission' => ['view_exam'],
        'permission_mode' => '',

        'children' => [

            [
                'title' => 'Exams',
                'icon' => 'fa fa-list',
                'url' => '?route=view_exams',
                'route' => 'view_exams',
                'permission' => 'view_exam'
            ],

            [
                'title' => 'Add New Exam',
                'icon' => 'fa fa-plus-circle',
                'url' => '?route=add_exam',
                'route' => 'add_exam',
                'permission' => 'create_exam'
            ]

        ]
    ],

    [
        'key' => 'cms_management',
        'title' => 'CMS Management',
        'icon' => 'fa fa-pencil-square-o',
        'routes' => [
            'gallery',
            'home_sliders',
            'manage_cities',
        ],
        'permission' => ['gallery', 'view_category', 'manage_home_slider', 'manage_city_db'],
        'permission_mode' => 'OR',

        'children' => [

            [
                'title' => 'Gallery',
                'icon' => 'fa fa-picture-o',
                'url' => '?route=gallery',
                'route' => 'gallery',
                'permission' => 'view_gallery'
            ],

            [
                'title' => 'Category',
                'icon' => 'fa fa-sitemap',
                'url' => '?route=view_category',
                'route' => 'view_category',
                'permission' => 'view_category'
            ],

            [
                'title' => 'Home Slider',
                'icon' => 'fa fa-picture-o',
                'url' => '?route=home_sliders',
                'route' => 'home_sliders',
                'permission' => 'manage_home_slider'
            ],

            [
                'title' => 'Manage City DB',
                'icon' => 'fa fa-building-o',
                'url' => '?route=manage_cities',
                'route' => 'manage_cities',
                'permission' => 'manage_city_db'
            ]

        ]
    ],

    [
        'key' => 'email_template',
        'title' => 'Email Templates',
        'icon' => 'fa fa-inbox',
        'routes' => [
            'view_email_templates',
            'add_email_template',
            'edit_email_template'
        ],
        'permission' => ['view_template'],
        'permission_mode' => '',

        'children' => [

            [
                'title' => 'Email Template List',
                'icon' => 'fa fa-list',
                'url' => '?route=view_email_templates',
                'route' => 'view_email_templates',
                'permission' => 'view_template'
            ],

            [
                'title' => 'Add New Template',
                'icon' => 'fa fa-plus-circle',
                'url' => '?route=add_email_template',
                'route' => 'add_email_template',
                'permission' => 'create_template'
            ]

        ]
    ],

    [
        'key' => 'news',
        'title' => 'News',
        'icon' => 'fa fa-question-circle',
        'routes' => [
            'view_news',
            'add_news',
            'edit_news'
        ],
        'permission' => ['view_news'],
        'permission_mode' => '',

        'children' => [

            [
                'title' => 'News List',
                'icon' => 'fa fa-list',
                'url' => '?route=view_news',
                'route' => 'view_news',
                'permission' => 'view_news'
            ],

            [
                'title' => 'Add New News',
                'icon' => 'fa fa-plus-circle',
                'url' => '?route=add_news',
                'route' => 'add_news',
                'permission' => 'create_news'
            ]

        ]
    ],

    [
        'key' => 'enquiry',
        'title' => 'Enquiry',
        'icon' => 'fa fa-envelope-o',
        'url' => SITE_URL . '?route=view_enquiry',
        'routes' => ['view_enquiry', ''],
        'permission' => ['view_enquiry'],
        'permission_mode' => '',
        'student_allowed' => false
    ],

    [
        'key' => 'settings',

        'title' => 'Settings',

        'icon' => 'fa fa-cogs',

        'routes' => [
            'edit_profile',
            'edit_admin_profile',
            'edit_site_setting'
        ],

        'custom_condition' => function () use ($permissionService) {

            return ($permissionService
                ->checkUserRolePermission('update_site_setting')

                ||

                ($permissionService
                    ->checkUserRolePermission('manage_profile')

                    &&

                    $_SESSION['user_type'] != 'student'
                )
            );
        },

        'children' => [

            [
                'title' => 'Manage Profile',

                'icon' => 'fa fa-user-circle',

                'url' => '?route=edit_profile',

                'route' => 'edit_profile',

                'permissions' => [
                    'manage_profile'
                ]
            ],

            [
                'title' => 'Manage Admin Profile',

                'icon' => 'fa fa-user-circle',

                'url' => '?route=edit_admin_profile',

                'route' => 'edit_admin_profile',

                'custom_condition' => function () {

                    return $_SESSION['user_type'] == 'developer';
                }
            ],

            [
                'title' => 'Site Settings',

                'icon' => 'fa fa-cog',

                'url' => '?route=edit_site_setting',

                'route' => 'edit_site_setting',

                'permissions' => [
                    'update_site_setting'
                ]
            ]
        ]
    ],

    [
        'key' => 'log_out',
        'title' => 'Log out',
        'icon' => 'fa fa-sign-out',
        'url' => SITE_URL.'?route=logout',
        'routes' => ['logout', ''],
        'permission' => [],
        'permission_mode' => '',
        'student_allowed' => true
    ]

];
