<?php
class Utility{
	public static $numberMap = array(
		0 => '零',1 => '幺',2=>'二',3=>'三',4=>'四',5=> '五',6=>'六',7=>'七' ,8=> '八',9=>'九',
	);
	
    const CHAR_MIX = 0;
    const CHAR_NUM = 1;
    const CHAR_WORD = 2;
	
    /**
     * 生成随机字符串
     * @param number $len 长度
     * @param number $type 生成字符类型 
     * @return string 随机字串
     */
	public static function GenRandomStr($len = 6,$type = self::CHAR_MIX){
		$random = '';
		for ($i = 0; $i < $len;  $i++) {
			$random .= self::_GenRandomChar($type,$i);
		}
		return $random;
	}
	
	public static function getRequestURI(){
		$server = $_SERVER;
		return $server['REQUEST_URI'];
	}
	
	//////////辅助函数
	private static function _GenRandomChar($type = self::CHAR_MIX,$index = 0){
		$random = '';
		switch ($type){
			case self::CHAR_NUM:
				if($index == 0){
					$random = chr(rand(49, 57));
				} else {
					$random = chr(rand(48, 57));
				}
				break;
			case self::CHAR_WORD:
				$key  = rand(0, 1);
				$random = $key ? chr(rand(65, 90)) : chr(rand(97, 122));
				break;
			case self::CHAR_MIX:
				$key  = rand(0, 2);
				if($key == 0){
					if($index == 0){
						$random = chr(rand(49, 57));
					} else {
						$random = chr(rand(48, 57));
					}
				} else if($key == 1){
					$random = chr(rand(65, 90));
				} else {
					$random = chr(rand(97, 122));
				}
				break;
		}
		return $random;
	}
	
	
	public static function getUserIP($defaultIP = null){ //获取用户IP todo
		
		if(isset($_SERVER['HTTP_CLIENTIP'])){
			$userIP = $_SERVER['HTTP_CLIENTIP'];
		} else if(isset($_SERVER['REMOTE_ADDR'])){
			$userIP = $_SERVER['REMOTE_ADDR'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
			$intPos = strrpos($userIP, ',');
			if($intPos > 0){
				$userIP = substr($userIP, $intPos+1);
			}
		} else if(isset($_SERVER['HTTP_CLIENT_IP'])){
			$userIP = $_SERVER['HTTP_CLIENT_IP'];
		}
		$userIP = strip_tags($userIP);
		$userIP = trim($userIP);
		
		if(!$userIP && $defaultIP){
			$userIP = $defaultIP;
		}
		
		return $userIP;
	}
	
	/**
	 * 页面跳转函数
	 *
	 * 这里使用修改头文件的方式现实页面跳转
	 * 这里要注意的是调用这个函数之前 页面不能有任何输出 否则跳转失败
	 * 跳转后会退出程序
	 *
	 * @param string $u 跳转页面
	 */
	public static function Redirect($u=null) {
		if (!$u) $u = $_SERVER['HTTP_REFERER'];
		if (!$u) $u = '/';
		Header("Location: {$u}");
		exit;
	}
	
	/**
	 * 阿拉伯数字转化成中文数字
	 */
	public static function TransNumberToCN($number){
		$number = strval($number);
		
		$temp = '';
		for($i = 0 ; $i< strlen($number); $i ++){
			$curNumStr = self::$numberMap[$number[$i]];
			if($curNumStr){
				$temp .= $curNumStr;
			}
		}
		
		return $temp;
	}
}