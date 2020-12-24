<?php


namespace VRobin\Weixin\Token;


use VRobin\Weixin\Apis\Api;
use VRobin\Weixin\Cache\Cache;
use VRobin\Weixin\Exception\{ApiException, TokenException, WeixinException};
use VRobin\Weixin\Apis\Mp\AccessTokenApiRequest;

/**
 * 服务器端接口请求Token
 * Class AccessTokenCreator
 * @package VRobin\Weixin\Token
 */
class AccessTokenCreator implements TokenInterface
{
    protected $appid;
    protected $secret;

    public function __construct(string $appid, string $secret)
    {
        $this->appid = $appid;
        $this->secret = $secret;
    }

    /**
     * @return mixed
     * @throws ApiException
     * @throws TokenException
     * @throws WeixinException
     */
    public function getToken(): string
    {
        $tokenKey = 'acccessToken_' . $this->appid;
        $cache = Cache::get($tokenKey, '');
        if ($cache && $cache['appid'] == $this->appid) {
            return $cache['acccessToken'];
        }
        $data = $this->request();
        $ttl = $data['expires_in'] - 200;
        $accessToken = array('acccessToken' => $data['access_token'], 'appid' => $this->appid);
        Cache::set($tokenKey, $accessToken, $ttl);
        return $data['access_token'];
    }

    /**
     * @return string
     * @throws ApiException
     * @throws TokenException
     */
    protected function request()
    {
        $api = new AccessTokenApiRequest();
        $api->setAppid($this->appid);
        $api->setSecret($this->secret);

        $sender = new Api();
        return $sender->sendRequest($api);
    }

}