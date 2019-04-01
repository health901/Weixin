<?php


namespace VRobin\Weixin;


class WeixinServiceApi
{
    protected $api = "https://api.weixin.qq.com/cgi-bin/";

    protected $appid;
    protected $secret;
    protected $accessToken;
    protected $isService = FALSE;
    public $compatibleJS = FALSE;
    public $cacheType = 'File';
    public $cacheDir = NULL;
    public $cacheFile = NULL;
    public $develop = FALSE;


    public function __construct($appid,$secret)
    {
        $this->appid = $appid;
        $this->secret = $secret;
    }

    public function config($data){
        foreach ($data as $k=>$v){
            if(property_exists($this,$k)){
                $this->$k = $v;
            }
        }
    }

    public function setDevelopMode($value = false)
    {
        $this->develop = $value;
        return $this;
    }

    /**
     * 设置缓存目录
     * @param type $dir 缓存目录
     * @return WeixinServiceApi
     */
    public function setCacheDir($dir)
    {
        $this->cacheDir = $dir;
        return $this;
    }

    /**
     * 设置缓存路径,带文件名
     * @param type $file 缓存路径
     * @return WeixinServiceApi
     */
    public function setCachePath($file)
    {
        $pathinfo = pathinfo($file);
        $this->setCacheDir($pathinfo['dirname']);
        $this->cacheFile = $pathinfo['basename'];
        return $this;
    }




    ########################
    # 客服消息接口
    ########################

    /**
     * 发送文本消息
     *
     * @param string $sendTo 普通用户openid
     * @param string $content 消息内容
     * @return array
     */
    public function sendText($sendTo, $content)
    {
        $data['touser'] = $sendTo;
        $data['msgtype'] = 'text';
        $data['text']['content'] = $content;
        return $this->send($data);
    }

    /**
     * 发送图片消息
     *
     * @param string $sendTo 普通用户openid
     * @param string $mediaid 通过上传多媒体文件，得到的id
     * @return array
     */
    public function sendImage($sendTo, $mediaid)
    {
        $data['touser'] = $sendTo;
        $data['msgtype'] = 'image';
        $data['image']['media_id'] = $mediaid;
        return $this->send($data);
    }

    /**
     * 发送语音消息
     *
     * @param string $sendTo 普通用户openid
     * @param string $mediaid 通过上传多媒体文件，得到的id
     * @return array
     */
    public function sendVoice($sendTo, $mediaid)
    {
        $data['touser'] = $sendTo;
        $data['msgtype'] = 'voice';
        $data['voice']['media_id'] = $mediaid;
        return $this->send($data);
    }

    /**
     * 发送视频消息
     *
     * @param string $sendTo 普通用户openid
     * @param string $mediaid 通过上传多媒体文件，得到的id
     * @param string $title 视频消息的标题
     * @param string $desc 视频消息的描述
     * @return array
     */
    public function sendVideo($sendTo, $mediaid, $title = '', $desc = '')
    {
        $data['touser'] = $sendTo;
        $data['msgtype'] = 'video';
        $data['video'] = array(
            'media_id' => $mediaid,
            'title' => $title,
            'description' => $desc
        );
        return $this->send($data);
    }

    /**
     * 发送音乐消息
     *
     * @param string $sendTo 普通用户openid
     * @param string $mediaid 缩略图的媒体id，通过上传多媒体文件，得到的id
     * @param string $title 音乐标题
     * @param string $desc 音乐描述
     * @param string $url 音乐链接
     * @param string $hqurl 高质量音乐链接，WIFI环境优先使用该链接播放音乐
     * @return array
     */
    public function sendMusic($sendTo, $mediaid, $title = '', $desc = '', $url = '', $hqurl = '')
    {
        $data['touser'] = $sendTo;
        $data['msgtype'] = 'music';
        $data['music'] = array(
            'thumb_media_id' => $mediaid,
            'title' => $title,
            'description' => $desc,
            'musicurl' => $url,
            'hqmusicurl' => $hqurl
        );
        return $this->send($data);
    }

    /**
     * 发送图文消息
     * 图文消息个数，限制为10条以内
     *
     * @param string $sendTo 普通用户openid
     * @param array $articles 多个article构成的数组，article格式为array('title'=>'','description'=>'','picurl'=>'','url'=>'')
     * @return array
     * @see http://mp.weixin.qq.com/wiki/index.php?title=%E5%8F%91%E9%80%81%E5%AE%A2%E6%9C%8D%E6%B6%88%E6%81%AF#.E5.8F.91.E9.80.81.E5.9B.BE.E6.96.87.E6.B6.88.E6.81.AF
     */
    public function sendNews($sendTo, $articles)
    {
        $data['touser'] = $sendTo;
        $data['msgtype'] = 'news';
        $data['news']['articles'] = $articles;
        return $this->send($data);
    }

