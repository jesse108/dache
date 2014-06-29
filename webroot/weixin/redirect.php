<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';
$libUser =new Lib_User();
$openID = $_GET['open_id'];
$url = $_GET['url'];

$libUser->weixinLogin($_GET['open_id']);

Utility::Redirect($url);