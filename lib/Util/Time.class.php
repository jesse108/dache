<?php
class Util_Time{
	const TIMER_PRECISION_MSEC = 1; //毫秒
	const TIMER_PRECISION_USEC = 2; //微秒
	const TIMER_PRECISION_SEC = 3;  //秒
	
	private static $timerList = array(); //计时用数组
	
	//获取当前的毫秒数  单位毫秒
	public static function GetMilliTime(){
		return floatval(self::GetMicroTime()/1000);
	}
	
	//获取当前的微秒数 单位微秒
	public static function GetMicroTime(){
		list($usec,$sec) = explode(' ', microtime());
		$usec = floatval($usec) * 1000000;
		$sec = floatval($sec) * 1000000;

		$uTime = $usec + $sec;
		return floatval($uTime);
	}
	
	
	
	//////////////计时开始
	public static function TimerStart($key = 'default'){
		$key = md5($key);
		self::$timerList[$key] = self::GetMicroTime();
	}
	
	///////计时结束
	public static function TimerStop($key = 'default',$precision = self::TIMER_PRECISION_MSEC){
		$key = md5($key);
		$startTime = self::$timerList[$key];
		if(!isset($startTime)){
			return 0;
		}
		$endTime = self::GetMicroTime();
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
	
	
	///////
	public static function getManReadTime($time,$currentTime = 0){
		$currentTime = $currentTime ? $currentTime : time();
		if(abs($time-$currentTime) <= 60){
			return "现在";
		}
		
		$currentDate = date('Y-m-d',$currentTime);
		$currentDateTime = strtotime($currentDate);
		
		$date = date('Y-m-d',$time);
		$dateTime = strtotime($date);
		
		$dateDiff = $dateTime - $currentDateTime;
		if($dateDiff >=0  && $dateDiff < 86400){
			$showDay = "今天";
		} else if($dateDiff >= 86400  && $dateDiff < 2*86400){
			$showDay = "明天";
		} else if($dateDiff > -86400  && $dateDiff < 0){
			$showDay = "昨天";
		} else {
			$showDay = $date;
		}
		
		$hour = date("H",$time);
		$hour = intval($hour);
		if($hour < 7){
			$halfDay = "早上";
		} else if($hour >=7 && $hour < 11){
			$halfDay = "上午";
		} else if($hour >= 11 && $hour < 2){
			$halfDay = "中午";
		} else if($hour >=2 && $hour < 18){
			$halfDay = "下午";
		} else {
			$halfDay = "晚上";
		}
		
		$miniute = date("i",$time);
		
		if(intval($miniute) == 0){
			$showMiniute = "";
		} else if(intval($miniute) == 15){
			$showMiniute = "一刻";
		} else if(intval($miniute) == 30){
			$showMiniute = "半";
		} else {
			$showMiniute = $miniute .'分';
		}
		
		$showTime = "{$showDay}{$halfDay}{$hour}点{$showMiniute}";
		return $showTime;
	}
	
}