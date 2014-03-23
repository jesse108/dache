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
		
		if($orderInfo['user_id']){
			$order['user_id'] = $orderInfo['user_id'];
		}
		
		if($orderInfo['departure']){
			$order['departure'] = $orderInfo['departure'];
		}
		
		if($orderInfo['destination']){
			$order['destination'] = $orderInfo['destination'];
		}
		
		if($orderInfo['time']){
			$order['time'] = $orderInfo['time'];
		}
		
		if($orderInfo['num']){
			$order['num'] = $orderInfo['num'];
		}
		
		if($orderInfo['contact_mobile']){
			$order['contact_mobile'] = $orderInfo['contact_mobile'];
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
			if($time <= $currentTime + $duration){
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
			self::$error = "出发地不能为空";
			return false;
		}
		
		if(!$order['destination']){
			self::$error  = "目的地不能为空";
			return false;
		}
		
		if(!$order['num']){
			self::$error  = "至少要有1人";
			return false;
		}
		
		if(!$order['time']){
			self::$error = "请选择出发时间";
			return false;
		}
		
		if(!$order['contact_mobile']){
			self::$error = "手机号不能为空 ";
			return false;
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