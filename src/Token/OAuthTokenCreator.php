<?php


namespace VRobin\Weixin\Token;


use VRobin\Weixin\Exception\ApiException;
use VRobin\Weixin\Exception\TokenException;
use VRobin\Weixin\Request\WebApis\SnsOauthAaccessTokenRequest;
use VRobin\Weixin\Request\WebApiSender;

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
        $api = new SnsOauthAaccessTokenRequest();
        $api->setAppid($this->appid);
        $api->setSecret($this->secret);
        $api->setCode($this->code);

        $sender = new WebApiSender();
        return $sender->sendRequest($api);
    }
}