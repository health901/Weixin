<?php


namespace VRobin\Weixin\Apis\Mp;

class MusicMessageRequest extends MessageCustomSend
{
    protected $msgtype = 'cgi-bin/music';

    /**
     * 发送音乐消息
     *
     * @param string $media_id 缩略图的媒体id，通过上传多媒体文件，得到的id
     * @param string $title 音乐标题
     * @param string $desc 音乐描述
     * @param string $url 音乐链接
     * @param string $hqurl 高质量音乐链接，WIFI环境优先使用该链接播放音乐
     * @return void
     */
    public function setMusic($media_id, $title = '', $desc = '', $url = '', $hqurl = '')
    {
        $this->data['music'] = array(
            'thumb_media_id' => $media_id,
            'title' => $title,
            'description' => $desc,
            'musicurl' => $url,
            'hqmusicurl' => $hqurl
        );
    }
}