<?php


namespace VRobin\Weixin\Apis\Web;


use VRobin\Weixin\Apis\ApiRequest;

class SnsOauthRefreshTokenApiRequest extends ApiRequest
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