<?php


namespace VRobin\Weixin\Apis\Mp;

use VRobin\Weixin\Apis\ApiRequest;

/**
 * 发送模板消息
 * Class TemplateApiRequest
 * @package VRobin\Weixin\ApiRequest\Apis
 * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Template_Message_Interface.html#6
 */
class TemplateApiRequest extends ApiRequest
{
    protected $api = 'cgi-bin/message/template/send';

    protected $method = 'POST';

    public function setToUser($to)
    {
        $this->data['toUser'] = $to;
    }


    public function setTemplateId($id)
    {
        $this->data['template_id'] = $id;
    }

    public function setTemplateData($data)
    {
        $this->data['data'] = $data;
    }

    public function setUrl($url)
    {
        $this->data['url'] = $url;
    }

    public function setMiniProgram($appid, $pagepath)
    {
        $data['miniprogram'] = [
            "appid" => $appid,
            "pagepath" => $pagepath
        ];
    }
}