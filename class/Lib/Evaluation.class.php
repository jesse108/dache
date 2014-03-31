<?php
class Lib_Evaluation{
	
	public static function Fetch($orderID){
		$dbEvaluation = new DB_OrderEvaluation();

		$evaluations = $dbEvaluation->fetch($orderID,'order_id');
		if(!Util_Array::IsArrayValue($evaluations)){
			return false;
		}
		
		if(is_array($orderID)){
			$evaluations = Util_Array::AssColumn($evaluations, 'order_id');
		}
		
		return $evaluations;
	}
	
	public static function getDefaultEvaluation(){
		$evaluation = array(
			'service_mark' => 1,
			'time_mark' => 1,
			'commnet' => '',
		);
		return $evaluation;
	}
	
	public static function Evaluate($order,$orderEvaluation){
		if($order['status'] != DB_Order::STATUS_ACCEPT_ON){
			return false;
		}
		
		$evaluation = array(
			'order_id' => $order['id'],
			'service_mark' =>$orderEvaluation['service_mark'],
			'time_mark' => $orderEvaluation['time_mark'],
			'comment' => $orderEvaluation['comment'],
			'company_id' => $order['company_id'],
			'departure' => $order['departure'],
			'destination' => $order['destination'],
		);
		
		$dbEvalution = new DB_OrderEvaluation();
		$dbEvalution->create($evaluation);
		return true;
	}
}