<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';
$token = 'dachequ_aliyun_123';
$handler = new Lib_WeiXin_RequestHandler();

$weixinManager = new WeiXin_RequestManage($handler, $token);

$result = $weixinManager->handleRquest();
echo $result;