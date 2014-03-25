<?php
/**
 * 出租车公司呼叫记录
 * 
 * @author zhaojian01
 *
 */
class Lib_RouterLog{
	
	public static function Log($order,$companyID){
		$dbRouterLog = new DB_RouterLog();
		
		
		$log = array(
			'order_id' => $order['id'],
			'departure' => 0,
			'destination' => 0,
			'company_id' => $companyID,
			'user_id' => $order['user_id'],
		);
		
		$dbRouterLog->create($log); //global log
		
		$log['departure'] = $order['departure'];
		$log['destination'] = $order['destination'];
		$dbRouterLog->create($log); //route log
	}
}