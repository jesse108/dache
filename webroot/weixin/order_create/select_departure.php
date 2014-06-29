<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';
$libUser =new Lib_User();
$loginUserID = $libUser->getLoginUserID();


//////////////获取所有路线信息
$parentID = intval($_GET['parent_id']);

$libRouter = new Lib_Router();
$departures = $libRouter->getAllDeparture($parentID);
//////获取之前打车过的路线
$pastRoutes = $libUser->getLastUserRoute($loginUserID);

Template::Show();