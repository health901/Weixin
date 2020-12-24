<?php


namespace VRobin\Weixin\Apis\Web;


use VRobin\Weixin\Apis\ApiRequest;

class SnsOauthAccessTokenApiRequest extends ApiRequest
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