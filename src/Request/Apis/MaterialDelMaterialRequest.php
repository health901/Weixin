<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Request\Request;

class MaterialDelMaterialRequest extends Request
{
    protected $api = 'material/del_material';

    protected $method = 'POST';

    public function setMediaId($media_id)
    {
        $this->data['media_id'] = $media_id;
    }
}