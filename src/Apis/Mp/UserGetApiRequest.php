<?php


namespace VRobin\Weixin\Apis\Mp;

use VRobin\Weixin\Apis\ApiRequest;

/**
 * 获取关注者列表
 * 一次10000条，超过10000条需要传入nextOpenId作为起始ID进行多次抓取
 */
class UserGetApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/user/get';

    public function setNextOpenId($openid)
    {
        $this->data['next_openid'] = $openid;
    }
}