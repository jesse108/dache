<?php
class Lib_Order_Manage{
	public static $maxCallOrderNum = 100;   //同时最多电话数
	
	public static function CallOrders(){
		$dbOrder = new DB_Order();
		$condition = array(
			'status' => DB_Order::STATUS_NORMAL,
			'call_status' => DB_Order::CALL_STATUS_NO_CALL,
		);
		
		$option = array(
			'size' => self::$maxCallOrderNum,
		);
		$orders = $dbOrder->get($condition,$option);
		
		if(Util_Array::IsArrayValue($orders)){
			foreach ($orders as $order){
				self::CallOneOrder($order);
			}
		}
	}
	
	
	/**
	 * 一个订单发起呼叫请求
	 * @param unknown $order
	 * @return unknown|boolean
	 */
	public static function CallOneOrder($order){
		$libOrderBusiness = new Lib_Order_Business();
		$companyID = $libOrderBusiness->getOrderNextCompanyID($order); //获取呼叫公司ID
		if($companyID){
			$company = Lib_Company::Fetch($companyID);
			$phone = $company['phone'];
			$cloopenObj = new Lib_Cloopen();
			$callResult = $cloopenObj->ivrDial($phone); //发起呼叫
			if(Util_Array::IsArrayValue($callResult) && $callResult['callSid']){
				$callID = $callResult['callSid'];
				$trackID = $libOrderBusiness->call($order, $companyID,$callID);
				return $callID;
			}
		}
		return false;
	}
	
	
	public static function RefuseTimeoutOrders(){
		$maxCallTime = 120; //最大呼叫时间两分钟
		$time = time();
		$time = $time - $maxCallTime;
		
		$dbOrderTrack= new DB_OrderTrack();
		$condition  = array(
			"call_time < {$time}",
			'status' => DB_OrderTrack::STATTUS_CALLING, 
		);
		$orderTracks = $dbOrderTrack->get($condition);
		if($orderTracks){
			$libOrderBusiness = new Lib_Order_Business();
			foreach ($orderTracks as $orderTrack){
				$libOrderBusiness->refuse($orderTrack);	
			}
		}
	}
}