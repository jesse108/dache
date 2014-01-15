<?php
class Util_Time{
	const TIMER_PRECISION_MSEC = 1; //毫秒
	const TIMER_PRECISION_USEC = 2; //微秒
	const TIMER_PRECISION_SEC = 3;  //秒
	
	private static $timerList = array(); //计时用数组
	
	//获取当前的毫秒数  单位毫秒
	public static function getMilliTime(){
		return floatval(self::getMicroTime()/1000);
	}
	
	//获取当前的微秒数 单位微秒
	public static function getMicroTime(){
		list($usec,$sec) = explode(' ', microtime());
		$usec = floatval($usec) * 1000000;
		$sec = floatval($sec) * 1000000;

		$uTime = $usec + $sec;
		return floatval($uTime);
	}
	
	
	
	//////////////计时开始
	public static function timerStart($key = 'default'){
		$key = md5($key);
		self::$timerList[$key] = self::getMicroTime();
	}
	
	///////计时结束
	public static function timerStop($key = 'default',$precision = self::TIMER_PRECISION_MSEC){
		$key = md5($key);
		$startTime = self::$timerList[$key];
		if(!isset($startTime)){
			return 0;
		}
		$endTime = self::getMicroTime();
		$duration = $endTime - $startTime;
		switch ($precision){
			case self::TIMER_PRECISION_SEC://秒
				$duration = $duration / 1000000;
				break;
			case self::TIMER_PRECISION_MSEC://毫秒
				$duration = $duration / 1000;
				break;
			case self::TIMER_PRECISION_USEC:
				break;
		}
		return $duration;
	}
}