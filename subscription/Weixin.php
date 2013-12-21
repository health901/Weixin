<?php

/**
 * 微信公众接口SDK 订阅号版
 * 
 * @author Viking Robin <admin@vkrobin.com>
 */
Class Weixin {

    private $_token;
    private $_data;
    private $_callback;
    private $_xml;

    /**
     * 创建SDK实例
     * 
     * @param string $token Token
     */
    public function __construct($token = NULL) {
	if ($token)
	    $this->_token = $token;
    }

    /**
     * 设置Token
     * 
     * @param string $token Token
     */
    public function setToken($token) {
	$this->_token = $token;
    }

    /**
     * 监听用户信息
     */
    public function listen() {
	$check = $this->checkSignature();
	if (FALSE === $check) {
	    echo '403';
	    return;
	}
	if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	    if ($check !== TRUE) {
		echo $check;
	    } else {
		echo $_SERVER['REQUEST_METHOD'];
		echo '404';
	    }
	} else {
	    $type = $this->parseData($_POST['body']);
	    if (isset($this->_callback[$type])) {
		call_user_func($this->_callback[$type], $this->_data);
	    } elseif (isset($this->_callback['undefined'])) {
		call_user_func($this->_callback['undefined'], $this->_data);
	    }
	}
    }

    /**
     * 对不同类型的用户消息设置对应的处理过程（回调函数）
     * 若设置了$type为undefind的回调函数，则所有未指定回调函数的消息类型将调用此函数
     * 接收到的用户消息（SimpleXMLElement）将作为唯一参数传递给回调函数
     * http://mp.weixin.qq.com/wiki/index.php?title=%E6%8E%A5%E6%94%B6%E6%99%AE%E9%80%9A%E6%B6%88%E6%81%AF
     * 
     * @param string $type 接收到的用户消息类型
     * @param callback $callback 回调函数
     */
    public function setCallback($type, $callback) {
	$this->_callback[strtolower($type)] = $callback;
    }

    /**
     * 回复文本消息
     * 
     * @param string $content 消息内容
     */
    public function responseText($content) {
	$this->_xml .= "<MsgType><![CDATA[text]]></MsgType>";
	$this->_xml .= "<Content><![CDATA[{$content}]]></Content>";
	$this->response();
    }

    /**
     * 回复图片消息
     * 
     * @param string $mediaid 通过上传多媒体文件，得到的id 
     */
    public function responseImage($mediaid) {
	$this->_xml .= "<MsgType><![CDATA[image]]></MsgType>";
	$this->_xml .= "<Image><MediaId><![CDATA[{$mediaid}]]></MediaId></Image>";
	$this->response();
    }

    /**
     * 回复语音消息
     * 
     * @param string $mediaid 通过上传多媒体文件，得到的id 
     */
    public function responseVoice($mediaid) {
	$this->_xml .= "<MsgType><![CDATA[voice]]></MsgType>";
	$this->_xml .= "<Voice><MediaId><![CDATA[{$mediaid}]]></MediaId></Voice>";
	$this->response();
    }

    /**
     * 回复视频消息
     * 
     * @param string $mediaid 通过上传多媒体文件，得到的id 
     * @param string $title 视频消息的标题 
     * @param string $desc 视频消息的描述
     */
    public function responseVideo($mediaid, $title = '', $desc = '') {
	$this->_xml .= "<MsgType><![CDATA[video]]></MsgType>";
	$this->_xml .= "<Video><MediaId><![CDATA[{$mediaid}]]></MediaId>Title><![CDATA[{$title}]]></Title><Description><![CDATA[{$desc}]]></Description></Video>";
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
     */
    public function responseMusic($mediaid, $title = '', $desc = '', $url = '', $hqurl = '') {
	$this->_xml .= "<MsgType><![CDATA[music]]></MsgType>";
	$this->_xml .= "<Music><Title><![CDATA[{$title}]]></Title><Description><![CDATA[DESCRIPTION{$desc}]]></Description><MusicUrl><![CDATA[{$url}]]></MusicUrl><HQMusicUrl><![CDATA[{$hqurl}]]></HQMusicUrl><ThumbMediaId><![CDATA[{$mediaid}]]></ThumbMediaId></Music>";
	$this->response();
    }

    /**
     * 回复图文消息
     * 图文消息个数，限制为10条以内
     * 
     * @param int $count 图文消息个数，限制为10条以内
     * @param array $articles 多个article构成的数组，article格式为array('title'=>'','description'=>'','picurl'=>'','url'=>'')
     */
    public function responseNews($count, $articles) {
	$this->_xml .= "<MsgType><![CDATA[news]]></MsgType>";
	$this->_xml .= "<ArticleCount>{$count}</ArticleCount>";
	$this->_xml .= "<Articles>";
	foreach ($articles as $article) {
	    $title = isset($article['title']) ? $article['title'] : '';
	    $desc = isset($article['description']) ? $article['description'] : '';
	    $picurl = isset($article['picurl']) ? $article['picurl'] : '';
	    $url = isset($article['url']) ? $article['url'] : '';
	    $this->_xml .= "<item><Title><![CDATA[{$title}]]></Title><Description><![CDATA[{$desc}]]></Description><PicUrl><![CDATA[{$picurl}]]></PicUrl><Url><![CDATA[{$url}]]></Url></item>";
	}
	$this->_xml .= "</Articles>";
	$this->response();
    }

    private function checkSignature() {
	if (empty($_GET['signature']) || empty($_GET['timestamp']) || empty($_GET['nonce'])) {
	    return FALSE;
	}
	$signature = $_GET["signature"];
	$timestamp = $_GET["timestamp"];
	$nonce = $_GET["nonce"];

	$tmpArr = array($this->_token, $timestamp, $nonce);
	sort($tmpArr);
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
    private function parseData($data) {
	$this->_data = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
	return strval($this->_data->MsgType);
    }

    private function response() {
	$t = time();
	$xml = "<ToUserName><![CDATA[{$this->_data->FromUserName}]]></ToUserName>";
	$xml .= "<FromUserName><![CDATA[{$this->_data->ToUserName}]]></FromUserName>";
	$xml .= "<CreateTime>{$t}</CreateTime>";
	$xml = "<xml>" . $xml . $this->_xml . "</xml>";
	echo $xml;
    }

}