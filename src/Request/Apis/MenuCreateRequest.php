<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Helper\Menu;
use VRobin\Weixin\Request\Request;

class MenuCreateRequest extends Request
{
    protected $api = 'menu/create';
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