<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Apis\ApiRequest;

/**
 * 上传图文消息内的图片获取URL
 * Class MediaUploadimgApiRequest
 * @package VRobin\Weixin\ApiRequest\Apis
 */
class MediaUploadimgApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/media/uploadimg';

    protected $method = 'POST';

    protected $postJson = false;

    public function setMediaFile($file)
    {
        $this->data['@media'] = $file;
    }
}