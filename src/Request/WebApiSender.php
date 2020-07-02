<?php


namespace VRobin\Weixin\Request;

use VRobin\Weixin\Exception\ApiException;
use VRobin\Weixin\Exception\TokenException;
use VRobin\Weixin\Request\Request as Api;

/**
 * 微信网页开发Api
 * Class WebApi
 * @package VRobin\Weixin\Request
 */
class WebApiSender
{
    use ApiTrait;

    protected $apiUrl = 'https://api.weixin.qq.com/';

    protected $accessToken = '';

    public function __construct($token = null)
    {
        $token && $this->accessToken = $token;
    }

    /**
     * @param Request $api
     * @return string
     * @throws TokenException
     * @throws ApiException
     */
    public function sendRequest(Api $api)
    {
        if ($api->isNeedToken() && !$this->accessToken) {
            throw new TokenException("Cannot get accessToken");
        }
        return $this->request($api->getApi(), $api->getData(), $api->getMethod(), $api->returnRaw());
    }
}