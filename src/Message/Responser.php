<?php


namespace VRobin\Weixin\Message;


class Responser
{

    /**
     * 文本消息
     */
    const TYPE_TEXT = 'text';
    /*
     * 图片消息
     */
    const TYPE_IMAGE = 'image';
    /*
     * 音频消息
     */
    const TYPE_VOICE = 'voice';

    /**
     * 视频消息
     */
    const TYPE_VEDIO = 'video';

    /**
     * 地理位置消息
     */
    const TYPE_LOCATION = 'location';

    /**
     * 链接消息
     */
    const TYPE_LINK = 'link';

    /**
     * 事件推送
     */
    const TYPE_EVENT = 'event';

    /**
     * 所有未被捕获的消息
     */
    const TYPE_UNDEFINED = 'undefined';

    /**
     * 订阅事件
     */
    const EVENT_SUBSCRIBE = 'subscribe';

    /**
     * 取消订阅事件
     */
    const EVENT_UNSUBSCRIBE = 'unsubscribe';

    /**
     * 扫描带参数二维码事件
     */
    const EVENT_SCAN = 'scan';

    /**
     * 上报地理位置事件 (自动上报事件,手动上报为location消息,不是event消息)
     */
    const EVENT_LOCATION = 'location';

    /*
     * 点击菜单拉取消息时的事件推送
     */
    const EVENT_CLICK = 'click';

    /**
     * 点击菜单跳转链接时的事件推送
     */
    const EVENT_VIEW = 'view';

    /**
     *
     * @var Result
     */
    protected $data;
    protected $token;
    protected $callbacks;
    protected $xml;
    protected $responseLock = False;
    protected $sender;


    /**
     * Responser constructor.
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * 监听用户消息
     */
    public function listen()
    {

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $check = $this->checkSignature();
            if (FALSE === $check) {
                return;
            } else {
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
                        foreach ($this->callbacks[$_type] as $callback) {
                            call_user_func($callback, $this->data);
                        }
                        break;
                    }
                }
            } else if (isset($this->callbacks[$type])) {
                foreach ($this->callbacks[$type] as $callback) {
                    call_user_func($callback, $this->data);
                }
            } elseif (isset($this->callbacks[self::TYPE_UNDEFINED])) {
                foreach ($this->callbacks[self::TYPE_UNDEFINED] as $callback) {
                    call_user_func($callback, $this->data);
                }
            }
        }
    }

    /**
     * 对不同类型的用户消息设置对应的处理过程（回调函数）
     * 若设置了$type为undefind的回调函数，则所有未指定回调函数的消息类型将调用此函数
     * 接收到的用户消息（WeixinResult）将作为唯一参数传递给回调函数
     * @see http://mp.weixin.qq.com/wiki/index.php?title=%E6%8E%A5%E6%94%B6%E6%99%AE%E9%80%9A%E6%B6%88%E6%81%AF
     *
     * @param string|array $type 消息类型,事件类型可以使用数组来描述,元素依次为 事件,事件名,事件值
     * @param callback $callback 回调函数
     * @return self
     */
    public function setCallback($type, $callback)
    {
        if (is_array($type)) {
            $this->callbacks[implode('.', $type)][] = $callback;
        } else {
            $this->callbacks[strtolower($type)][] = $callback;
        }
        return $this;
    }

    /**
     * 批量接口,使用方式参考setCallback
     * @param array $callbacks
     * @return self
     */
    public function setCallbacks($callbacks = array())
    {
        foreach ($callbacks as $callback) {
            $this->setCallback($callback['type'], $callback['callback']);
        }
        return $this;
    }

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
     * @return void
     */
    public function responseText($content)
    {
        $this->xml .= "<MsgType><![CDATA[text]]></MsgType>";
        $this->xml .= "<Content><![CDATA[{$content}]]></Content>";
        $this->response();
    }

    /**
     * 回复图片消息
     *
     * @param string $mediaid 通过上传多媒体文件，得到的id
     * @return array
     */
    public function responseImage($mediaid)
    {
        $this->xml .= "<MsgType><![CDATA[image]]></MsgType>";
        $this->xml .= "<Image><MediaId><![CDATA[{$mediaid}]]></MediaId></Image>";
        $this->response();
    }

    /**
     * 回复语音消息
     *
     * @param string $mediaid 通过上传多媒体文件，得到的id
     * @return array
     */
    public function responseVoice($mediaid)
    {
        $this->xml .= "<MsgType><![CDATA[voice]]></MsgType>";
        $this->xml .= "<Voice><MediaId><![CDATA[{$mediaid}]]></MediaId></Voice>";
        $this->response();
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
        $this->xml .= "<MsgType><![CDATA[video]]></MsgType>";
        $this->xml .= "<Video><MediaId><![CDATA[{$mediaid}]]></MediaId>Title><![CDATA[{$title}]]></Title><Description><![CDATA[{$desc}]]></Description></Video>";
        $this->response();
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
        $this->xml .= "<MsgType><![CDATA[music]]></MsgType>";
        $this->xml .= "<Music><Title><![CDATA[{$title}]]></Title><Description><![CDATA[{$desc}]]></Description><MusicUrl><![CDATA[{$url}]]></MusicUrl><HQMusicUrl><![CDATA[{$hqurl}]]></HQMusicUrl><ThumbMediaId><![CDATA[{$mediaid}]]></ThumbMediaId></Music>";
        $this->response();
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
     * @return self
     */
    protected function parseData($data)
    {
        $this->data = new Result($data);
        $this->sender = $this->data->FromUserName;
        return $this;
    }

    protected function getMsgType()
    {
        if ($this->data->MsgType == self::TYPE_EVENT) {
            $type = array($this->data->MsgType);
            $type[] = strtolower($this->data->Event);
            if ($this->data->EventKey) {
                $type[] = strtolower($this->data->EventKey);
            }
            return $type;
        } else {
            return strtolower($this->data->MsgType);
        }
    }
}