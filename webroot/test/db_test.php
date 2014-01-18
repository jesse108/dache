<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';
DB::Debug();

$condition =array(
	'id' => 2,
);

$updateRow = array(
	'varchar_test' => 'Jesse',
	'int_test' => 5,
);

$result = DB::Update('test', $condition, $updateRow);
dump($result);


if(!$result){
	dump(DB::$error);
}