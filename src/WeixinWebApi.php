<?php


namespace VRobin\Weixin;


use VRobin\Weixin\Request\Request;
use VRobin\Weixin\Request\WebApiSender;
use VRobin\Weixin\Token\OAuthTokenCreator;

class WeixinWebApi
{
    protected $appid;
    protected $secret;
    /**
     * @var OAuthTokenCreator
     */
    protected $tokenCreator;
    protected $oauthAccessToken;

    /**
     * WeixinWebApi constructor.
     * @param string $appid
     * @param string $secret
     * @param null $tokenCreator
     */
    public function __construct($appid = '', $secret = '', $tokenCreator = null)
    {
        $this->appid = $appid;
        $this->secret = $secret;
        $this->makeService($tokenCreator);
    }

    /**
     * @param null $tokenCreator
     */
    protected function makeService($tokenCreator = null)
    {

        $this->tokenCreator = $tokenCreator ? $tokenCreator : new OAuthTokenCreator($this->appid, $this->secret);
    }

    /**
     * @param Request $api
     * @return string
     * @throws Exception\TokenException|Exception\ApiException
     */
    public function call(Request $api)
    {
        $sender = new WebApiSender($this->oauthAccessToken);
        return $sender->sendRequest($api);
    }


    /**
     * @param $code
     * @return mixed|string
     * @throws Exception\ApiException|Exception\TokenException
     */
    public function getToken($code)
    {
        $this->tokenCreator->setCode($code);
        return $this->tokenCreator->getToken();
    }

    /**
     * @return OAuthTokenCreator
     */
    public function tokenCreator()
    {
        return $this->tokenCreator;
    }

    public function authorizeUrl($redirect_url, $withUserInfo = false)
    {
        $redirect_url = urlencode($redirect_url);
        $scope = $withUserInfo ? 'snsapi_base' : 'snsapi_userinfo';
        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->appid}&redirect_uri={$redirect_url}&response_type=code&scope={$scope}&state=STATE#wechat_redirect";
    }
}