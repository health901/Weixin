<?php


namespace VRobin\Weixin\Apis\Mp;


class TextMessageRequest extends MessageCustomSend
{
    protected $msgtype = 'cgi-bin/text';

    public function setContent($content)
    {
        $this->data['text']['content'] = $content;
    }
}