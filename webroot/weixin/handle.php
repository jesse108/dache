<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';


$config = Config::Get('weixin');
$token = $config['default']['token'];
$handler = new Lib_WeiXin_RequestHandler();
$weixinManager = new WeiXin_RequestManage($handler, $token);
$result = $weixinManager->handleRquest();

if($result){
	echo $result;
} else {
	echo ' ';
}
