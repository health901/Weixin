<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Apis\ApiRequest;

/**
 * 获取永久素材的列表
 * Class MaterialBatchgetMaterialApiRequest
 * @package VRobin\Weixin\ApiRequest\Apis
 */
class MaterialBatchgetMaterialApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/material/batchget_material';

    protected $method = 'POST';


    public function __construct()
    {
        $this->data['offset'] = 0;
    }

    /**
     * @param string $type 素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
     */
    public function setType($type)
    {
        $this->data['type'] = $type;
    }

    public function setOffset($offset)
    {
        $this->data['offset'] = $offset;
    }

    public function setCount($count)
    {
        $this->data['count'] = $count;
    }
}