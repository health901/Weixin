<?php

namespace VRobin\Weixin;

use VRobin\Weixin\Apis\MiniApi;
use VRobin\Weixin\Apis\MpApi;
use VRobin\Weixin\Apis\ThirdApi;
use VRobin\Weixin\Apis\WebApi;
use VRobin\Weixin\Message\Responser;

/**
 * 微信公众接口SDK
 *
 * @author Viking Robin <admin@vkrobin.com>
 *
 */
class Weixin
{

    public static $config;

    public static function config($config = null)
    {
        if (!$config) {
            $config = require(__DIR__ . '/config.php');
        }
        self::$config = $config;
        return new self();
    }

    public static function __callStatic($name, $arguments)
    {
        return (new self())->$name($arguments);
    }

    /**
     * 返回公众号回复响应器
     * @param $token
     * @param string $aesKey 消息加密秘钥
     * @return Responser
     */
    public static function responser($token, $aesKey = '')
    {
        return new Responser($token, $aesKey);
    }

    /**
     * 返回公众号接口
     * @param string $appid
     * @param string $secret
     * @return MpApi
     * @throws Exception\WeixinException
     */
    public function mp($appid = '', $secret = '')
    {
        return new MpApi($appid, $secret);
    }

    /**
     * 返回网页接口
     * @param string $appid
     * @param string $secret
     * @return WebApi
     * @throws Exception\WeixinException
     */
    public function web(string $appid = '', string $secret = '')
    {
        return new WebApi($appid, $secret);
    }

    /**
     * @param string $appid
     * @param string $secret
     * @return MiniApi
     * @throws Exception\WeixinException
     */
    public function mini($appid = '', $secret = '')
    {
        return new MiniApi($appid, $secret);
    }

    /**
     * @param string $appid
     * @param string $secret
     * @return ThirdApi
     * @throws Exception\WeixinException
     */
    public function third($appid = '', $secret = '')
    {
        return new ThirdApi($appid, $secret);
    }

    /**
     * js 签名生成
     * @param string $appid
     * @param string $secret
     * @return WeixinJsSignaturePack
     * @throws Exception\WeixinException
     */
    public static function jsSignaturePack($appid = '', $secret = '')
    {
        return new WeixinJsSignaturePack($appid, $secret);
    }
}