<?php


namespace VRobin\Weixin\Request;


use VRobin\Weixin\Exception\{ApiException, TokenException, WeixinException};
use VRobin\Weixin\Request\Request as Api;
use VRobin\Weixin\Token\TokenCreator;

class ApiSender
{
    use ApiTrait;

    protected $apiUrl = "https://api.weixin.qq.com/cgi-bin/";
    protected $accessToken;
    /**
     * @var TokenCreator
     */
    protected $tokenCreator;

    public function __construct($tokenCreator = null)
    {
        if ($tokenCreator && $tokenCreator instanceof TokenCreator) {
            $this->tokenCreator = $tokenCreator;
        }
    }

    /**
     * @param Request $api
     * @return string
     * @throws TokenException
     * @throws WeixinException
     * @throws ApiException
     */
    public function sendRequest(Api $api)
    {
        if ($api->isNeedToken()) {
            $this->accessToken = $this->tokenCreator->getToken();
            if (!$this->accessToken) {
                throw new TokenException("Cannot get accessToken");
            }
        }
        return $this->request($api->getApi(), $api->getData(), $api->getMethod());
    }
}