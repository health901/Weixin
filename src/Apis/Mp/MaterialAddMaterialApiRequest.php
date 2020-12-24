<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Apis\ApiRequest;

class MaterialAddMaterialApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/material/add_material';

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