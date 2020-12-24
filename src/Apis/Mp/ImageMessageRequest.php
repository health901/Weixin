<?php


namespace VRobin\Weixin\Apis\Mp;


class ImageMessageRequest extends MessageCustomSend
{
    protected $msgtype = 'cgi-bin/image';

    public function setMediaId($media_id)
    {
        $this->data['image']['media_id'] = $media_id;
    }
}