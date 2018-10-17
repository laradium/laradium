<?php
return [
    // Cache key
    'cache_key'        => 'laradium::settings',

    // Default settings to seed
    'default_settings' => [
        [
            'group'           => 'scripts',
            'name'            => 'Custom scripts between head tags',
            'key'             => 'custom_scripts_between_head',
            'type'            => 'textarea',
            'is_translatable' => 0,
            'has_manager'     => 0,
            'value'           => "<script type=\"text/javascript\">console.log('Between head tags');</script>",
        ],
        [
            'group'           => 'scripts',
            'name'            => 'Custom scripts after opening body tag',
            'key'             => 'custom_scripts_after_body_open',
            'type'            => 'textarea',
            'is_translatable' => 0,
            'has_manager'     => 0,
            'value'           => "<script type=\"text/javascript\">console.log('After opening body tag');</script>",
        ],
        [
            'group'           => 'scripts',
            'name'            => 'Custom scripts before closing body tag',
            'key'             => 'custom_scripts_before_body_closing',
            'type'            => 'textarea',
            'is_translatable' => 0,
            'has_manager'     => 0,
            'value'           => "<script type=\"text/javascript\">console.log('Before closing body tag');</script>",
        ],
        [
            'group' => 'scripts',
            'name'  => 'Custom styles',
            'key'   => 'custom_css',
            'type'  => 'textarea'
        ],
        [
            'group'           => 'seo',
            'name'            => 'Meta title',
            'key'             => 'meta_title',
            'type'            => 'text',
            'is_translatable' => 1,
        ],
        [
            'group'           => 'seo',
            'name'            => 'Meta keywords',
            'key'             => 'meta_keywords',
            'type'            => 'textarea',
            'is_translatable' => 1,
        ],
        [
            'group'           => 'seo',
            'name'            => 'Meta description',
            'key'             => 'meta_description',
            'type'            => 'textarea',
            'is_translatable' => 1,
        ],
        [
            'group'           => 'seo',
            'name'            => 'Meta image',
            'key'             => 'meta_image',
            'type'            => 'text',
            'is_translatable' => 1,
        ],
        // Mail
        [
            'group' => 'mail',
            'name'  => 'Mail server host',
            'key'   => 'mail_host',
            'type'  => 'text'
        ],
        [
            'group' => 'mail',
            'name'  => 'Mail server port',
            'key'   => 'mail_port',
            'type'  => 'text'
        ],
        [
            'group' => 'mail',
            'name'  => 'Mail server username',
            'key'   => 'mail_user',
            'type'  => 'text'
        ],
        [
            'group' => 'mail',
            'name'  => 'Mail server password',
            'key'   => 'mail_password',
            'type'  => 'text'
        ],
        [
            'group' => 'mail',
            'name'  => 'Email address from which to send emails',
            'key'   => 'mail_from_address',
            'type'  => 'text'
        ],
        [
            'group' => 'mail',
            'name'  => 'Name from which to send emails',
            'key'   => 'mail_from_name',
            'type'  => 'text'
        ],
        [
            'group' => 'design',
            'name'  => 'Admin theme color',
            'key'   => 'admin_theme_color',
            'type'  => 'color'
        ],
        [
            'group' => 'design',
            'name'  => 'Admin theme logo',
            'key'   => 'admin_theme_logo',
            'type'  => 'file'
        ],
        [
            'group' => 'design',
            'name'  => 'Admin theme favicon',
            'key'   => 'admin_theme_favicon',
            'type'  => 'file'
        ]

    ],

    'public_settings' => [
        //'design.admin_theme_color'
    ]
];