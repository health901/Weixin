<?php


namespace VRobin\Weixin\Token;


use VRobin\Weixin\Cache\Cache;
use VRobin\Weixin\Exception\{ApiException, TokenException, WeixinException};
use VRobin\Weixin\Request\Apis\AccessTokenRequest;
use VRobin\Weixin\Request\ApiSender;

class TokenCreator implements TokenInterface
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
    public function getToken()
    {
        $cache = Cache::get('acccessToken', '');
        if ($cache && $cache['appid'] == $this->appid && $cache['expire'] > time()) {
            return $cache['acccessToken'];
        }
        $data = $this->request();
        $expire = time() + $data['expires_in'] - 200;
        $accessToken = array('acccessToken' => $data['access_token'], 'expire' => $expire, 'appid' => $this->appid);
        Cache::set('acccessToken', $accessToken);
        return $data['access_token'];
    }

    /**
     * @return string
     * @throws ApiException
     * @throws TokenException
     * @throws WeixinException
     */
    protected function request()
    {
        $api = new AccessTokenRequest();
        $api->setAppid($this->appid);
        $api->setSecret($this->secret);

        $sender = new ApiSender();
        return $sender->sendRequest($api);
    }

}