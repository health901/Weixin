<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Helper\Menu;
use VRobin\Weixin\Apis\ApiRequest;

class MenuCreateApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/menu/create';

    protected $method = 'POST';

    public function setMenu($data)
    {
        if ($data instanceof Menu) {
            $this->data = $data->toArray();
        } else {
            $this->data = $data;
        }
    }
}