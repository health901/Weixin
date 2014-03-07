<?php

/**
 * 接受消息结果辅助类
 * @author Viking Robin <admin@vkrobin.com>
 */
class WeixinResult {

	/**
	 *
	 * @property-read string $MsgType			消息类型
	 * @property-read string $ToUserName		开发者微信号
	 * @property-read string $FromUserName		发送方帐号
	 * @property-read string $CreateTime		创建时间
	 * @property-read string $Content			文本消息内容
	 * @property-read string $Event			事件类型
	 * @property-read string $EventKey			事件KEY值
	 * @property-read string $Ticket			二维码的ticket，可用来换取二维码图片
	 * @property-read string $MediaId 			图片消息媒体id，可以调用多媒体文件下载接口拉取数据
	 * @property-read string $ThumbMediaId 	视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据
	 * @property-read string $Location_X	 	地理位置纬度
	 * @property-read string $Location_Y	 	地理位置经度
	 * @property-read string $Scale			地图缩放大小
	 * @property-read string $Label			地理位置信息
	 * @property-read string $PicUrl			图片链接
	 * @property-read string $Description		消息描述 
	 * @property-read string $Title			消息标题
	 * @property-read string $Url				消息链接  
	 * @property-read string $Format			语音格式，如amr，speex等 
	 * @property-read string $MsgId			消息id
	 */
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