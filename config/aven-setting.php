<?php
return [
    // Cache key
    'cache_key'        => 'settings',
    // Upload path
    'upload_path'      => '/uploads/settings',
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
            'group' => 'seo',
            'name'  => 'Meta keywords',
            'key'   => 'meta_keywords',
            'type'  => 'textarea',
        ],
        [
            'group' => 'seo',
            'name'  => 'Meta description',
            'key'   => 'meta_description',
            'type'  => 'textarea',
        ],
        // Facebook OG tags
        [
            'group' => 'seo',
            'name'  => 'OG title',
            'key'   => 'og_title',
            'type'  => 'text',
        ],
        [
            'group' => 'seo',
            'name'  => 'OG type',
            'key'   => 'og_type',
            'type'  => 'text',
        ],
        [
            'group' => 'seo',
            'name'  => 'OG URL',
            'key'   => 'og_url',
            'type'  => 'text',
        ],
        [
            'group' => 'seo',
            'name'  => 'OG description',
            'key'   => 'og_description',
            'type'  => 'textarea'
        ],
        [
            'group' => 'seo',
            'name'  => 'OG image',
            'key'   => 'og_image',
            'type'  => 'text'
        ],
        // Twitter cards
        [
            'group' => 'seo',
            'name'  => 'Twitter Card title',
            'key'   => 'twitter_title',
            'type'  => 'text',
        ],
        [
            'group' => 'seo',
            'name'  => 'Twitter Card site',
            'key'   => 'twitter_site',
            'type'  => 'text',
        ],
        [
            'group' => 'seo',
            'name'  => 'Twitter Card type',
            'key'   => 'twitter_card',
            'type'  => 'text',
        ],
        [
            'group' => 'seo',
            'name'  => 'Twitter Card description',
            'key'   => 'twitter_description',
            'type'  => 'textarea'
        ],
        [
            'group' => 'seo',
            'name'  => 'Twitter Card image',
            'key'   => 'twitter_image',
            'type'  => 'text'
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
        ]
    ]
];