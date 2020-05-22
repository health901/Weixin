<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Request\Request;

/**
 * 获取临时素材
 * Class MediaGetJssdkRequest
 * @package VRobin\Weixin\Request\Apis
 */
class MediaGetJssdkRequest extends Request
{
    protected $api = 'media/get/jssdk';


    public function setMediaId($media_id)
    {
        $this->data['media_id'] = $media_id;
    }
}