<?php
// +----------------------------------------------------------------------
// | ThinkPHP JwtClient [Simple JsonWebToken Client For ThinkPHP]
// +----------------------------------------------------------------------
// | ThinkPHP JwtClient
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: axguowen <axguowen@qq.com>
// +----------------------------------------------------------------------

return [
    // 默认平台
    'default' => 'default',
    // 平台配置
    'platforms' => [
        // 默认
        'default' => [
            // 签名密钥
            'sign_key' => 'default_sign_key',
            // 有效时间, 单位秒
            'expiration_time' => 900,
            // 指定时间后生效, 单位秒
            'not_before' => 0,
            // 颁发者
            'issuer' => 'https://example.com',
            // 接收者
            'audience' => 'https://example.com',
            // 识别码
            'id' => '',
            // 关联对象
            'subject' => '',
        ],
        // 其它
        'other' => [
            // 签名密钥
            'sign_key' => 'other_sign_key',
            // 有效时间, 单位秒
            'expiration_time' => 900,
            // 指定时间后生效, 单位秒
            'not_before' => 0,
            // 颁发者
            'issuer' => 'https://other.com',
            // 接收者
            'audience' => 'https://other.com',
            // 识别码
            'id' => '',
            // 关联对象
            'subject' => '',
        ],
    ]
];
