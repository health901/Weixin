<?php


namespace VRobin\Weixin\Request\Apis;


class TextMessageRequest extends MessageCustomSend
{
    protected $msgtype = 'text';

    public function setContent($content)
    {
        $this->data['text']['content'] = $content;
    }
}