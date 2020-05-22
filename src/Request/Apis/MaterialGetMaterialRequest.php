<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Request\Request;

/**
 * 获取永久素材
 * Class MaterialGetMaterialRequest
 * @package VRobin\Weixin\Request\Apis
 */
class MaterialGetMaterialRequest extends Request
{
    protected $api = 'material/get_material';

    protected $method = 'POST';

    public function setMediaId($media_id)
    {
        $this->data['media_id'] = $media_id;
    }
}