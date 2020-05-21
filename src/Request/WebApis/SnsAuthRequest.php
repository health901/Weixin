<?php


namespace VRobin\Weixin\Request\WebApis;


use VRobin\Weixin\Request\Request;

class SnsAuthRequest extends Request
{
    protected $api = 'sns/auth';

    public function setOpenId($openid)
    {
        $this->data['openid'] = $openid;
    }

}