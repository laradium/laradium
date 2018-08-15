<?php

return [
    'fields_list'    => [
        'text'     => \Netcore\Aven\Aven\Fields\Text::class,
        'hidden'   => \Netcore\Aven\Aven\Fields\Hidden::class,
        'wysiwyg'  => \Netcore\Aven\Aven\Fields\Wysiwyg::class,
        'boolean'  => \Netcore\Aven\Aven\Fields\Boolean::class,
        'textarea' => \Netcore\Aven\Aven\Fields\Textarea::class,
        'select'   => \Netcore\Aven\Aven\Fields\Select::class,
        'hasMany'  => \Netcore\Aven\Aven\Fields\HasMany::class,
    ],
    'resources'      => [
        \Netcore\Aven\Aven\Resources\TranslationResource::class,
        \Netcore\Aven\Aven\Resources\LanguageResource::class,
        // list of your resources
    ],

    // Default admin account credentials
    'user'           => [
        'email'    => 'admin@netcore.lv',
        'password' => 'aven2018'
    ],

    // Default dashboard view
    'dashboard-view' => 'aven::admin.dashboard',

    // Default language list
    'languages'      => [
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
    ]
];