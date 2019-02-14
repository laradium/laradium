<?php

return [
    'template_layout' => 'laradium::layouts.main',

    'default_models_directory' => 'App\\Models',

    'custom_field_directory' => app_path('Laradium/Fields'),

    'custom_field_namespace' => 'App\\Laradium\\Fields',

    'resource_path' => 'App\\Laradium\\Resources',

    'file_size' => '2048', // default max file upload size 2mb

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

    'validate_all_languages' => false,

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
    ],

    'menu_class' => \Laradium\Laradium\Models\Menu::class,
    'menu_item_class' => \Laradium\Laradium\Models\MenuItem::class,

    'disable_menus' => [
        //\Laradium\Laradium\Base\Resources\LanguageResource::class,
        //\Laradium\Laradium\Base\Resources\MenuResource::class,
        //\Laradium\Laradium\Base\Resources\SettingResource::class,
        //\Laradium\Laradium\Base\Resources\TranslationResource::class,
    ],

    'disable_permissions' => false
];