    /**
     * 发送模板消息
     * @param string $sendTo 普通用户openid
     * @param string $templateId 模板消息ID
     * @param string $url 模板跳转链接
     * @param array $tmpData 模板消息数据
     * @param array $mimiProgram
     * @return array
     * @see https://mp.weixin.qq.com/wiki?action=doc&id=mp1433751277&t=0.8050389632632116#6
     */
    public function sendTemplate($sendTo, $templateId, $tmpData, $url = NULL, $mimiProgram = NULL)
    {
        $api = "message/template/send";
        $data['touser'] = $sendTo;
        $data['template_id'] = $templateId;
        $data['data'] = $tmpData;
        if ($url) {
            $data['url'] = $url;
        }
        if ($mimiProgram) {
            $data['miniprogram'] = $mimiProgram;
        }
        return json_decode($this->request($api, $data, 'post'), TRUE);
    }

    ########################
    # 用户管理接口
    ########################
    //分组管理接口

    /**
     * 查询分组
     *
     * @return array
     */
    public function getGroup()
    {
        $json = $this->request('groups/get');
        return json_decode($json, TRUE);
    }

    /**
     * 创建分组
     *
     * @param string $name 分组名字（30个字符以内）
     * @return array
     */
    public function createGroup($name)
    {
        $json = $this->request('groups/create', array('group' => array('name' => $name)), 'post');
        return json_decode($json, TRUE);
    }

    /**
     * 修改分组名
     *
     * @param int $id 分组id，由微信分配
     * @param string $name 分组名字（30个字符以内）
     * @return array
     */
    public function updateGroup($id, $name)
    {
        $data = array('group' => array('name' => $name, 'id' => $id));
        $json = $this->request('groups/update', $data, 'post');
        return json_decode($json, TRUE);
    }

    /**
     * 移动用户分组
     *
     * @param string $openId 用户唯一标识符
     * @param int $toGroupId 分组id
     * @return array
     */
    public function updateMenberGroup($openId, $toGroupId)
    {
        $data = array('openid' => $openId, 'to_groupid' => $toGroupId);
        $json = $this->request('groups/members/update', $data, 'post');
        return json_decode($json, TRUE);
    }

    //

    /**
     * 获取用户基本信息
     *
     * @param string $openId 普通用户的标识，对当前公众号唯一
     * @return array
     */
    public function getUserInfo($openId)
    {
        $url = 'user/info?openid=' . $openId;
        $json = $this->request($url);
        return json_decode($json, TRUE);
    }

    /**
     * 获取关注者列表
     * 一次10000条，超过10000条需要传入nextOpenId作为起始ID进行多次抓取
     *
     * @param string $nextOpenId
     * @return array
     */
    public function getUserList($nextOpenId = NULL)
    {
        $url = 'user/get';
        if ($nextOpenId) {
            $url .= '?next_openid=' . $nextOpenId;
        }
        $json = $this->request($url);
        return json_decode($json, TRUE);
    }

    ########################
    # 自定义菜单接口
    ########################

    /**
     * 查询自定义菜单
     *
     * @return array
     */
    public function getMenu()
    {
        $json = $this->request('menu/get');
        return json_decode($json, TRUE);
    }

    /**
     * 创建自定义菜单
     *
     * @param mixed $buttons 自定义菜单数组 或 WeixinMenu辅助类
     * @return array
     */
    public function createMenu($buttons)
    {
        if ($buttons instanceof WeixinMenu) {
            $data = $buttons->toArray();
        } else {
            $data = $buttons;
        }
        $json = $this->request('menu/create', $data, 'post');
        return json_decode($json, TRUE);
    }

    /**
     * 删除自定义菜单
     *
     * @return array
     */
    public function deleteMenu()
    {
        $json = $this->request('menu/delete');
        return json_decode($json, TRUE);
    }

    ########################
    # 二维码接口
    ########################

