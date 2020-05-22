<?php


namespace VRobin\Weixin;


use VRobin\Weixin\Cache\Cache;
use VRobin\Weixin\Cache\File;
use VRobin\Weixin\Request\ApiSender;
use VRobin\Weixin\Request\Request;
use VRobin\Weixin\Token\TokenCreator;

class WeixinApi
{
    protected $appid;
    protected $secret;
    protected $tokenCreator;

    /**
     * WeixinApi constructor.
     * @param string $appid
     * @param string $secret
     * @param null $tokenCreator
     * @param null $cacheConfig
     * @throws Exception\WeixinException
     */
    public function __construct($appid = '', $secret = '', $tokenCreator = null, $cacheConfig = null)
    {
        $this->appid = $appid;
        $this->secret = $secret;
        $this->makeService($tokenCreator, $cacheConfig);
    }

    /**
     * @param null $tokenCreator
     * @param null $cacheConfig
     * @throws Exception\WeixinException
     */
    protected function makeService($tokenCreator = null, $cacheConfig = null)
    {

        $this->tokenCreator = $tokenCreator ? $tokenCreator : new TokenCreator($this->appid, $this->secret);

        if ($cacheConfig) {
            Cache::getStore($cacheConfig['store'], $cacheConfig['config']);
        } else {
            Cache::getStore(File::class, [
                'cacheDir' => __DIR__,
                'cacheFile' => 'weixin.cache'
            ]);
        }
    }

    /**
     * @param Request $api
     * @return string
     * @throws Exception\WeixinException|Exception\ApiException|Exception\TokenException
     */
    public function call(Request $api)
    {
        $sender = new ApiSender($this->tokenCreator);
        return $sender->sendRequest($api);
    }
}