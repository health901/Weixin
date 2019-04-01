<?php

namespace VRobin\Weixin;

/**
 * 微信公众接口SDK
 *
 * @author Viking Robin <admin@vkrobin.com>
 */

/**
 * @property string $cacheType 缓存类型
 * @property string $cacheDir 缓存目录
 * @property boolean $develop 开发者模式
 */
Class Weixin
{

    protected $config = array(
        'token' => "",
        'appid' => '',
        'secret' => "",
        'compatibleJS' => false,
        "cacheType" => "File",
        "cacheDir" => null,
        "cacheFile" => null,
        "develop" => false
    );

    /**
     * @var WeixinResponser
     */
    protected $response;

    /**
     * @var WeixinServiceApi
     */
    protected $service;

    public function __set($name, $value)
    {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        } elseif (method_exists($this, 'set' . ucfirst($name))) {
            $method = 'set' . ucfirst($name);
            $this->$method($value);
        }
    }

    public function __get($name)
    {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        } elseif (method_exists($this, 'get' . ucfirst($name))) {
            $method = 'get' . ucfirst($name);
            return $this->$method();
        }
    }


    /**
     * @return WeixinResponser
     */
    public function getResponse()
    {
        if (!$this->response) {
            $this->response = self::response($this->config['token']);
        }
        return $this->response;
    }

    /**
     * @return WeixinServiceApi
     */
    public function getService()
    {
        if (!$this->service) {
            $this->service = self::service($this->config['appid'], $this->config['secret']);
            $this->service->config($this->config);
        }
        return $this->service;
    }

    /**
     * 返回回复响应器
     * @param $token
     * @return WeixinResponser
     */
    public static function response($token)
    {
        return new WeixinResponser($token);
    }

    /**
     * 返回服务接口
     * @param null $appid
     * @param null $secret
     * @return WeixinServiceApi
     */
    public static function service($appid = null, $secret = null)
    {
        return new WeixinServiceApi($appid, $secret);
    }


}