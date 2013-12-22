<?php
/**
 * 订阅号 
 */
//require_once('subscription/Weixin.php');
//$weixin = new Weixin('123');
//$weixin->setCallback('image', 'receiveImage');
//$weixin->listen();

/**
 * 服务号
 */
require_once('service/Weixin.php');
$weixin = new Weixin('123','wx047be627bb7116c3','392f899f7e6c57c0ffeb588cf01674bc');
//$weixin->setCallback('image', 'receiveImage');
//$weixin->listen();

//print_r($weixin->getUserList();


//$weixin->deleteMenu();
$menu = new WeixinMenu();
$menu->addButton('今日歌曲', 'click', 'V1001_TODAY_MUSIC');
$menu->addButton('歌手简介', 'click', 'V1001_TODAY_SINGER');
$menu->addButton('菜单');
$menu->addSubButton(2, '搜索', 'view', 'http://google.com');
print_r($weixin->createMenu($menu));
print_r($weixin->getMenu());

//print_r($weixin->createQRCode(2));

function receiveImage($data) {
    global $weixin;
    $r = $weixin->responseText('Receive an image message. Media Id: ' . $data->MediaId);
    var_dump($r);
}