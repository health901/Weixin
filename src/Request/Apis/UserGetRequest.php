<?php


namespace VRobin\Weixin\Request\Apis;

use VRobin\Weixin\Request\Request;

/**
 * 获取关注者列表
 * 一次10000条，超过10000条需要传入nextOpenId作为起始ID进行多次抓取
 */
class UserGetRequest extends Request
{
    protected $api = 'user/get';

    public function setNextOpenId($openid)
    {
        $this->data['next_openid'] = $openid;
    }
}