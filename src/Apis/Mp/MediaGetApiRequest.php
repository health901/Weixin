<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Apis\ApiRequest;

/**
 * 获取临时素材
 * Class MediaGetApiRequest
 * @package VRobin\Weixin\ApiRequest\Apis
 */
class MediaGetApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/media/get';
    protected $returnRaw = true;

    public function setMediaId($media_id)
    {
        $this->data['media_id'] = $media_id;
    }
}