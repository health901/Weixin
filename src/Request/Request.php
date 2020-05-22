<?php


namespace VRobin\Weixin\Request;


class Request
{
    protected $api;

    protected $data = [];

    protected $method = 'GET';

    protected $needToken = true;

    protected $postJson = false;

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function getData()
    {
        return $this->postJson ? json_encode($this->data) : $this->data;
    }

    public function isNeedToken()
    {
        return $this->needToken;
    }

    public function getApi()
    {
        return $this->api;
    }

    public function getMethod()
    {
        return strtolower($this->method);
    }
}