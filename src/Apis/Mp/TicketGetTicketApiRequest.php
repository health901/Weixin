<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Apis\ApiRequest;

class TicketGetTicketApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/ticket/getticket';

    public function __construct()
    {
        $this->data['type'] = 'jsapi';
    }
}