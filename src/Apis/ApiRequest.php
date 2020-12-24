<?php


namespace VRobin\Weixin\Apis;


class ApiRequest
{
    public $apiUrl;

    protected $api;

    protected $data = [];

    protected $queryData = [];

    protected $method = 'GET';

    protected $needToken = true;

    protected $accessToken;

    protected $tokenType = null;

    protected $postJson = true;

    protected $returnRaw = false;

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    protected function postJson()
    {
        return $this->method == 'GET' ? false : ($this->data ? $this->postJson : false);
    }

    public function getData()
    {
        return $this->postJson() ? json_encode($this->data, JSON_UNESCAPED_UNICODE) : $this->data;
    }

    public function isNeedToken()
    {
        return $this->needToken;
    }

    public function getTokenType()
    {
        return $this->tokenType;
    }

    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }

    public function returnRaw()
    {
        return $this->returnRaw;
    }

    public function getApi()
    {
        return $this->apiUrl();
    }

    public function getMethod()
    {
        return strtolower($this->method);
    }

    protected function apiUrl()
    {
        $url = $this->apiUrl . $this->api;
        if ($this->accessToken) {
            $this->queryData['access_token'] = $this->accessToken;
        }
        if ($this->queryData) {
            $url .= '?' . http_build_query($this->queryData);
        }
        return $url;
    }
}