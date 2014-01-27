<?php
require_once('service/Weixin.php');
$weixin = new Weixin('wx047be627bb7116c3','392f899f7e6c57c0ffeb588cf01674bc','robin',true);
// $menu = new WeixinMenu();
// $menu->addButton('按钮1', 'click', 'V1001_TODAY_MUSIC');
// $menu->addButton('按钮2', 'click', 'V1001_TODAY_SINGER');
// $menu->addButton('菜单');
// $menu->addSubButton(2, '谷歌', 'view', 'http://google.com');
// print_r($weixin->createMenu($menu));


$weixin->setCallback('undefined', 'undefinded');
$weixin->listen();

function undefinded($data){
        global $weixin;
        $weixin->responseText('Hello World,接受到一条'.$data->MsgType.'消息');
}
