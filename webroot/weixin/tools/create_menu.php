<?php
/**
 * 创建自定义菜单
 */
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';

$weixinConfig = Config::Get('weixin');
$weixinConfig = $weixinConfig['test2'];

$appID = $weixinConfig['app_id'];
$appSecret = $weixinConfig['app_secret'];


$weixinDache = new Lib_WeiXin_Dache($weixinConfig);
$weixinDache->createMenu();

