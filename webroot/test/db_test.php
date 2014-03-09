<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';
DB::Debug();


$inserUser = array(
	'id' => 1,
	'name' => 'jesse',
	'sex' => '1',
);

$insertID = DB::Insert('user', $inserUser);

$delContion = array(
	'id' => 1,
);

$condition = array(
	'id' => 1,
);

$option = array(
	'order' => 'order bu id desc',
);
$result = DB::LimitQuery('user',$condition);