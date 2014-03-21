<?php
/**
 * 微信处理类
 * 
 * @author jesse_108@163.com  zhajian
 */
class WeiXin_RequestManage{
	public $handler = null;
	public $token = '';
	public $postData = '';
	public $postArray = array();
	
	const DEFAULT_OUTPUT = ' ';//默认输出 空格
	
	public function __construct($handler,$token){
		$this->handler = $handler;
		$this->token = $token;
	}
	
	public function handleRquest(){
		if(!$this->checkSignature()){
			return false;
		}
		/////////验证  开通开发者请求
		if($_GET['echostr']){
			return $_GET['echostr'];
		}
		//////
		
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"]; //获取raw post数据
		if(!$postStr){
			$result =  ' ';
		} else {
			$this->postData = $postStr;
			
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$postArray = Util_Array::ObjectToArray($postObj);
			$this->postArray = $postArray;
			
			$msgType = $postArray['MsgType'];
			
			$result = $this->handler->handleRquest($msgType,$postArray);
		}

		Log::Set($result);
		if(!$result){
			return self::DEFAULT_OUTPUT;
		}
		
		$result = self::formatResult($result);
		return $result;
	}
	
	
	/**
	 * 检查请求是否合法
	 * @return boolean
	 */
	private  function checkSignature()
	{
		$token = $this->token;
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
	
	/**
	 * 结构化返回数据
	 * @param unknown $result
	 */
	public static function formatResult($result){
		if(!is_array($result)){
			return $result;
		}
		
		$formatStr = self::formatArray($result);
		$formatStr = "<xml>{$formatStr}</xml>";
		
		return $formatStr;
	}
	
	public static  function formatArray($array){
		$str = '';
		foreach ($array as $key => $value){
			$value = strval($value);
			if(htmlspecialchars($value) != $value){
				$str .= "<{$key}><![CDATA[{$value}]]></{$key}>";
			} else {
				$str .= "<{$key}>{$value}</{$key}>";
			}
			
		}
		return $str;
	}
}