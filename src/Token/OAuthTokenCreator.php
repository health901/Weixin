<?php


namespace VRobin\Weixin\Token;


use VRobin\Weixin\Apis\Api;
use VRobin\Weixin\Exception\ApiException;
use VRobin\Weixin\Exception\TokenException;
use VRobin\Weixin\Apis\Web\SnsOauthAccessTokenApiRequest;


/**
 * 微信用户授权Token
 * Class OAuthTokenCreator
 * @package VRobin\Weixin\Token
 */
class OAuthTokenCreator implements TokenInterface
{
    protected $appid;
    protected $secret;
    protected $code;
    /**
     * @var string|void
     */
    protected $result;

    public function __construct(string $appid, string $secret)
    {
        $this->appid = $appid;
        $this->secret = $secret;
    }

    public function __get($key)
    {
        return $this->result[$key] ?? null;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     * @throws ApiException
     * @throws TokenException
     */
    public function getToken()
    {
        $result = $this->request();
        $this->result = $result;
        return $result;
    }

    /**
     * @return string
     * @throws ApiException
     * @throws TokenException
     */
    protected function request()
    {
        $api = new SnsOauthAccessTokenApiRequest();
        $api->setAppid($this->appid);
        $api->setSecret($this->secret);
        $api->setCode($this->code);

        $sender = new Api();
        return $sender->sendRequest($api);
    }
}