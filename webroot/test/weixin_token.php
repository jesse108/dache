<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';
if(checkSignature()){
	echo $_GET['echostr'];
}





function checkSignature()
{
	$signature = $_GET["signature"];
	$timestamp = $_GET["timestamp"];
	$nonce = $_GET["nonce"];

	$token = 'dachequ_aliyun_123';
	$tmpArr = array($token, $timestamp, $nonce);
	sort($tmpArr);
	$tmpStr = implode( $tmpArr );
	$tmpStr = sha1( $tmpStr );

	if( $tmpStr == $signature ){
		return true;
	}else{
		return false;
	}
}