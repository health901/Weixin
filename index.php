<?php
//服务号
require_once('service/Weixin.php');
$weixin = new Weixin('wx047be627bb7116c3', '392f899f7e6c57c0ffeb588cf01674bc', 'robin', true);

//$weixin->develop = TRUE;	#开发者模式,不会生成缓存.

// 修改菜单
//$menu = new WeixinMenu();
//$menu->addButton('按钮1', 'click', 'EVENT_CLICK_1');
//$menu->addButton('按钮2', 'click', 'EVENT_CLICK_2');
//$menu->addButton('菜单');
//$menu->addSubButton(2, '谷歌', 'view', 'http://google.com');
//print_r($weixin->createMenu($menu));

//监听用户消息
$weixin->setCallback(Weixin::TYPE_UNDEFINED, 'catchAll');
$weixin->listen();

function catchAll($data) {
	global $weixin;
	$weixin->responseText('Hello World,接受到一条' . $data->MsgType . '消息');
}
