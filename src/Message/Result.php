<?php
namespace VRobin\Weixin\Message;
/**
 * 接受消息结果辅助类
 * @author Viking Robin <admin@vkrobin.com>
 */

/**
 * @property-read Object $xml            SimpleXML格式消息
 * @property-read string $MsgType            消息类型
 * @property-read string $ToUserName        开发者微信号
 * @property-read string $FromUserName        发送方帐号
 * @property-read string $CreateTime        创建时间
 * @property-read string $Content            文本消息内容
 * @property-read string $Event             事件类型
 * @property-read string $EventKey            事件KEY值
 * @property-read string $Ticket            二维码的ticket，可用来换取二维码图片
 * @property-read string $MediaId            图片消息媒体id，可以调用多媒体文件下载接口拉取数据
 * @property-read string $ThumbMediaId      视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据
 * @property-read string $Location_X	 	地理位置纬度
 * @property-read string $Location_Y	 	地理位置经度
 * @property-read string $Scale             地图缩放大小
 * @property-read string $Label             地理位置信息
 * @property-read string $PicUrl			图片链接
 * @property-read string $Description		消息描述 
 * @property-read string $Title             消息标题
 * @property-read string $Url				消息链接  
 * @property-read string $Format			语音格式，如amr，speex等 
 * @property-read string $MsgId             消息id
 */
class Result
{

    protected $xml;
    protected $key;

    public function __construct($xml, $key = '')
    {
        $xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $this->xml = json_decode(json_encode($xml));
        $this->key = $key;
        if ($this->Encrypt) {
            $this->aesDecode();
        }
    }

    public function __get($name)
    {
        if (property_exists($this->xml, $name)) {
            return $this->xml->$name;
        }

        $method = 'get' . $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }

    /**
     * @return Object
     */
    public function getXml()
    {
        return $this->xml;
    }

    protected function aesDecode()
    {
        $key = $this->key . '=';
        $result = openssl_decrypt(base64_decode($this->Encrypt), 'AES-256-CBC', base64_decode($key), OPENSSL_RAW_DATA);

        if (strlen($result) < 16)
            return "";
        $content = substr($result, 16, strlen($result));
        $len_list = unpack("N", substr($content, 0, 4));
        $xml_len = $len_list[1];
        $xml_content = substr($content, 4, $xml_len);
        $this->xml = simplexml_load_string($xml_content, 'SimpleXMLElement', LIBXML_NOCDATA);
    }
}