    /**
     * 生成带参数的二维码
     *
     * @param int $id 场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为1000（目前参数只支持1--1000）
     * @param int $expire 该二维码有效时间，以秒为单位。 最大不超过1800。 若设置为0或空值，则二维码为永久二维码，反之则为临时二维码
     * @return array
     */
    public function createQRCode($id, $expire = NULL)
    {
        if ($expire) {
            $data['expire_seconds'] = intval($expire);
            $data['action_name'] = 'QR_SCENE';
        } else {
            $data['action_name'] = 'QR_LIMIT_SCENE';
        }
        $data['action_info']['scene']['scene_id'] = $id;
        $json = $this->request('qrcode/create', $data, 'post');
        $result = json_decode($json, TRUE);
        if (!isset($result['errorcode'])) {
            $result['link'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $result['ticket'];
        }
        return $result;
    }

    ########################################


    public function getAccessToken()
    {
        if ($this->compatibleJS) {
            return $this->getJsSDKAccessToken();
        }
        if (!$this->develop) {
            $cache = $this->getCache('acccessToken');
            if ($cache && $cache['expire'] > time()) {
                return $cache['acccessToken'];
            }
        }
        $url = 'token';
        $params = array('grant_type' => 'client_credential', 'appid' => $this->appid, 'secret' => $this->secret);
        $_data = $this->curlGet($url, $params);
        if ($_data) {
            $data = json_decode($_data, TRUE);
            if (isset($data['errcode'])) {
                die($data['errmsg']);
            }

            $expire = time() + $data['expires_in'] - 200;
            $accessToken = array('acccessToken' => $data['access_token'], 'expire' => $expire);


            if (!$this->develop) {
                $this->setCache('acccessToken', $accessToken);
            }

            return $data['access_token'];
        }
    }

    /**
     * 兼容JSSDK的Token保存
     * @return string
     */
    public function getJsSDKAccessToken()
    {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = null;
        if (file_exists($this->cacheDir . "/access_token.json")) {
            $data = json_decode(file_get_contents($this->cacheDir . "/access_token.json"), true);
        }

        if (!$data || $data['expire_time'] < time()) {
            // 如果是企业号用以下URL获取access_token
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
            $url = "token";
            $params = array('grant_type' => 'client_credential', 'appid' => $this->appid, 'secret' => $this->secret);
            $res = json_decode($this->curlGet($url, $params));
            $access_token = $res->access_token;
            if ($access_token) {
                $data['expire_time'] = time() + 7000;
                $data['access_token'] = $access_token;
                $fp = fopen($this->cacheDir . "/access_token.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $access_token = $data['access_token'];
        }
        return $access_token;
    }

    /**
     *
     * @param array $data
     * @return string
     */
    protected function send($data)
    {
        $url = 'message/custom/send';
        return json_decode($this->request($url, $data, 'post'), TRUE);
    }

    /**
     *
     * @param string $url
     * @param array $data
     * @param string $method
     * @return string
     */
    protected function request($url, $data = array(), $method = 'get')
    {
        $this->accessToken = $this->getAccessToken();
        if (!$this->accessToken) {
            die("Cannot get accessToken");
        }
        if (FALSE === strpos($url, '?')) {
            $connect = '?';
        } else {
            $connect = '&';
        }
        $url = $url . $connect . 'access_token=' . $this->accessToken;
        $params = array();
        if ($method == 'post') {
            if (!empty($data))
                $params = $this->unicodeDecode(json_encode($data));
            return $this->curlPost($url, $params);
        } else {
            return $this->curlGet($url, $data);
        }
    }

    /**
     *
     * @param string $url
     * @param array $params
     * @param array $option
     * @return string
     */
    protected function curlGet($url, $params = array(), $option = array())
    {

        if (!empty($params)) {
            $p = http_build_query($params);

            if (FALSE === strpos($url, '?')) {
                $url = $url . '?' . $p;
            } else {
                $url = $url . '&' . $p;
            }
        }
        $ch = curl_init($this->api . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt_array($ch, $option);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result ? $result : false;
    }

    /**
     *
     * @param string $url
     * @param mixed $params
     * @param array $option
     * @return string
     */
    protected function curlPost($url, $params = NULL, $option = array())
    {

        if (is_array($params) && !empty($params)) {
            $p = http_build_query($params);
        } else {
            $p = $params;
        }

        $ch = curl_init($this->api . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
        curl_setopt_array($ch, $option);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result ? $result : false;
    }

    /**
     *
     * @param string $string
     * @return string
     */
    protected function unicodeDecode($string)
    {
        $matches = NULL;
        preg_match_all("/\\\\u\w{4}/i", $string, $matches);
        if (!empty($matches)) {
            foreach ($matches[0] as $unicode) {
                $code = base_convert(substr($unicode, 2, 2), 16, 10);
                $code2 = base_convert(substr($unicode, 4), 16, 10);
                $c = chr($code) . chr($code2);
                $c = iconv('UCS-2BE', 'UTF-8', $c);
                $string = str_replace($unicode, $c, $string);
            }
        }
        return $string;
    }

    /**
     * 缓存
     * @todo 使用缓存类来扩展缓存功能
     */
    protected function getCache($key)
    {
        $cacheHandel = 'get' . $this->cacheType . 'Cache';
        return $this->$cacheHandel($key);
    }

    protected function setCache($key, $value = NULL)
    {
        $cacheHandel = 'set' . $this->cacheType . 'Cache';
        return $this->$cacheHandel($key, $value);
    }

    protected function getCacheFile()
    {
        $dir = $this->cacheDir ? $this->cacheDir : dirname(__FILE__);
        $file = $this->cacheFile ? $this->cacheFile : 'weixin.cache';
        return $dir . '/' . $file;
    }

    protected function getFileCache($key)
    {
        $cachefile = $this->getCacheFile();
        if (file_exists($cachefile)) {
            $_cache = file_get_contents($cachefile);
            if ($_cache && $cache = unserialize($_cache)) {
                return $cache[$key];
            }
        }
        return false;
    }

    protected function setFileCache($key, $value = NULL)
    {
        $cachefile = $this->getCacheFile();
        $cache = array($key => $value);
        file_put_contents($cachefile, serialize($cache));
    }
}