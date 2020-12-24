<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Apis\ApiRequest;

/**
 * 上传临时素材
 * Class MediaUploadApiRequest
 * @package VRobin\Weixin\ApiRequest\Apis
 */
class MediaUploadApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/media/upload';

    protected $method = 'POST';

    protected $postJson = false;

    /**
     * @param string $type 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     */
    public function setType($type)
    {
        $this->queryData['type'] = $type;
    }

    public function setMediaFile($file)
    {
        $this->data['@media'] = $file;
    }
}