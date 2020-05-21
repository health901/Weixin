<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Request\Request;

class MessageCustomSend extends Request
{
    protected $api = 'message/custom/send';
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