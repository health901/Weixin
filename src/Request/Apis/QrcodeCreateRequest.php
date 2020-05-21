<?php


namespace VRobin\Weixin\Request\Apis;


use VRobin\Weixin\Exception\ParamsException;
use VRobin\Weixin\Request\Request;

/**
 * 创建二维码
 * Class QrcodeCreateRequest
 * @package VRobin\Weixin\Request\Apis
 * @see https://developers.weixin.qq.com/doc/offiaccount/Account_Management/Generating_a_Parametric_QR_Code.html
 */
class QrcodeCreateRequest extends Request
{
    protected $api = 'qrcode/create';
    protected $forever = true;
    protected $scene = '';

    /**
     * 临时二维码过期时间
     * @param $sec
     */
    public function setExpireSeconds($sec)
    {
        $this->data['expire_seconds'] = intval($sec);
        $this->forever = false;
    }

    /**
     * 设置场景值ID
     * @param $scene_id
     * @throws ParamsException
     */
    public function setSceneId($scene_id)
    {
        if ($this->scene != 'id') {
            throw new ParamsException('Can not set scene_id and scene_str both');
        }
        $this->data['action_info']['scene']['scene_id'] = $scene_id;
        $this->scene = 'id';
    }

    /**
     * 设置场景值ID
     * @param $scene_str
     * @throws ParamsException
     */
    public function setSceneStr($scene_str)
    {
        if ($this->scene != 'str') {
            throw new ParamsException('Can not set scene_id and scene_str both');
        }
        $this->data['action_info']['scene']['scene_str'] = $scene_str;
        $this->scene = 'str';
    }

    public function getData()
    {
        $action_name = $this->forever ? 'QR_LIMIT_SCENE' : 'QR_SCENE';
        if ($this->scene == 'str') {
            $action_name = str_replace('_SCENE', '_STR_SCENE', $action_name);
        }
        $this->data['action_name'] = $action_name;
        return parent::getData();
    }
}