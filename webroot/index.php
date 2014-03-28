<?php
include_once dirname(dirname(__FILE__)).'/app.php';



$cloopenObj = new Lib_Cloopen();
$result = $cloopenObj->callBack('15210954985', '13241638952');
dump($result);

$subAccounts = $cloopenObj->getSubAccounts();
dump($subAccounts);

exit;
Template::Show();