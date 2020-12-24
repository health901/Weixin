<?php


namespace VRobin\Weixin\Apis\Mp;


use VRobin\Weixin\Apis\ApiRequest;

class MessageCustomSend extends ApiRequest
{
    protected $api = 'cgi-bin/message/custom/send';
    protected $method = 'POST';
    protected $msgtype = '';

    public function __construct()
    {
        $this->data['msgtype'] = $this->msgtype;
    }

    public function setToUser($to)
    {
        $this->data['toUser'] = $to;
    }
}