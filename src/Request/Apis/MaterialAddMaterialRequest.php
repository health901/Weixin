<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Request\Request;

class MaterialAddMaterialRequest extends Request
{
    protected $api = 'material/add_material';

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