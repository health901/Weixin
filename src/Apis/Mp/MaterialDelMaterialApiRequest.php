<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Apis\ApiRequest;

class MaterialDelMaterialApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/material/del_material';

    protected $method = 'POST';

    public function setMediaId($media_id)
    {
        $this->data['media_id'] = $media_id;
    }
}