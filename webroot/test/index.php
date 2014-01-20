<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';
DB::Debug();
$openID = 'jesse_test_01';
$libUser = new Lib_User();
$result = $libUser->bindWeiXinUser($openID);


dump($result);