<?php

/**
 * 微信公众接口SDK 服务号版
 * 
 * @author Viking Robin <admin@vkrobin.com>
 */
Class Weixin {

	private $token;
	private $data;
	private $callbacks;
	private $appid;
	private $secret;
	private $accessToken;
	private $sender;
	private $cacheDir;
	private $develop;

	/**
	 * 创建SDK实例
	 * 
	 * @param type $appid AppID
	 * @param type $secret APPSECRET
	 * @param type $token Token字符串
	 * @param type $cacheDir 缓存目录,需要有读写权限，默认为当前目录。该目录外部不可访问。
	 */
	public function __construct($appid, $secret, $token, $develop = false, $cacheDir = NULL) {
		$this->token = $token;
		$this->appid = $appid;
		$this->secret = $secret;
		if ($cacheDir) {
			$this->cacheDir = $cacheDir;
		}
		$this->develop = $develop;
		$this->accessToken = $this->getAccessToken();
	}

	public function listen() {
		$check = $this->checkSignature();
		if (FALSE === $check) {
//	    echo '401';
			return;
		}
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			if ($check !== TRUE) {
				echo $check;
			} else {
//		echo '404';
			}
		} else {
			$data = file_get_contents("php://input");
			if (!$data) {
//		echo '404';
				return;
			}
			$type = $this->parseData($data);
			if (isset($this->callbacks[$type])) {
				call_user_func($this->callbacks[$type], $this->data);
			} elseif (isset($this->callbacks['undefined'])) {
				call_user_func($this->callbacks['undefined'], $this->data);
			}
		}
	}

	/**
	 * 对不同类型的用户消息设置对应的处理过程（回调函数）
	 * 若设置了$type为undefind的回调函数，则所有未指定回调函数的消息类型将调用此函数
	 * 接收到的用户消息（SimpleXMLElement）将作为唯一参数传递给回调函数
	 * http://mp.weixin.qq.com/wiki/index.php?title=%E6%8E%A5%E6%94%B6%E6%99%AE%E9%80%9A%E6%B6%88%E6%81%AF
	 * 
	 * @param string $type
	 * @param callback $callback
	 */
	public function setCallback($type, $callback) {
		$this->callbacks[strtolower($type)] = $callback;
	}

	########################
	# 回复接口,只能回调中使用
	########################

	/**
	 * 获取发送者ID
	 * 
	 * @return string 发送消息的用户OpenID
	 */
	public function getSender() {
		return $this->sender;
	}

	/**
	 * 回复文本消息
	 * 
	 * @param string $content 消息内容
	 * @return array
	 */
	public function responseText($content) {
		return $this->sendText($this->sender, $content);
	}

	/**
	 * 回复图片消息
	 * 
	 * @param string $mediaid 通过上传多媒体文件，得到的id
	 * @return array
	 */
	public function responseImage($mediaid) {
		return $this->sendImage($this->sender, $mediaid);
	}

	/**
	 * 回复语音消息
	 * 
	 * @param string $mediaid 通过上传多媒体文件，得到的id
	 * @return array
	 */
	public function responseVoice($mediaid) {
		return $this->sendVoice($this->sender, $mediaid);
	}

	/**
	 * 回复视频消息
	 * 
	 * @param string $mediaid 通过上传多媒体文件，得到的id 
	 * @param string $title 视频消息的标题 
	 * @param string $desc 视频消息的描述
	 * @return array
	 */
	public function responseVideo($mediaid, $title = '', $desc = '') {
		return $this->sendVideo($this->sender, $mediaid, $title, $desc);
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
	public function responseMusic($mediaid, $title = '', $desc = '', $url = '', $hqurl = '') {
		return $this->sendMusic($this->sender, $mediaid, $title, $desc, $url, $hqurl);
	}

	/**
	 * 回复图文消息
	 * 图文消息个数，限制为10条以内
	 * 
	 * @param array $articles 多个article构成的数组，article格式为array('title'=>'','description'=>'','picurl'=>'','url'=>'')
	 * @return array
	 */
	public function responseNews($articles) {
		return $this->sendNews($this->sender, $articles);
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
	public function sendText($sendTo, $content) {
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
	public function sendImage($sendTo, $mediaid) {
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
	public function sendVoice($sendTo, $mediaid) {
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
	public function sendVideo($sendTo, $mediaid, $title = '', $desc = '') {
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
	public function sendMusic($sendTo, $mediaid, $title = '', $desc = '', $url = '', $hqurl = '') {
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
	 */
	public function sendNews($sendTo, $articles) {
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
	public function getGroup() {
		$json = $this->request('https://api.weixin.qq.com/cgi-bin/groups/get');
		return json_decode($json, TRUE);
	}

	/**
	 * 创建分组
	 * 
	 * @param string $name  分组名字（30个字符以内）
	 * @return array
	 */
	public function createGroup($name) {
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
	public function updateGroup($id, $name) {
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
	public function updateMenberGroup($openId, $toGroupId) {
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
	public function getUserInfo($openId) {
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
	public function getUserList($nextOpenId = NULL) {
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
	public function getMenu() {
		$json = $this->request('https://api.weixin.qq.com/cgi-bin/menu/get');
		return json_decode($json, TRUE);
	}

	/**
	 * 创建自定义菜单
	 * 
	 * @param mixed $buttons 自定义菜单数组 或 WeixinMenu辅助类
	 * @return array
	 */
	public function createMenu($buttons) {
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
	public function deleteMenu() {
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
	public function createQRCode($id, $expire = NULL) {
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
		$this->data = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
		$this->sender = strval($this->data->FromUserName);
		if (strval($this->data->MsgType) == 'event') {
			$return = 'event.' . $this->data->Event;
			if (isset($this->data->EventKey)) {
				$return .= '.' . $this->data->EventKey;
			}
			return $return;
		} else {
			return strval($this->data->MsgType);
		}
	}

	private function getAccessToken() {

		if (!$this->develop) {
			/**
			 * @todo 使用数据库等缓存最佳
			 */
			$cachefile = $this->cacheDir ? $this->cacheDir . '/weixin.cache' : dirname(__FILE__) . '/weixin.cache';
			if (file_exists($cachefile)) {
				$_cache = file_get_contents($cachefile);
				if ($_cache && $cache = unserialize($_cache)) {
					if ($cache['expire'] > time()) {
						return $cache['acccessToken'];
					}
				}
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
				/**
				 * @todo 使用数据库
				 */
				file_put_contents($cachefile, serialize($accessToken));
			}

			return $data['access_token'];
		}
	}

	/**
	 * 
	 * @param array $data
	 * @return string 
	 */
	private function send($data) {
		$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send';
		echo $this->unicodeDecode(json_encode($data));
		return json_decode($this->request($url, $data, 'post'), TRUE);
	}

	/**
	 * 
	 * @param string $url
	 * @param array $data
	 * @param string $method
	 * @return string
	 */
	private function request($url, $data = array(), $method = 'get') {
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
	private function curlGet($url, $params = array(), $option = array()) {

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
	private function curlPost($url, $params = NULL, $option = array()) {

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
	private function unicodeDecode($string) {
		$matches = NULL;
		preg_match_all("/\\\\u\w{4}/i", $string, $matches);
		if (!empty($matches)) {
			foreach ($matches[0] as $unicode) {
				$code = base_convert(substr($unicode, 2, 2), 16, 10);
				$code2 = base_convert(substr($unicode, 4), 16, 10);
				$c = chr($code) . chr($code2);
				$c = iconv('UCS-2', 'UTF-8', $c);
				$string = str_replace($unicode, $c, $string);
			}
		}
		return $string;
	}

}

/**
 * 微信自定义菜单辅助类
 * 
 * @author Viking Robin <admin@vkrobin.com>
 */
Class WeixinMenu {

	private $buttons = array();

	/**
	 * 创建一级菜单
	 * 
	 * @param string $name 菜单标题，不超过16个字节，子菜单不超过40个字节
	 * @param string $type 菜单的响应动作类型，目前有click、view两种类型 
	 * @param string $value  若为click类型,则为key的值，若为view类型，则为url链接
	 * @return boolean 返回是否创建成功
	 */
	public function addButton($name, $type = NULL, $value = NULL) {
		if (sizeof($this->buttons) == 3) {
			return FALSE;
		}
		if ($type == 'view') {
			$button['type'] = 'view';
			$button['url'] = $value;
		} else {
			$button['type'] = 'click';
			$button['key'] = $value;
		}
		$button['name'] = $name;
		$this->buttons[] = $button;
	}

	/**
	 * 创建二级菜单
	 * 
	 * @param int $index 父按钮序号 0~2 直接
	 * @param string $name 菜单标题，不超过16个字节，子菜单不超过40个字节
	 * @param string $type 菜单的响应动作类型，目前有click、view两种类型 
	 * @param string $value  若为click类型,则为key的值，若为view类型，则为url链接
	 * @return boolean 返回是否创建成功
	 */
	public function addSubButton($index, $name, $type, $value) {
		if (!isset($this->buttons[$index])) {
			return FALSE;
		}
		if (sizeof($this->buttons[$index]) == 5) {
			return FALSE;
		}
		if ($type == 'view') {
			$button['type'] = 'view';
			$button['url'] = $value;
		} else {
			$button['type'] = 'click';
			$button['key'] = $value;
		}
		$button['name'] = $name;
		$this->buttons[$index]['sub'][] = $button;
		return TRUE;
	}

	/**
	 * 导出数组
	 * 
	 * @return array
	 */
	public function toArray() {
		$buttons = array();
		foreach ($this->buttons as $button) {
			if (isset($button['sub'])) {
				$buttons[] = array('name' => $button['name'], 'sub_button' => $button['sub']);
			} else {
				$buttons[] = $button;
			}
		}
		return array('button' => $buttons);
	}

}