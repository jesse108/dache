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
			'user_id' => $orderInfo['user_id'],
			'departure' => $orderInfo['departure'],
			'destinnation' => $orderInfo['destination'],
			'status' => DB_Order::STATUS_NORMAL,
			'time' => $orderInfo['time'],
			'num' => $orderInfo['num'],
			'contact_mobile' => $orderInfo['contact_mobile'],
			'contact_user' => $orderInfo['contact_user'] ? $orderInfo['contact_user'] : '',
		);
		
		$validateInfo = $this->validateOrder($order);
		if(!$validateInfo['pass']){
			$this->error = $validateInfo['message'];
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
			if(!$order['destinnation']){
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
	 * 获取订单信息
	 * 
	 * @param int $orderID
	 * @return array
	 */
	public function getOrderInfo($orderID){
		$order = $this->dbOrder->fetch($orderID);
		
		if(!$order){
			$this->error = $this->dbOrder->error;
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
	

}