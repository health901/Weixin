<?php

//引入库
require_once('Weixin.php');
//创建SDK实例
$weixin = Weixin::init(
                'vrobin', #Token
                'wx047be627bb7116c3', #AppID
                '392f899f7e6c57c0ffeb588cf01674bc', #AppSecret
                true    #是否服务号
);

/**
 * 创建自定义菜单菜单
 */
$menu = new WeixinMenu();
$menu->addButton('按钮1', 'click', 'EVENT_CLICK_1');
$menu->addButton('按钮2', 'click', 'EVENT_CLICK_2');
$menu->addButton('菜单');
$menu->addSubButton(1, '按钮1-1', 'click', 'EVENT_CLICK_1_1');
$menu->addSubButton(3, '谷歌', 'view', 'http://google.com');
print_r($menu->toArray());
$weixin->createMenu($menu);
print_r($weixin->getMenu());

/**
 * 获取关注用户列表
 */
print_r($weixin->getUserList());
