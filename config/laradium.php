<?php

return [
    'default_models_directory' => 'App\\Models',

    'resource_path' => 'App\\Laradium\\Resources',

    'file_size' => '2024', // default max file upload size 2mb

    'translations_file' => 'translations',

    'user'      => [
        'email'    => 'admin@laradium.com',
        'password' => 'laradium2018'
    ],

    // Default language list
    'languages' => [
        [
            'iso_code'        => 'en',
            'title'           => 'English',
            'title_localized' => 'English',
            'is_visible'      => true,
            'is_fallback'     => true,
        ],
        [
            'iso_code'        => 'lv',
            'title'           => 'Latvian',
            'title_localized' => 'Latviešu',
            'is_visible'      => true,
            'is_fallback'     => false,
        ],
    ],

    'dashboard-view' => 'laradium::admin.dashboard',

    'menus' => [
        'Admin menu' => [
//            [
//                'is_active'    => 1,
//                'translations' => [
//                    'name' => 'Translations',
//                    'url'  => '/admin/translations',
//                ]
//            ],
        ]
    ]
];