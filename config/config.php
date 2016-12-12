<?php

return [
    'page' => \Mcms\Pages\Models\Page::class,
    'related' => \Mcms\Pages\Models\Related::class,
    'featured' => \Mcms\Pages\Models\Featured::class,
    'items' => [
        'slug_pattern' => '/page/%slug$s',
        'previewController' => '\FrontEnd\Http\Controllers\HomeController@preview',
        'images' => [
            'keepOriginals' => true,
            'optimize' => true,
            'dirPattern' => 'pages/page_%id$s',
            'recommendedSize' => '500x500',
            'filePattern' => '',
            'types' => [
                [
                    'uploadAs' => 'image',
                    'name' => 'images',
                    'title' => 'Images',
                    'settings' => [
                        'default' => true
                    ]
                ],
                [
                    'name' => 'floor_plans',
                    'title' => 'Floor Plans',
                    'uploadAs' => 'image',
                    'settings' => [
                        'default' => false
                    ]
                ]
            ],
            'copies' => [
                'thumb' => [
                    'width' => 70,
                    'height' => 70,
                    'quality' => 100,
                    'prefix' => 't_',
                    'resizeType' => 'fit',
                    'dir' => 'thumbs/',
                ],
                'big_thumb' => [
                    'width' => 170,
                    'height' => 170,
                    'quality' => 100,
                    'prefix' => 't1_',
                    'resizeType' => 'fit',
                    'dir' => 'big_thumbs/',
                    'useOnAdmin' => true,
                ],
                'main' => [
                    'width' => 500,
                    'height' => 500,
                    'quality' => 100,
                    'prefix' => 'm_',
                    'resizeType' => 'fit',
                    'dir' => '/',
                ],
            ]
        ],
        'files' => [
            'dirPattern' => 'pages/page_%id$s',
            'filePattern' => '',
        ]
    ],
    'categories' => [
        'slug_pattern' => '/pages/%slug$s',
        'images' => [
            'keepOriginals' => true,
            'optimize' => true,
            'dirPattern' => 'pages/category_%id$s',
            'filePattern' => '',
            'types' => [
                [
                    'uploadAs' => 'image',
                    'name' => 'images',
                    'title' => 'Images',
                    'settings' => [
                        'default' => true
                    ]
                ]
            ],
            'copies' => [
                'thumb' => [
                    'width' => 70,
                    'height' => 70,
                    'quality' => 100,
                    'prefix' => 't_',
                    'resizeType' => 'fit',
                    'dir' => 'thumbs/',
                ],
                'big_thumb' => [
                    'width' => 170,
                    'height' => 170,
                    'quality' => 100,
                    'prefix' => 't1_',
                    'resizeType' => 'fit',
                    'dir' => 'big_thumbs/',
                ],
                'main' => [
                    'width' => 500,
                    'height' => 500,
                    'quality' => 100,
                    'prefix' => 'm_',
                    'resizeType' => 'fit',
                    'dir' => '/',
                ],
            ]
        ]
    ]
];