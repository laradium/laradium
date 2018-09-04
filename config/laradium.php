<?php

return [
    'default_models_directory' => 'App\\Models',

    'file_size' => '2024', // default max file upload size 2mb

    'resources' => [
        \Laradium\Laradium\Base\Resources\TranslationResource::class,
        \Laradium\Laradium\Base\Resources\LanguageResource::class,
        \Laradium\Laradium\Base\Resources\MenuResource::class,
        \Laradium\Laradium\Base\Resources\SettingResource::class,
        \Laradium\Laradium\Content\Laradium\Resources\PageResource::class,
        // List of your resources
        // \App\Laradium\Resources\ArticleResource::class,

    ],

    'api_resources' => [
        // List of API resources
    ],

    'translations_file' => 'translations',

    'user'      => [
        'email'    => 'admin@netcore.lv',
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

    'fields_list' => [
        'text'              => \Laradium\Laradium\Base\Fields\Text::class,
        'hidden'            => \Laradium\Laradium\Base\Fields\Hidden::class,
        'wysiwyg'           => \Laradium\Laradium\Base\Fields\Wysiwyg::class,
        'boolean'           => \Laradium\Laradium\Base\Fields\Boolean::class,
        'textarea'          => \Laradium\Laradium\Base\Fields\Textarea::class,
        'email'             => \Laradium\Laradium\Base\Fields\Email::class,
        'password'          => \Laradium\Laradium\Base\Fields\Password::class,
        'select'            => \Laradium\Laradium\Base\Fields\Select::class,
        'tab'               => \Laradium\Laradium\Base\Fields\Tab::class,
        'file'              => \Laradium\Laradium\Base\Fields\File::class,
        'date'              => \Laradium\Laradium\Base\Fields\Date::class,
        'time'              => \Laradium\Laradium\Base\Fields\Time::class,
        'datetime'          => \Laradium\Laradium\Base\Fields\DateTime::class,
        'color'             => \Laradium\Laradium\Base\Fields\Color::class,
        // Relations
        'hasOne'            => \Laradium\Laradium\Base\Fields\HasOne::class,
        'hasMany'           => \Laradium\Laradium\Base\Fields\HasMany::class,
        'morphsTo'          => \Laradium\Laradium\Base\Fields\MorphsTo::class,
        'belongsTo'         => \Laradium\Laradium\Base\Fields\BelongsTo::class,
        'belongsToMany'     => \Laradium\Laradium\Base\Fields\BelongsToMany::class,

        // Laradium Content
        'widgetConstructor' => \Laradium\Laradium\Content\Laradium\Fields\WidgetConstructor::class,
    ],

    'menus' => [
        'Admin menu' => [
            [
                'is_active'    => 1,
                'translations' => [
                    'name' => 'Translations',
                    'url'  => '/admin/translations',
                ]
            ],
            [
                'is_active'    => 1,
                'translations' => [
                    'name' => 'Languages',
                    'url'  => '/admin/languages',
                ]
            ],
            [
                'is_active'    => 1,
                'translations' => [
                    'name' => 'Menus',
                    'url'  => '/admin/menus',
                ]
            ],

        ]
    ]
];