<?php

namespace VRobin\Weixin;

use VRobin\Weixin\Message\Responser;

/**
 * 微信公众接口SDK
 *
 * @author Viking Robin <admin@vkrobin.com>
 */
class Weixin
{

    /**
     * 返回公众号回复响应器
     * @param $token
     * @return Responser
     */
    public static function responser($token)
    {
        return new Responser($token);
    }

    /**
     * 返回公众号接口
     * @param string $appid
     * @param string $secret
     * @param null $tokenCreator
     * @param null $cacheConfig
     * @return WeixinApi
     * @throws Exception\WeixinException
     */
    public static function api($appid = '', $secret = '', $tokenCreator = null, $cacheConfig = null)
    {
        return new WeixinApi($appid, $secret, $tokenCreator, $cacheConfig);
    }

    /**
     * 返回网页接口
     * @param string $appid
     * @param string $secret
     * @param null $tokenCreator
     * @return WeixinWebApi
     */
    public static function webApi($appid = '', $secret = '', $tokenCreator = null)
    {
        return new WeixinWebApi($appid, $secret, $tokenCreator);
    }

    /**
     * js 签名生成
     * @param string $appid
     * @param string $secret
     * @param null $tokenCreator
     * @param null $cacheConfig
     * @return WeixinJsSignaturePack
     * @throws Exception\WeixinException
     */
    public static function jsSignaturePack($appid = '', $secret = '', $tokenCreator = null, $cacheConfig = null){
        return new WeixinJsSignaturePack($appid, $secret, $tokenCreator, $cacheConfig);
    }
}