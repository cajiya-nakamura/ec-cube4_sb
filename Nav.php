<?php

namespace Plugin\SimpleBlog42;

use Eccube\Common\EccubeNav;

class Nav implements EccubeNav
{
    /**
     * @return array
     */
    public static function getNav()
    {
        return [
            'content' => [
                'children' => [
                    'simpleblog42' => [
                        'name' => 'ブログ管理',
                        'children' => [
                            'blog' => [
                                'name' => 'ブログ一覧',
                                'url' => 'admin_content_blog',
                            ],
                            'category' => [
                                'name' => 'カテゴリー',
                                'url' => 'admin_content_blog',
                            ]
                        ],
                    ],
                ],
            ],
        ];
    }
}
