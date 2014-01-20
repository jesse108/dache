<?php
class Utility{
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
		
	}
}