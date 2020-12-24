<?php


namespace VRobin\Weixin\Apis\Web;


use VRobin\Weixin\Apis\ApiRequest;

class SnsUserInfoApiRequest extends ApiRequest
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