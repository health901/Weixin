<?php
//可替换类映射及配置
use VRobin\Weixin\Cache\File;
use VRobin\Weixin\Token\AccessTokenCreator;
use VRobin\Weixin\Token\OAuthTokenCreator;

return [
    'token' => [
        'access_token' => AccessTokenCreator::class,
        'oauth_token' => OAuthTokenCreator::class
    ],
    'cache' => [
        'class' => File::class,
        'config' => [
            'cacheDir' => __DIR__,
            'cacheFile' => 'weixin.cache'
        ]
    ]
];