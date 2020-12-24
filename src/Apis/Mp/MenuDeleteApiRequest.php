<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Apis\ApiRequest;

class MenuDeleteApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/menu/delete';

    protected $method = 'POST';
}