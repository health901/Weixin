<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Request\Request;

class UserInfoRequest extends Request
{
    protected $api = 'user/info';

    public function setOpenId($openid)
    {
        $this->data['openid'] = $openid;
    }
}