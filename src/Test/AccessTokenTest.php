<?php


namespace VRobin\Weixin\Test;


use PHPUnit\Framework\TestCase;
use VRobin\Weixin\Cache\Cache;
use VRobin\Weixin\Token\AccessTokenCreator;

require_once(__DIR__ . '/test_config.php');

class AccessTokenTest extends TestCase
{
    public function testToken()
    {
        $config = require(__DIR__ . '/../config.php');
        Cache::setStore($config['cache']['class'], $config['cache']['config']);
        Cache::clear();
        $creator = new AccessTokenCreator(APP_ID, APP_SECRET);
        $token = $creator->getToken();
        $this->assertIsString($token, 'token 成功获取');
        $token = $creator->getToken();
        $this->assertIsString($token, 'token 缓存成功获取');
    }
}