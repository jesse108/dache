<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';

$GLOBALS["HTTP_RAW_POST_DATA"] = 
'<xml><ToUserName><![CDATA[gh_71b329fe56cd]]></ToUserName>
<FromUserName><![CDATA[oAPZ_jnlOdsbVpkcGqCW5Ixn5IPo]]></FromUserName>
<CreateTime>1390144036</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[打车]]></Content>
<MsgId>5970623171449450676</MsgId>
</xml>';

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
