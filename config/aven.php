<?php

return [
    'fields_list' => [
        'text'     => \Netcore\Aven\Aven\Fields\Text::class,
        'hidden'   => \Netcore\Aven\Aven\Fields\Hidden::class,
        'wysiwyg'  => \Netcore\Aven\Aven\Fields\Wysiwyg::class,
        'boolean'  => \Netcore\Aven\Aven\Fields\Boolean::class,
        'textarea' => \Netcore\Aven\Aven\Fields\Textarea::class,
        'select'   => \Netcore\Aven\Aven\Fields\Select::class,
        'hasMany'  => \Netcore\Aven\Aven\Fields\HasMany::class,
    ],
    'resources'   => [
        // list of your resources
    ]
];