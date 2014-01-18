<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';
$token = 'dachequ_aliyun_123';

$echoStr = $_GET["echostr"];

$validate = WeiXin::checkSignature($token);


if(!$validate){
	exit;
}



if($echoStr){
	echo $echoStr;
}


$msg = WeiXin::getMsg();

dump($msg);