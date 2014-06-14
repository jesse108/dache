<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';


$cloopen = new Lib_Cloopen();

$result = $cloopen->ivrDial('15210954985');
dump($result);
