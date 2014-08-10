<?php

//引入库
require_once('Weixin.php');
//使用init方法创建SDK实例
Weixin::init('vrobin');
/**
 * 监听用户消息
 * 用DemoClass类的subscribe方法处理 事件-订阅
 * 用DemoClass类的otherEvent方法处理其余事件
 * 用匿名函数处理location消息
 * 用catchAll函数处理其余消息
 */
$class = new DemoClass();
$location = function($data) {
    //使用instance方法获取已经创建好的weixin实例
    Weixin::instance()->responseText('Hello World,这是一条位置消息,你的位置为' . $data->Label);
};
Weixin::instance()->setCallback(Weixin::TYPE_UNDEFINED, 'catchAll')
        ->setCallback(array(Weixin::TYPE_EVENT, Weixin::EVENT_SUBSCRIBE), array($class, 'subscribe'))
        ->setCallback(Weixin::TYPE_EVENT, array($class, 'otherEvent'))
        ->setCallback(Weixin::TYPE_LOCATION, $location)
        ->listen();

function catchAll(WeixinResult $data)
{
    $weixin = Weixin::instance();
    $weixin->responseText('Hello World,接受到一条' . $data->MsgType . '消息');
}

class DemoClass
{

    function __construct()
    {
        $this->weixin = Weixin::instance();
    }

    function subscribe(WeixinResult $data)
    {
        $this->weixin->responseText('欢迎订阅,该测试号基于Weixin SDK开发,(c) Viking Robin');
    }

    function otherEvent(WeixinResult $data)
    {
        $this->weixin->responseText('其他事件:' . $data->Event);
    }

}
