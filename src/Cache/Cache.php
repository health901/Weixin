<?php


namespace VRobin\Weixin\Cache;


use VRobin\Weixin\Exception\WeixinException;

class Cache
{
    protected static $config;
    protected static $instance;

    /**
     * @param $key
     * @param $value
     * @param $ttl int 单位秒
     * @return mixed
     * @throws WeixinException
     */
    public static function set($key, $value, int $ttl)
    {
        if (!self::$instance) {
            self::getInstance();
        }
        return self::$instance->set($key, $value, $ttl);
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     * @throws WeixinException
     */
    public static function get($key, $value = "")
    {
        if (!self::$instance) {
            self::getInstance();
        }
        return self::$instance->get($key, $value);
    }

    public static function clear(){
        if (!self::$instance) {
            self::getInstance();
        }
        return self::$instance->clear();
    }

    /**
     * @return mixed
     * @throws WeixinException
     */
    protected static function getInstance()
    {
        $store = self::$config['store'] ?? '';
        if (!$store) {
            throw new WeixinException("store is not set");
        }

        $storeInstance = new $store;
        $storeInstance->config(self::$config['config']);
        self::$instance = $storeInstance;
        return $storeInstance;
    }

    /**
     * @param $store
     * @param array $config
     * @throws WeixinException
     */
    public static function setStore($store, $config = [])
    {
        self::$config = [
            'store' => $store,
            'config' => $config
        ];
        self::getInstance();
    }
}