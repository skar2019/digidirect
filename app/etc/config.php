<?php
return [
    'scopes' => [
        'websites' => [
            'admin' => [
                'website_id' => '0',
                'code' => 'admin',
                'name' => 'Admin',
                'sort_order' => '0',
                'default_group_id' => '0',
                'is_default' => '0'
            ],
            'digi_website_au' => [
                'website_id' => '1',
                'code' => 'digi_website_au',
                'name' => 'digiDirect Au',
                'sort_order' => '0',
                'default_group_id' => '1',
                'is_default' => '1'
            ]
        ],
        'groups' => [
            [
                'group_id' => '0',
                'website_id' => '0',
                'name' => 'Default',
                'root_category_id' => '0',
                'default_store_id' => '0',
                'code' => 'default'
            ],
            [
                'group_id' => '1',
                'website_id' => '1',
                'name' => 'digiDirect AU',
                'root_category_id' => '2',
                'default_store_id' => '1',
                'code' => 'digi_store_au'
            ]
        ],
        'stores' => [
            'admin' => [
                'store_id' => '0',
                'code' => 'admin',
                'website_id' => '0',
                'group_id' => '0',
                'name' => 'Admin',
                'sort_order' => '0',
                'is_active' => '1'
            ],
            'digi_store_view_au' => [
                'store_id' => '1',
                'code' => 'digi_store_view_au',
                'website_id' => '1',
                'group_id' => '1',
                'name' => 'digiDirect AU',
                'sort_order' => '0',
                'is_active' => '1'
            ]
        ]
    ],
    'system' => [
        'default' => [
            'general' => [
                'locale' => [
                    'code' => 'en_AU'
                ]
            ],
            'dev' => [
                'static' => [
                    'sign' => '1'
                ],
                'front_end_development_workflow' => [
                    'type' => 'server_side_compilation'
                ],
                'template' => [
                    'minify_html' => '1',
                    'allow_symlink' => '0'
                ],
                'js' => [
                    'merge_files' => '1',
                    'minify_files' => '1',
                    'minify_exclude' => [
                        'tiny_mce' => '/tiny_mce/',
                        'authorizenet_acceptjs' => '\\.authorize\\.net/v1/Accept'
                    ],
                    'session_storage_logging' => '0',
                    'translate_strategy' => 'dictionary',
                    'enable_js_bundling' => '0'
                ],
                'css' => [
                    'minify_files' => '1',
                    'minify_exclude' => [
                        'tiny_mce' => '/tiny_mce/'
                    ],
                    'merge_css_files' => '1'
                ]
            ]
        ],
        'stores' => [

        ],
        'websites' => [

        ]
    ],
    'admin_user' => [
        'locale' => [
            'code' => [
                'en_AU'
            ]
        ]
    ]
];
