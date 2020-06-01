<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Request\Request;

class TicketGetTicketRequest extends Request
{
    protected $api = 'ticket/getticket';

    public function __construct()
    {
        $this->data['type'] = 'jsapi';
    }
}