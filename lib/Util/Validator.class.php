<?php
class Util_Validator{
	const TYPE_MOBILE = 1;
	
	
	public static $patternArray = array(
		self::TYPE_MOBILE => "/^1(3|5|8)[0-9]{9}$/",
	);
	
	public static function validate($data,$type){
		$validateResult = false;
		
		switch($type){
			case self::TYPE_MOBILE:
				$validateResult = self::validateMobileNumber($data);
				break;
			default:
				
				break;
		}
		return $validateResult;
	}
	
	
	/**
	 * 验证手机号
	 */
	public static function validateMobileNumber($mobile){
		$mobile = trim(strval($mobile));
		$pattern = self::$patternArray[self::TYPE_MOBILE];
		if(preg_match($pattern, $mobile)){
			return true;
		} else {
			return false;
		}
	}
}