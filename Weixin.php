<?php

/**
 * 微信公众接口SDK
 * 
 * @author Viking Robin <admin@vkrobin.com>
 */
require_once('WeixinMenu.php');
require_once('WeixinResult.php');

/**
 * @property string $cacheType 缓存类型
 * @property string $cacheDir 缓存目录 
 * @property boolean $develop 开发者模式 
 */
Class Weixin
{

    protected $token;
    /**
     *
     * @var WeixinResult
     */
    protected $data;
    protected $callbacks;
    protected $xml;
    protected $appid;
    protected $secret;
    protected $accessToken;
    protected $sender;
    protected $isService = FALSE;
    protected $responseLock = False;
    public $cacheType = 'File';
    public $cacheDir = NULL;
    public $develop = FALSE;

    /**
     * 文本消息
     */
    CONST TYPE_TEXT = 'text';
    /*
     * 图片消息 
     */
    CONST TYPE_IMAGE = 'image';
    /*
     * 音频消息 
     */
    CONST TYPE_VOICE = 'voice';

    /**
     * 视频消息 
     */
    CONST TYPE_VEDIO = 'video';

    /**
     * 地理位置消息 
     */
    CONST TYPE_LOCATION = 'location';

    /**
     * 链接消息 
     */
    CONST TYPE_LINK = 'link';

    /**
     * 事件推送 
     */
    CONST TYPE_EVENT = 'event';

    /**
     * 所有未被捕获的消息 
     */
    CONST TYPE_UNDEFINED = 'undefined';

    /**
     * 订阅事件
     */
    CONST EVENT_SUBSCRIBE = 'subscribe';

    /**
     * 取消订阅事件
     */
    CONST EVENT_UNSUBSCRIBE = 'unsubscribe';

    /**
     * 取消订阅事件
     */
    CONST EVENT_SCAN = 'scan';

    /**
     * 上报地理位置事件 (自动上报事件,手动上报为location消息,不是event消息)
     */
    CONST EVENT_LOCATION = 'location';

    /*
     * 点击菜单拉取消息时的事件推送
     */
    CONST EVENT_CLICK = 'click';

    /**
     * 点击菜单跳转链接时的事件推送 
     */
    CONST EVENT_VIEW = 'view';

    /**
     * 实例
     * 
     * @var object 
     */
    protected static $instance;

    protected function __construct()
    {
        
    }

    /**
     * 创建SDK实例,并返回唯一的实例
     * 
     * @param string $token   Token
     * @param string $appid   AppId (服务号必填)
     * @param string $secret  AppSecret (服务号必填)
     * @param boolean $isService 是否是服务号
     * @return Weixin
     */
    public static function init($token, $appid = null, $secret = null, $isService = FALSE)
    {
        if (!isset(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class;
        }
        self::$instance->token = $token;
        self::$instance->isService = $isService;
        if ($appid && $token) {
            self::$instance->appid = $appid;
            self::$instance->secret = $secret;
            self::$instance->accessToken = self::$instance->getAccessToken();
            if (!self::$instance->accessToken) {
                self::$instance->isService = FALSE;
            }
        }
        return self::$instance;
    }

    /**
     * 返回唯一的实例
     * @return Weixin
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            die('instance not exist');
        }
        return self::$instance;
    }

    public function setDevelopMode($value = false)
    {
        $this->develop = $value;
        return $this;
    }

    /**
     * 设置缓存目录
     * @param type $dir 缓存目录
     */
    public function setCacheDir($dir)
    {
        $this->cacheDir = $dir;
    }

    /**
     * 监听用户消息
     */
    public function listen()
    {
        if (isset($_SERVER['WEIXIN_NO_SIGNATURE'])) {
            $check = TRUE;
        } else {
            $check = $this->checkSignature();
            if (FALSE === $check) {
                return;
            }
        }

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            if ($check !== TRUE) {
                echo $check;
            }
        } else {
            $data = file_get_contents("php://input");
            if (!$data) {
                return;
            }
            $type = $this->parseData($data)->getMsgType();
            if ($this->data->MsgType == self::TYPE_EVENT) {
                for ($i = 0; $i <= sizeof($type); $i++) {
                    $_type = implode('.', array_slice($type, 0, sizeof($type) - $i));
                    if (isset($this->callbacks[$_type])) {
                        call_user_func($this->callbacks[$_type], $this->data);
                        break;
                    }
                }
            } else if (isset($this->callbacks[$type])) {
                call_user_func($this->callbacks[$type], $this->data);
            } elseif (isset($this->callbacks[self::TYPE_UNDEFINED])) {
                call_user_func($this->callbacks[self::TYPE_UNDEFINED], $this->data);
            }
        }
    }

    /**
     * 对不同类型的用户消息设置对应的处理过程（回调函数）
     * 若设置了$type为undefind的回调函数，则所有未指定回调函数的消息类型将调用此函数
     * 接收到的用户消息（WeixinResult）将作为唯一参数传递给回调函数
     * @see http://mp.weixin.qq.com/wiki/index.php?title=%E6%8E%A5%E6%94%B6%E6%99%AE%E9%80%9A%E6%B6%88%E6%81%AF
     * 
     * @param string|array $type	消息类型,事件类型可以使用数组来描述,元素依次为 事件,事件名,事件值
     * @param callback $callback 回调函数
     */
    public function setCallback($type, $callback)
    {
        if (is_array($type)) {
            $this->callbacks[implode('.', $type)] = $callback;
        } else {
            $this->callbacks[strtolower($type)] = $callback;
        }
        return $this;
    }

    /**
     * 批量接口,使用方式参考setCallback
     * @param array $callbacks
     */
    public function setCallbacks($callbacks = array())
    {
        foreach ($callbacks as $callback) {
            $this->setCallback($callback['type'], $callback['callback']);
        }
        return $this;
    }

    ########################
    # 回复接口,只能回调中使用
    ########################

    /**
     * 获取发送者ID
     * 
     * @return string 发送消息的用户OpenID
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * 回复文本消息
     * 
     * @param string $content 消息内容
     * @return array
     */
    public function responseText($content)
    {
        if ($this->isService) {
            return $this->sendText($this->sender, $content);
        } else {
            $this->xml .= "<MsgType><![CDATA[text]]></MsgType>";
            $this->xml .= "<Content><![CDATA[{$content}]]></Content>";
            $this->response();
        }
    }

    /**
     * 回复图片消息
     * 
     * @param string $mediaid 通过上传多媒体文件，得到的id
     * @return array
     */
    public function responseImage($mediaid)
    {
        if ($this->isService) {
            return $this->sendImage($this->sender, $mediaid);
        } else {
            $this->xml .= "<MsgType><![CDATA[image]]></MsgType>";
            $this->xml .= "<Image><MediaId><![CDATA[{$mediaid}]]></MediaId></Image>";
            $this->response();
        }
    }

    /**
     * 回复语音消息
     * 
     * @param string $mediaid 通过上传多媒体文件，得到的id
     * @return array
     */
    public function responseVoice($mediaid)
    {
        if ($this->isService) {
            return $this->sendVoice($this->sender, $mediaid);
        } else {
            $this->xml .= "<MsgType><![CDATA[voice]]></MsgType>";
            $this->xml .= "<Voice><MediaId><![CDATA[{$mediaid}]]></MediaId></Voice>";
            $this->response();
        }
    }

    /**
     * 回复视频消息
     * 
     * @param string $mediaid 通过上传多媒体文件，得到的id 
     * @param string $title 视频消息的标题 
     * @param string $desc 视频消息的描述
     * @return array
     */
    public function responseVideo($mediaid, $title = '', $desc = '')
    {
        if ($this->isService) {
            return $this->sendVideo($this->sender, $mediaid, $title, $desc);
        } else {
            $this->xml .= "<MsgType><![CDATA[video]]></MsgType>";
            $this->xml .= "<Video><MediaId><![CDATA[{$mediaid}]]></MediaId>Title><![CDATA[{$title}]]></Title><Description><![CDATA[{$desc}]]></Description></Video>";
            $this->response();
        }
    }

    /**
     * 回复音乐消息
     * 
     * @param string $mediaid 缩略图的媒体id，通过上传多媒体文件，得到的id
     * @param string $title 音乐标题
     * @param string $desc 音乐描述
     * @param string $url 音乐链接
     * @param string $hqurl 高质量音乐链接，WIFI环境优先使用该链接播放音乐
     * @return array
     */
    public function responseMusic($mediaid, $title = '', $desc = '', $url = '', $hqurl = '')
    {
        if ($this->isService) {
            return $this->sendMusic($this->sender, $mediaid, $title, $desc, $url, $hqurl);
        } else {
            $this->xml .= "<MsgType><![CDATA[music]]></MsgType>";
            $this->xml .= "<Music><Title><![CDATA[{$title}]]></Title><Description><![CDATA[DESCRIPTION{$desc}]]></Description><MusicUrl><![CDATA[{$url}]]></MusicUrl><HQMusicUrl><![CDATA[{$hqurl}]]></HQMusicUrl><ThumbMediaId><![CDATA[{$mediaid}]]></ThumbMediaId></Music>";
            $this->response();
        }
    }

    /**
     * 回复图文消息
     * 图文消息个数，限制为10条以内
     * 
     * @param array $articles 多个article构成的数组，article格式为array('title'=>'','description'=>'','picurl'=>'','url'=>'')
     * @return array
     */
    public function responseNews($articles)
    {
        if ($this->isService) {
            return $this->sendNews($this->sender, $articles);
        } else {
            $count = sizeof($articles);
            $this->xml .= "<MsgType><![CDATA[news]]></MsgType>";
            $this->xml .= "<ArticleCount>{$count}</ArticleCount>";
            $this->xml .= "<Articles>";
            foreach ($articles as $article) {
                $title = isset($article['title']) ? $article['title'] : '';
                $desc = isset($article['description']) ? $article['description'] : '';
                $picurl = isset($article['picurl']) ? $article['picurl'] : '';
                $url = isset($article['url']) ? $article['url'] : '';
                $this->xml .= "<item><Title><![CDATA[{$title}]]></Title><Description><![CDATA[{$desc}]]></Description><PicUrl><![CDATA[{$picurl}]]></PicUrl><Url><![CDATA[{$url}]]></Url></item>";
            }
            $this->xml .= "</Articles>";
            $this->response();
        }
    }

    protected function response()
    {
        if ($this->responseLock)
            return;
        $this->responseLock = TRUE;
        $t = time();
        $xml = "<ToUserName><![CDATA[{$this->data->FromUserName}]]></ToUserName>";
        $xml .= "<FromUserName><![CDATA[{$this->data->ToUserName}]]></FromUserName>";
        $xml .= "<CreateTime>{$t}</CreateTime>";
        $xml = "<xml>" . $xml . $this->xml . "</xml>";
        echo $xml;
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
     * @see http://mp.weixin.qq.com/wiki/index.php?title=%E5%8F%91%E9%80%81%E5%AE%A2%E6%9C%8D%E6%B6%88%E6%81%AF#.E5.8F.91.E9.80.81.E5.9B.BE.E6.96.87.E6.B6.88.E6.81.AF
     * @return array
     */
    public function sendNews($sendTo, $articles)
    {
        $data['touser'] = $sendTo;
        $data['msgtype'] = 'news';
        $data['news']['articles'] = $articles;
        return $this->send($data);
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
        $json = $this->request('https://api.weixin.qq.com/cgi-bin/groups/get');
        return json_decode($json, TRUE);
    }

    /**
     * 创建分组
     * 
     * @param string $name  分组名字（30个字符以内）
     * @return array
     */
    public function createGroup($name)
    {
        $json = $this->request('https://api.weixin.qq.com/cgi-bin/groups/create', array('group' => array('name' => $name)), 'post');
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
        $json = $this->request('https://api.weixin.qq.com/cgi-bin/groups/update', $data, 'post');
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
        $json = $this->request('https://api.weixin.qq.com/cgi-bin/groups/members/update', $data, 'post');
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
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?openid=' . $openId;
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
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get';
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
        $json = $this->request('https://api.weixin.qq.com/cgi-bin/menu/get');
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
        $json = $this->request('https://api.weixin.qq.com/cgi-bin/menu/create', $data, 'post');
        return json_decode($json, TRUE);
    }

    /**
     * 删除自定义菜单
     * 
     * @return array
     */
    public function deleteMenu()
    {
        $json = $this->request('https://api.weixin.qq.com/cgi-bin/menu/delete');
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
        $json = $this->request('https://api.weixin.qq.com/cgi-bin/qrcode/create', $data, 'post');
        $result = json_decode($json, TRUE);
        if (!isset($result['errorcode'])) {
            $result['link'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $result['ticket'];
        }
        return $result;
    }

    ########################################

    protected function checkSignature()
    {
        if (empty($_GET['signature']) || empty($_GET['timestamp']) || empty($_GET['nonce'])) {
            return FALSE;
        }
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $tmpArr = array($this->token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = sha1(implode($tmpArr));

        if ($tmpStr == $signature) {
            if (isset($_GET['echostr'])) {
                return $_GET['echostr'];
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function parseData($data)
    {
        $this->data = new WeixinResult($data);
        $this->sender = $this->data->FromUserName;
        return $this;
    }

    protected function getMsgType()
    {
        if ($this->data->MsgType == self::TYPE_EVENT) {
            $type = array($this->data->MsgType);
            $type[] = $this->data->Event;
            if (isset($this->data->EventKey)) {
                $type[] = $this->data->EventKey;
            }
            return $type;
        } else {
            return $this->data->MsgType;
        }
    }

    protected function getAccessToken()
    {

        if (!$this->develop) {
            $cache = $this->getCache('acccessToken');
            if ($cache && $cache['expire'] > time()) {
                return $cache['acccessToken'];
            }
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/token';
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
     * 
     * @param array $data
     * @return string 
     */
    protected function send($data)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send';
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
        if (!$this->accessToken) {
            die("Cannot get accessToken");
        }
        if (FALSE === strpos('?', $url)) {
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

            if (FALSE === strpos('?', $url)) {
                $url = $url . '?' . $p;
            } else {
                $url = $url . '&' . $p;
            }
        }
        $ch = curl_init($url);
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

        $ch = curl_init($url);
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

    protected function getFileCache($key)
    {
        $cachefile = $this->cacheDir ? $this->cacheDir . '/weixin.cache' : dirname(__FILE__) . '/weixin.cache';
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
        $cachefile = $this->cacheDir ? $this->cacheDir . '/weixin.cache' : dirname(__FILE__) . '/weixin.cache';
        $cache = array($key => $value);
        file_put_contents($cachefile, serialize($cache));
    }

}
