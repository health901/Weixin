<?php


namespace VRobin\Weixin\Apis\Web;


use VRobin\Weixin\Apis\ApiRequest;

/**
 * 检验授权凭证（access_token）是否有效
 * Class SnsAuthApiRequest
 * @package VRobin\Weixin\ApiRequest\WebApis
 */
class SnsAuthApiRequest extends ApiRequest
{
    protected $api = 'sns/auth';

    public function setOpenId($openid)
    {
        $this->data['openid'] = $openid;
    }

}