<?php
include_once dirname(dirname(__FILE__)).'/app.php';



$cloopenObj = new Lib_Cloopen();
$to = '15210954985';
$respUrl = "http://www.dachequ.com:80/cloopen.php";

//$result = $cloopenObj->landingCalls($to,'测试啊','','15210999',3,$respUrl);
//dump($result);

//$subAccounts = $cloopenObj->getSubAccounts();
//dump($subAccounts);

exit;
Template::Show();