<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Apis\ApiRequest;

/**
 * 获取临时素材
 * Class MediaGetJssdkApiRequest
 * @package VRobin\Weixin\ApiRequest\Apis
 */
class MediaGetJssdkApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/media/get/jssdk';


    public function setMediaId($media_id)
    {
        $this->data['media_id'] = $media_id;
    }
}