<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Request\Request;

class AccessTokenRequest extends Request
{
    protected $api = 'token';

    protected $needToken = false;

    public function __construct()
    {
        $this->data['grant_type'] = 'client_credential';
    }

    public function setAppid($appid)
    {
        $this->data['appid'] = $appid;
    }

    public function setSecret($secret)
    {
        $this->data['secret'] = $secret;
    }
}