<?php
class Lib_Order{
	const ORDER_TRACK_STATUS_CREATE = 0;
	const ORDER_TRACK_STATUS_SUCCESS = 1;
	const ORDER_TRACK_STATUS_FAIL = 2;

	public $dbOrder;
	public $dbOrderTrack;
	public $error;
	
	public function __construct(){
		$this->dbOrder = new DB_Order();
		$this->dbOrderTrack = new DB_OrderTrack();
	}
	
	/**
	 * 提交订单
	 */
	public function submit($orderInfo){
		$order = array(
			'user_id' => $orderInfo['user_id'] ? $orderInfo['user_id'] : 0,
			'departure' => $orderInfo['departure'],
			'destination' => $orderInfo['destination'],
			'status' => DB_Order::STATUS_NORMAL,
			'time' => $orderInfo['time'],
			'num' => $orderInfo['num'],
			'contact_mobile' => $orderInfo['contact_mobile'],
			'contact_username' => $orderInfo['contact_username'] ? $orderInfo['contact_username'] : '',
		);
		
		$validateInfo = $this->validateOrder($order);
		if(!$validateInfo['pass']){
			$this->error = $validateInfo['message'];
			return false;
		}
		
		$orderID = $this->dbOrder->create($order);
		if(!$orderID){
			$this->error = $this->dbOrder->error;
			return false;
		}
		return $orderID;
	}
	
	public function validateOrder($order,$type = VALIDATE_TYPE_CREATE){
		$message = '';
		
		if($type == VALIDATE_TYPE_CREATE){
			if(!$order['departure']){
				$message .= "请选择选出发地,";
			}
			if(!$order['destination']){
				$message .= '请选择目的地,';
			}
			if(!$order['time']){
				$message .= '请选择出发时间,';
			}
			
			if(!$order['contact_mobile']){
				$message .= '请选择联系电话,';
			}
			$message = trim($message,',');
		}
		
		if($message){
			$pass = false;
		} else {
			$pass = true;
		}
		
		$result = array(
			'pass' => $pass,
			'message' => $message,
		);
		return $result;
	}
	
	/**
	 * 上车
	 * @param array $order
	 */
	public function getOnBus($order){
		if($order['status'] !=  DB_Order::STATUS_ACCEPT){
			return false;
		}
		$updateRow = array(
			'status' => DB_Order::STATUS_ACCEPT_ON,
		);
		$this->dbOrder->update(array('id' => $order['id']), $updateRow);
		return true;
	}
	
	/**
	 * 获取订单信息
	 * 
	 * @param int $orderID
	 * @return array
	 */
	public function getOrderInfo($orderID){
		$order = $this->dbOrder->fetch($orderID);
		
		if(!$order){
			$this->error = $this->dbOrder->error;
			return false;
		}
		
		return $order;
	}
	
	/**
	 * 获取订单 跟踪信息
	 * 
	 * @param int $orderID
	 * @return array
	 */
	public function getOrderTrack($orderID){
		$condition = array('order_id' => $orderID);
		$option = array('order' => 'order by id');
		$orderTracks  = $this->dbOrderTrack->get($condition,$option);
		
		return $orderTracks;
	}
	
	/**
	 * 获取用户今天订单情况
	 * 
	 * $p
	 * @param number $currentTime 当前时间
	 * @return Ambigous <boolean, multitype:, multitype:multitype: >
	 */
	public function getTodayOrder($userID , $currentTime = 0){
		$currentTime = $currentTime ? $currentTime : time();
		$currentDate = date('Y-m-d',$currentTime);
		
		$startTime = strtotime($currentDate);
		$endTime = $startTime + 86400 - 1;
		
		$delStauts = DB_Order::STATUS_DEL;
		$condition = array(
			"time >= {$startTime}",
			"time <= {$endTime}",
			"status <> {$delStauts}",
			"user_id"  => $userID,
		);
		$orders = $this->dbOrder->get($condition);
		return $orders;
	}
	
	/**
	 * 获取订单列表
	 * @param int $userID 用户ID
	 * @param number $start 
	 * @param number $num
	 * @return array 订单列表
	 */
	public function getOrderList($userID,$start = 0,$num = 0){
		$delStauts = DB_Order::STATUS_DEL;
		$condition = array(
				"status <> {$delStauts}",
				"user_id"  => $userID,
		);
		$option = array(
			'order' => 'order by create_time desc',
		);
		if($num){
			$option['offset'] = $start;
			$option['size'] = $num;
		}
		$orders = $this->dbOrder->get($condition,$option);
		return $orders;
	}
	

	/**
	 * 获取可读的订单信息
	 * @param array $order
	 */
	public static function  GetReadableOrder($order,$fullPlaceName = true){
		$locationDeparture = Lib_Location::Fetch($order['departure']);
		$parentDeparture = Lib_Location::Fetch($locationDeparture['parent_id']);
		$showDeparture = "{$locationDeparture['name']}";
		
		$locationDestination = Lib_Location::Fetch($order['destination']);
		$parentDestination = Lib_Location::Fetch($locationDestination['parent_id']);
		$showDestination = "{$locationDestination['name']}";
		
		
		if($fullPlaceName){
			$showDeparture = "{$parentDeparture['name']}".Lib_Location::getLevelShowStr($parentDeparture['level'])."{$showDeparture}";
			$showDestination = "{$parentDestination['name']}".Lib_Location::getLevelShowStr($parentDestination['level']). " {$showDestination}";
		}
		
		$showTime = Util_Time::getManReadTime($order['time']);
		
		$status = intval($order['status']);
		$showStatus = DB_Order::$staticArray[$status]['title'];
		
		switch ($status){
			
			case DB_Order::STATUS_ACCEPT_ON;
				$process = 1;
				$processShow = "已完成";
				break;
			case DB_Order::STATUS_DEL:
				$process = 2;
				$processShow = "已删除";
				break;
			case DB_Order::STATUS_REFUSE;
				$process = 2;
				$processShow = "未成功";
				break;
			case DB_Order::STATUS_NORMAL:
			case DB_Order::STATUS_ACCEPT:
			default:
				$process = 0;
				$processShow = '进行中';
				break;
		}
		
		$orderShow = array(
			'departure' => $showDeparture,
			'destination' => $showDestination,
			'show_time' => $showTime,
			'status_title' => $showStatus,
			'create_time' => date('Y-m-d H:i:s',$order['create_time']),
			'process' => $process,
			'process_title' => $processShow,
		);
		return $orderShow;
	}

	
	public static function getReadableOrderTrack($orderTrack){
		$createTime = date('Y-m-d H:i:s',$orderTrack['create_time']);
		$finishTime = date('Y-m-d H:i:s',$orderTrack['finish_time']);
		
		switch ($orderTrack['status']){
			case DB_OrderTrack::STATTUS_CALLING:
				$statusShow = '等待接单,呼叫中..';
				break;
			case DB_OrderTrack::STATUS_REFUSE:
				$statusShow = '未接受';
				break;
			case DB_OrderTrack::STATTUS_ACCEPT:
				$statusShow = '已接单';
				break;
			default:
				$statusShow = '未知状态';
				break;
		}
		$orderTrackInfo = array(
			'create_time' => $createTime,
			'finish_time' => $finishTime,
			'status_show' => $statusShow,
		);
		return $orderTrackInfo;
	}
	
	
}