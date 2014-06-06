<?php
class Lib_Order_Business{
	const MAX_CALL_NUM = 5; //最大呼叫次数
	public $error;
	private $dbOrder;
	private $dbCompany;
	private $dbOrderTrack;
	private $dbCompanyRoute;
	private $dbRouterLog;
	
	public static $acceptOrderStatus = array(
			DB_Order::STATUS_NORMAL,DB_Order::STATUS_REFUSE,
	); //可接受的订单状态
	
	
	public function __construct(){
		$this->dbOrder = new DB_Order();
		$this->dbCompany = new DB_Company();
		$this->dbOrderTrack = new DB_OrderTrack();
		$this->dbCompanyRoute = new DB_CompanyRoute();
		$this->dbRouterLog = new DB_RouterLog();
	}
	
	/**
	 * 给一家出租车公司打电话   注意 这里不做任何电信
	 */
	public function call($order,$companyID,$callID = ''){
		
		///验证
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
		
		/////////////更新各种状态
		$orderTrack = array(
			'order_id' => $order['id'],
			'company_id' => $companyID,
			'call_id' => $callID,
			'call_time' => time(),
			'status' => DB_OrderTrack::STATTUS_CALLING,
		);
		$trackID = $this->dbOrderTrack->create($orderTrack);
		if(!$trackID){
			$this->error = $this->dbOrderTrack->error;
		}
		
		$orderUpdate = array(
			'call_status' => DB_Order::CALL_STATUS_CALLING,
			'last_call_time' => time(),
		);
		$this->dbOrder->update(array('id'=> $order['id']), $orderUpdate);
		
		$companyUpdate = array(
			'call_status' => DB_Order::CALL_STATUS_CALLING,
		);
		$this->dbCompany->update(array('id' => $companyID), $companyUpdate);
		
		Lib_RouterLog::Log($order, $companyID);
		return $trackID;
	}
	
	/**
	 * 出租车公司接受订单
	 */
	public function accept($orderTrack){
		if(!Util_Array::IsArrayValue($orderTrack)){
			$this->error = "没有此呼叫记录";
			return false;
		}
		
		$order = $this->dbOrder->fetch($orderTrack['order_id']);
		if(!in_array($order['status'], self::$acceptOrderStatus)){
			$this->error = "订单状态不对, order_status:{$order['status']}";
			return false;
		}
		
		$orderUpdate = array(
			'status' => DB_Order::STATUS_ACCEPT,
			'call_status' => DB_Order::CALL_STATUS_NO_CALL,
			'company_id' =>  $orderTrack['company_id'],
		);
		$this->dbOrder->update(array('id' => $order['id']), $orderUpdate);
		
		$trackUpdate = array(
			'status' => DB_OrderTrack::STATTUS_ACCEPT,
			'finish_time' => time(),
		);
		$this->dbOrderTrack->update(array('id'=>$orderTrack['id']), $trackUpdate);
		
		$companyUpdate = array(
			'call_status' => DB_Company::CALL_STATUS_NO_CALL,
		);
		$this->dbCompany->update(array('id' =>$orderTrack['company_id']), $companyUpdate);
		
		return true;
	}
	
	/**
	 * 出租车公司拒绝订单
	 */
	public function refuse($orderTrack){
		if(!Util_Array::IsArrayValue($orderTrack)){
			$this->error = "没有此呼叫记录";
			return false;
		}
		
		$order = $this->dbOrder->fetch($orderTrack['order_id']);
		///////更新呼叫记录状态
		$trackUpdate = array(
				'status' => DB_OrderTrack::STATUS_REFUSE,
				'finish_time' => time(),
		);
		$this->dbOrderTrack->update(array('id'=>$orderTrack['id']), $trackUpdate);
		
		////////////根据情况更新订单状态
		$availableCompanyIDs = $this->getAvailableCompanyIDList($order['departure'], $order['destination'],false); //可用公司
		$libOrder = new Lib_Order();
		$orderTracks = $libOrder->getOrderTrack($order['id']);
		$countTrack = count($orderTracks);
		
		$orderUpdate = array(
				'call_status' => DB_Order::CALL_STATUS_NO_CALL
		);
		if($countTrack >= count($availableCompanyIDs) || $countTrack >= self::MAX_CALL_NUM){
			$orderUpdate['status'] = DB_OrderTrack::STATUS_REFUSE;
		}
		$this->dbOrder->update(array('id'=>$order['id']), $orderUpdate);

		
		$companyUpdate = array(
				'call_status' => DB_Company::CALL_STATUS_NO_CALL,
		);
		$this->dbCompany->update(array('id' =>$orderTrack['company_id']), $companyUpdate);
		
		return true;
	}
	
