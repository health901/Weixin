<?php


namespace VRobin\Weixin\Request\WebApis;


use VRobin\Weixin\Request\Request;

class SnsUserInfoRequest extends Request
{
    protected $api = 'sns/userinfo';

    public function setOpenId($openid)
    {
        $this->data['openid'] = $openid;
    }

    public function setLanguage($language)
    {
        $this->data['lang'] = $language;
    }
}