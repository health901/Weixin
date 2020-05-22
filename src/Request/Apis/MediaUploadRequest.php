<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Request\Request;

/**
 * 上传临时素材
 * Class MediaUploadRequest
 * @package VRobin\Weixin\Request\Apis
 */
class MediaUploadRequest extends Request
{
    protected $api = 'media/upload';

    protected $method = 'POST';

    /**
     * @param string $type 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     */
    public function setType($type)
    {
        $this->data['type'] = $type;
    }

    public function setMediaFile($file)
    {
        $this->data['@media'] = $file;
    }
}