	/**
	 * 获取订单下个呼叫公司ID
	 */
	public function getOrderNextCompanyID($order){
		if(!$order){
			return false;
		}
		
		if(!in_array($order['status'], self::$acceptOrderStatus)){
			$this->error = "订单状态不对, order_status:{$order['status']}";
			return false;
		}
		
		$nextCompanyID = $this->getNextCompanyID($order['departure'], $order['destination']);
		return $nextCompanyID;
	}
	
	
	///////////////////////下面是根据路线获取打车公司的算法
	
	/**
	 * 获取下一个出租车公司ID
	 * @param int $departure 出发地
	 * @param int $destination 目的地
	 * @return boolean|unknown
	 */
	public function getNextCompanyID($departure,$destination){
		$companyIDList = $this->getAvailableCompanyIDList($departure, $destination); //路线可用公司
		if(!$companyIDList){
			return false;
		}
		
		$num = count($companyIDList);
		$globalRouterLog = $this->getRouterLog($num); //获取最近几次全局呼叫记录
		
		if(Util_Array::IsArrayValue($globalRouterLog)){
			$globalRouterCompanyIDs = Util_Array::GetColumn($globalRouterLog, 'company_id');
		} else {
			$globalRouterCompanyIDs = array();
		}
		
		$lastRouterLog = $this->getRouterLog(1,$departure,$destination);  //获取此路线最近一次呼叫记录
		$lastRouterCompanyID = Util_Array::IsArrayValue($lastRouterLog) ? $lastRouterLog[0]['compay_id'] : 0;
		
		$companyIDList = self::ArrayMove($companyIDList, $lastRouterCompanyID);//得到一个呼叫公司ID列表
		
		foreach ($companyIDList as $curCompanyID){
			if(!in_array($curCompanyID, $globalRouterCompanyIDs)){
				$companyID = $curCompanyID;
				break;
			}
		}
		$companyID = $companyID ? $companyID : $companyIDList[0];
		return $companyID;
	}
	
	/**
	 * 获取指定路线的呼叫记录
	 * 
	 * @param unknown $num
	 * @param number $departure
	 * @param number $destination
	 * @return Ambigous <boolean, multitype:, multitype:multitype: >
	 */
	public function getRouterLog($num,$departure = 0,$destination = 0){
		$condition = array(
			'departure' => $departure,
			'destination' => $destination,
		);
		$option = array(
			'select' => 'company_id',
			'size' => $num,
			'order' => 'order by id desc',
		);
		
		$logs = $this->dbRouterLog->get($condition,$option);
		return $logs;
	}
	
	
	/**
	 * 获取指定路线可用的公司列表
	 * 需要加入判断   目前正在通话的公司是否需要加入列表 ? todo
	 * 
	 * @param unknown $departure
	 * @param unknown $destinition
	 * @param $free  是否空闲
	 */
	public function getAvailableCompanyIDList($departure,$destination,$free = true){
		$condition = array(
			'departure' => $departure,
			'destination' => $destination,
			'status' => DB_CompanyRoute::STATUS_NORMAL,
		);
		
		$option =array('select' => 'company_id');
		$companyIDList = $this->dbCompanyRoute->get($condition);
		$companyIDList = Util_Array::GetColumn($companyIDList, 'company_id');
		
		if($free){
			$condition = array(
				'id' => $companyIDList,
				'call_status' => DB_Company::CALL_STATUS_NO_CALL,
			);
			$option = array('select' => 'id');
			$companys = $this->dbCompany->get($condition,$option);
			if($companys){
				$companyIDList = Util_Array::GetColumn($companys, 'id');
			} else {
				$companyIDList = array();
			}
		}
		
		return $companyIDList;
	}
	
	
	///////////////Tools 
	public static function ArrayMove($array,$needle){
		$count = 0;
		foreach ($array as $one){
			$count++;
			if($needle == $one){
				break;
			}
		}
		$arrayStart = array_slice($array, $count);
		$arrayEnd = array_slice($array, 0,$count);
		$result = array_merge($arrayStart,$arrayEnd);
		return $result;
	}
	
}