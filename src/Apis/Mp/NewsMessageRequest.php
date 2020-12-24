<?php


namespace VRobin\Weixin\Apis\Mp;


class NewsMessageRequest extends MessageCustomSend
{
    protected $msgtype = 'cgi-bin/news';

    /**
     * 发送图文消息
     * 图文消息个数，限制为10条以内
     *
     * @param array $articles 多个article构成的数组，article格式为array('title'=>'','description'=>'','picurl'=>'','url'=>'')
     * @return void
     * @see http://mp.weixin.qq.com/wiki/index.php?title=%E5%8F%91%E9%80%81%E5%AE%A2%E6%9C%8D%E6%B6%88%E6%81%AF#.E5.8F.91.E9.80.81.E5.9B.BE.E6.96.87.E6.B6.88.E6.81.AF
     */
    public function setNews($articles)
    {
        $data['news']['articles'] = $articles;
    }
}