<?php
include_once dirname(dirname(__FILE__)).'/app.php';
$libUser =new Lib_User();

if($_GET['open_id']){
	$libUser->weixinLogin($_GET['open_id']);
}

$loginUserID = $libUser->getLoginUserID();

//////////////获取所有路线信息
$libRouter = new Lib_Router();
$departures = $libRouter->getAllDeparture();

Template::Show();