<?php


namespace VRobin\Weixin\Apis;

use VRobin\Weixin\Exception\ApiException;
use VRobin\Weixin\Exception\TokenException;
use VRobin\Weixin\Exception\WeixinException;
use VRobin\Weixin\Request\Request;
use VRobin\Weixin\Weixin;

class Api
{
    protected $apiUrl = 'https://api.weixin.qq.com/';
    protected $config;

    protected $tokenType;

    /**
     * Api constructor.
     * @param string $appid
     * @param string $secret
     * @param $config
     * @throws WeixinException
     */
    public function __construct($appid = '', $secret = '')
    {
        $this->appid = $appid;
        $this->secret = $secret;
    }

    protected function getTokenCreator($type = null)
    {
        $type = $type ? $type : $this->tokenType;
        $class = Weixin::$config['token'][$type];
        return new $class($this->appid, $this->secret);
    }


    /**
     * @param ApiRequest $api
     * @return string
     * @throws TokenException
     * @throws ApiException
     */
    public function sendRequest(ApiRequest $api)
    {
        $api->apiUrl = $this->apiUrl;
        if ($api->isNeedToken()) {
            $this->accessToken = $this->getTokenCreator($api->getTokenType())->getToken();
            if (!$this->accessToken) {
                throw new TokenException("Cannot get accessToken");
            }
            $api->setAccessToken($this->accessToken);
        }
        return $this->request($api->getApi(), $api->getData(), $api->getMethod(), $api->returnRaw());
    }

    /**
     * 别名
     * @param ApiRequest $api
     * @return string
     * @throws ApiException
     * @throws TokenException
     */
    public function call(ApiRequest $api)
    {
        return $this->sendRequest($api);
    }

    /**
     *
     * @param string $url
     * @param array $data
     * @param string $method
     * @param bool $raw
     * @return mixed
     * @throws ApiException
     */
    public function request(string $url, $data = array(), $method = 'get', $raw = false)
    {

        if ($method == 'post') {
            $result = $this->post($url, $data);
        } else {
            $result = $this->get($url, $data);
        }
        $data = json_decode($result, true);
        if (isset($data['errcode']) && $data['errcode']) {
            throw new ApiException($data['errcode'] . ':' . $data['errmsg'], $data['errcode']);
        }
        return $raw ? $result : $data;
    }

    protected function get($api, $data)
    {
        return Request::get($api, $data);
    }

    protected function post($api, $data)
    {
        return Request::post($api, $data);
    }
}