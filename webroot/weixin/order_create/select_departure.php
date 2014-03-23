<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';
$libUser =new Lib_User();
$loginUserID = $libUser->getLoginUserID();


//////////////获取所有路线信息
$libRouter = new Lib_Router();
$departures = $libRouter->getAllDeparture();

Template::Show();