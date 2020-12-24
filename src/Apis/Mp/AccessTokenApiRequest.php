<?php


namespace VRobin\Weixin\Apis\Mp;

use VRobin\Weixin\Apis\ApiRequest;

class AccessTokenApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/token';

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