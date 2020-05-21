<?php


namespace VRobin\Weixin\Request\WebApis;


use VRobin\Weixin\Request\Request;

class SnsOauthAaccessTokenRequest extends Request
{
    protected $api = 'sns/oauth2/access_token';
    protected $needToken = false;

    public function __construct()
    {
        $this->data['grant_type'] = 'authorization_code';
    }

    public function setAppid($appid)
    {
        $this->data['appid'] = $appid;
    }

    public function setSecret($secret)
    {
        $this->data['secret'] = $secret;
    }

    public function setCode($code)
    {
        $this->data['code'] = $code;
    }
}