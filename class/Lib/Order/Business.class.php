<?php
class Lib_Order_Business{
	public $error;
	private $dbOrder;
	private $dbCompany;
	private $dbOrderTrack;
	
	public static $acceptOrderStatus = array(
			DB_Order::STATUS_NORMAL,DB_Order::STATUS_REFUSE,
	); //可接受的订单状态
	
	
	public function __construct(){
		$this->dbOrder = new DB_Order();
		$this->dbCompany = new DB_Company();
		$this->dbOrderTrack = new DB_OrderTrack();
	}
	
	/**
	 * 给一家出租车公司打电话
	 */
	public function call($orderID,$companyID){
		$order = $this->dbOrder->fetch($orderID);
		if(!Util_Array::IsArrayValue($order)){
			$this->error = "订单ID不对";
			return false;
		}
		
		if(!in_array($order['status'], self::$acceptOrderStatus)){
			$this->error = "订单状态不对, order_status:{$order['status']}";
			return false;
		}
		
		$company = $this->dbCompany->fetch($companyID);
		if(!Util_Array::IsArrayValue($company)){
			$this->error = "汽车公司ID不对";
			return false;
		}
		
		if($company['status'] == DB_Company::STATUS_DEL){
			$this->error = "汽车公司状态不对:{$company['status']}";
			return false;
		}
		
		$orderTrack = array(
			'order_id' => $orderID,
			'company_id' => $companyID,
			'status' => DB_OrderTrack::STATTUS_CALLING,
		);
		$trackID = $this->dbOrderTrack->create($orderTrack);
		if(!$trackID){
			$this->error = $this->dbOrderTrack->error;
		}
		return $trackID;
	}
	
	/**
	 * 出租车公司接受订单
	 */
	public function accept($orderID,$companyID){
		
	}
	
	/**
	 * 出租车公司拒绝订单
	 */
	public function refuse($orderID,$companyID){
		
	}
	
	
}