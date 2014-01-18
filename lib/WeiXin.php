<?php
class WeiXin{
	
	public static function getMsg()
	{
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	
		return $postStr;
	}
	
	public static function checkSignature($token)
	{
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];

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
}