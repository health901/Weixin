<?php

require_once('subscription/Weixin.php');
$weixin = new Weixin('123');
$weixin->setCallback('image', function($data) {
	    global $weixin;
	    $weixin->responseText('xxxxx');
	});
$weixin->listen();

//$tmpArr = array('123', '12345678', '321');
//sort($tmpArr);
//$tmpStr = implode($tmpArr);
//$tmpStr = sha1($tmpStr);
//echo $tmpStr;