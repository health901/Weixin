<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Apis\ApiRequest;

/**
 * 获取永久素材
 * Class MaterialGetMaterialApiRequest
 * @package VRobin\Weixin\ApiRequest\Apis
 */
class MaterialGetMaterialApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/material/get_material';

    protected $method = 'POST';

    public function setMediaId($media_id)
    {
        $this->data['media_id'] = $media_id;
    }
}