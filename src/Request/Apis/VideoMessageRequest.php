<?php


namespace VRobin\Weixin\Request\Apis;


class VideoMessageRequest extends MessageCustomSend
{
    protected $msgtype = 'video';


    /**
     * 发送视频消息
     *
     * @param string $media_id 通过上传多媒体文件，得到的id
     * @param string $title 视频消息的标题
     * @param string $desc 视频消息的描述
     * @return void
     */
    public function setVideo($media_id, $title, $desc)
    {
        $data['video'] = array(
            'media_id' => $media_id,
            'title' => $title,
            'description' => $desc
        );
    }
}