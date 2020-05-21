<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Request\Request;

/**
 * 上传图文消息内的图片获取URL
 * Class MediaUploadimgRequest
 * @package VRobin\Weixin\Request\Apis
 */
class MediaUploadimgRequest extends Request
{
    protected $api = 'media/uploadimg';

    public function setMediaFile($file)
    {
        $this->data['@media'] = $file;
    }
}