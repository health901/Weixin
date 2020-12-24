<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Apis\ApiRequest;

class UserInfoApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/user/info';

    public function setOpenId($openid)
    {
        $this->data['openid'] = $openid;
    }
}