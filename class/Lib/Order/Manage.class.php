<?php
class Lib_Order_Manage{
	public static $maxCallOrderNum = 100;
	
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
	
	
	
	public static function CallOneOrder($order){
		$libOrderBusiness = new Lib_Order_Business();
		$companyID = $libOrderBusiness->getOrderNextCompanyID($order);
		if($companyID){
			//$trackID = $libOrderBusiness->call($order, $companyID);
			
			////////////接下来是电话公司逻辑
		}
	}
}