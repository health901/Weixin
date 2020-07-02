<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Request\Request;

/**
 * 获取临时素材
 * Class MediaGetRequest
 * @package VRobin\Weixin\Request\Apis
 */
class MediaGetRequest extends Request
{
    protected $api = 'media/get';
    protected $returnRaw = true;

    public function setMediaId($media_id)
    {
        $this->data['media_id'] = $media_id;
    }
}