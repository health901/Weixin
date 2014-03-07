<?php

//引入库
require_once('Weixin.php');
//创建SDK实例
$weixin = new Weixin('vrobin');
/**
 * 监听用户消息
 * 用DemoClass类的location方法处理位置消息
 * 用catchAll函数处理其余消息
 * 
 * 务必确保回调中的Weixin对象和调用listen()方法的Weixin对象为同一个
 */
$class = new DemoClass($weixin);
$weixin->setCallback(Weixin::TYPE_UNDEFINED, 'catchAll')
		->setCallback(Weixin::TYPE_LOCATION, array($class, 'location'))
		->listen();

function catchAll(WeixinResult $data) {
	global $weixin;
	$weixin->responseText('Hello World,接受到一条' . $data->MsgType . '消息');
}

class DemoClass {

	function __construct($weixin) {
		$this->weixin = $weixin;
	}

	function location($data) {
		$this->weixin->responseText('Hello World,这是一条位置消息,你的位置为' . $data->Label);
	}

}