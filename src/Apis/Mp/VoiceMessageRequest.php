<?php


namespace VRobin\Weixin\Apis\Mp;


class VoiceMessageRequest extends MessageCustomSend
{
    protected $msgtype = 'cgi-bin/voice';

    public function setMediaId($media_id)
    {
        $this->data['voice']['media_id'] = $media_id;
    }
}