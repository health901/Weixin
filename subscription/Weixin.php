<?php

/**
 * 微信公众接口SDK 订阅号版
 * 
 * @author Viking Robin <admin@vkrobin.com>
 */
Class Weixin {

	private $token;
	private $data;
	private $callbacks;
	private $xml;
	private $responseLock = False;

	CONST TYPE_TEXT = 'text';
	CONST TYPE_IMAGE = 'image';
	CONST TYPE_VOICE = 'voice';
	CONST TYPE_VEDIO = 'video';
	CONST TYPE_LOCATION = 'location';
	CONST TYPE_LINK = 'link';
	CONST TYPE_EVENT = 'event';
	CONST TYPE_UNDEFINED = 'undefined';

	/**
	 * 创建SDK实例
	 * 
	 * @param string $token Token
	 */
	public function __construct($token = NULL) {
		if ($token)
			$this->token = $token;
	}

	/**
	 * 设置Token
	 * 
	 * @param string $token Token
	 */
	public function setToken($token) {
		$this->token = $token;
	}

	/**
	 * 监听用户消息
	 */
	public function listen() {
		$check = $this->checkSignature();
		if (FALSE === $check) {
			return;
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
			$type = $this->parseData($data);
			if (isset($this->callbacks[$type])) {
				call_user_func($this->callbacks[$type], $this->data);
			} elseif (isset($this->callbacks[self::TYPE_UNDEFINED])) {
				call_user_func($this->callbacks[self::TYPE_UNDEFINED], $this->data);
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
		$this->callbacks[strtolower($type)] = $callback;
		return $this;
	}

	/**
	 * 回复文本消息
	 * 
	 * @param string $content 消息内容
	 */
	public function responseText($content) {
		$this->xml .= "<MsgType><![CDATA[text]]></MsgType>";
		$this->xml .= "<Content><![CDATA[{$content}]]></Content>";
		$this->response();
	}

	/**
	 * 回复图片消息
	 * 
	 * @param string $mediaid 通过上传多媒体文件，得到的id 
	 */
	public function responseImage($mediaid) {
		$this->xml .= "<MsgType><![CDATA[image]]></MsgType>";
		$this->xml .= "<Image><MediaId><![CDATA[{$mediaid}]]></MediaId></Image>";
		$this->response();
	}

	/**
	 * 回复语音消息
	 * 
	 * @param string $mediaid 通过上传多媒体文件，得到的id 
	 */
	public function responseVoice($mediaid) {
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
	 */
	public function responseVideo($mediaid, $title = '', $desc = '') {
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
	 */
	public function responseMusic($mediaid, $title = '', $desc = '', $url = '', $hqurl = '') {
		$this->xml .= "<MsgType><![CDATA[music]]></MsgType>";
		$this->xml .= "<Music><Title><![CDATA[{$title}]]></Title><Description><![CDATA[DESCRIPTION{$desc}]]></Description><MusicUrl><![CDATA[{$url}]]></MusicUrl><HQMusicUrl><![CDATA[{$hqurl}]]></HQMusicUrl><ThumbMediaId><![CDATA[{$mediaid}]]></ThumbMediaId></Music>";
		$this->response();
	}

	/**
	 * 回复图文消息
	 * 图文消息个数，限制为10条以内
	 * 
	 * @param array $articles 多个article构成的数组，article格式为array('title'=>'','description'=>'','picurl'=>'','url'=>'')
	 */
	public function responseNews($articles) {
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

	private function checkSignature() {
		if (empty($_GET['signature']) || empty($_GET['timestamp']) || empty($_GET['nonce'])) {
			return FALSE;
		}
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];

		$tmpArr = array($this->token, $timestamp, $nonce);
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
		$this->data = new WeixinResult($data);
		return $this->data->MsgType;
	}

	private function response() {
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

}

/**
 * 
 * @author Viking Robin <admin@vkrobin.com>
 */
class WeixinResult {

	private $xml;

	public function __construct($xml) {
		$this->xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
	}

	public function __get($name) {
		if (property_exists($this->xml, $name)) {
			return strval($this->xml->$name);
		}
	}

}