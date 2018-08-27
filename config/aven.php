<?php

return [
    'default_models_directory' => 'App\\Models',

    'resources' => [
        \Netcore\Aven\Aven\Resources\TranslationResource::class,
        \Netcore\Aven\Aven\Resources\LanguageResource::class,
        \Netcore\Aven\Aven\Resources\MenuResource::class,
        \Netcore\Aven\Aven\Resources\SettingResource::class,
        \Netcore\Aven\Content\Aven\Resources\PageResource::class,
        // list of your resources
//        \App\Aven\Resources\ArticleResource::class,

    ],

    'translations_file' => 'translations',

    'user'      => [
        'email'    => 'admin@netcore.lv',
        'password' => 'aven2018'
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

    'dashboard-view' => 'aven::admin.dashboard',

    'fields_list' => [
        'text'              => \Netcore\Aven\Aven\Fields\Text::class,
        'hidden'            => \Netcore\Aven\Aven\Fields\Hidden::class,
        'wysiwyg'           => \Netcore\Aven\Aven\Fields\Wysiwyg::class,
        'boolean'           => \Netcore\Aven\Aven\Fields\Boolean::class,
        'textarea'          => \Netcore\Aven\Aven\Fields\Textarea::class,
        'email'             => \Netcore\Aven\Aven\Fields\Email::class,
        'password'          => \Netcore\Aven\Aven\Fields\Password::class,
        'select'            => \Netcore\Aven\Aven\Fields\Select::class,
        'tab'               => \Netcore\Aven\Aven\Fields\Tab::class,
        'file'              => \Netcore\Aven\Aven\Fields\File::class,
        'date'              => \Netcore\Aven\Aven\Fields\Date::class,
        'time'              => \Netcore\Aven\Aven\Fields\Time::class,
        'datetime'          => \Netcore\Aven\Aven\Fields\DateTime::class,
        'color'             => \Netcore\Aven\Aven\Fields\Color::class,
        // Relations
        'hasOne'            => \Netcore\Aven\Aven\Fields\HasOne::class,
        'hasMany'           => \Netcore\Aven\Aven\Fields\HasMany::class,
        'morphsTo'          => \Netcore\Aven\Aven\Fields\MorphsTo::class,
        'belongsTo'         => \Netcore\Aven\Aven\Fields\BelongsTo::class,
        'belongsToMany'     => \Netcore\Aven\Aven\Fields\BelongsToMany::class,

        // Aven Content
        'widgetConstructor' => \Netcore\Aven\Content\Aven\Fields\WidgetConstructor::class,
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