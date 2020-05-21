<?php


namespace VRobin\Weixin\Request\WebApis;


use VRobin\Weixin\Request\Request;

class SnsOauthRefreshTokenRequest extends Request
{
    protected $api = 'sns/oauth2/refresh_token';
    protected $needToken = false;

    public function __construct()
    {
        $this->data['grant_type'] = 'refresh_token';
    }

    public function setAppid($appid)
    {
        $this->data['appid'] = $appid;
    }

    public function setRefreshToken($token)
    {
        $this->data['refresh_token'] = $token;
    }
}