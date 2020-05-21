<?php


namespace VRobin\Weixin\Request\Apis;


class VoiceMessageRequest extends MessageCustomSend
{
    protected $msgtype = 'voice';

    public function setMediaId($media_id)
    {
        $this->data['voice']['media_id'] = $media_id;
    }
}