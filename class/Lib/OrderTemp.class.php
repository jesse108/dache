<?php
/**
 * 记录用户临时创建的订单
 * 
 * 这里使用Session 来保存
 * @author zhaojian01
 *
 */
class Lib_OrderTemp{
	public static $error = '';
	
	public function getOrder(){
		$order = Session::Get(self::getOrderKey());
		$order = Util_Array::IsArrayValue($order) ? $order : array();
		return $order;
	}
	
	public function clearOrder(){
		Session::Del(self::getOrderKey());
	}
	
	/**
	 * 填充订单
	 * @param unknown $orderInfo
	 */
	public function setOrder($orderInfo){
		$order = $this->getOrder();
		
		foreach ($orderInfo as $index => $value){
			$order[$index] = $orderInfo[$index];	
		}
		
		Session::Set(self::getOrderKey(), $order);
		return true;
	}
	
	
	
	public static function getSelectTimeInfo($currentTime = 0){
		$currentTime = $currentTime ? $currentTime : time();
		$currentDate = date('Y-m-d' ,$currentTime);
		
		$startTime = strtotime($currentDate);
		$endTime = $startTime + 86400 * 2 - 1;
		
		$duration = 30* 60;//30 分钟
		
		$showDateArray = array();
		$showDateArray[] = array(
			'title' => '现在出发',
			'time' => $currentTime,
		);
		
		for($time = $startTime;$time < $endTime; $time += $duration){
			if($time <= $currentTime + 600){  //10分钟
				continue;
			}
			
			
			$date = date('Y-m-d',$time);
			$hour = date('H',$time);
			$minite = date('i',$time);
			
			if($hour < 6 || $hour > 22){
				continue;
			}
			
			$showDateArray[] = array(
				'title' => Util_Time::getManReadTime($time),
				'time' => $time,
			);
		}
		
		return $showDateArray;
	}
	
	
	public static function CheckOrder($order){
		if(!$order['departure']){
			self::$error = "请选择出发地，不然我们不知道您从哪里出发";
			return false;
		}
		
		if(!$order['destination']){
			self::$error  = "请选择目的地，不然我们不知道您要去哪里";
			return false;
		}
		
		if(!$order['num']){
			self::$error  = "至少要有1人";
			return false;
		}
		
		if(!$order['time']){
			self::$error = "请选择出发时间,不然我们不知道你什么时候出发";
			return false;
		}
		
		if(!$order['contact_mobile']){
			self::$error = "需要填写手机号，我们才能联系上您";
			return false;
		}
		
		if(!Util_Validator::validate($order['contact_mobile'], Util_Validator::TYPE_MOBILE)){
			self::$error = "需要填写正确的手机号，我们才能联系上您";
		}
		return true;
	}
	
	private static function getOrderKey(){
		$key = SESSION_ORDER_TEMP;
		$libUser =new Lib_User();
		$loginUserID = $libUser->getLoginUserID();
		$loginUserID = $loginUserID ? $loginUserID : 0;
		$key = $key . "_{$loginUserID}";
		return $key;
	}
}