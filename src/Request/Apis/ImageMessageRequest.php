<?php


namespace VRobin\Weixin\Request\Apis;


class ImageMessageRequest extends MessageCustomSend
{
    protected $msgtype = 'image';

    public function setMediaId($media_id)
    {
        $this->data['image']['media_id'] = $media_id;
    }
}