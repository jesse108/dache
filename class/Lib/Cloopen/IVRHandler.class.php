<?php
class Lib_Cloopen_IVRHandler{
	const DIRECTION_CALL_IN = 0;//呼入
	const DIRECTION_CALL_OUT = 1; //呼出
	
	
	public static function HandleRequest(){
		$data = $_REQUEST;
		$action = $data['ivr_action'];
		switch ($action){
			case 'startservice':
				$result = self::StartService($data);
				break;
			case 'stopservice':
				$result = self::StopService($data);
				break;
			case 'dtmfreport':
				//按键事件
				break;
			case 'status1':
				$result = self::Status1($data);
				break;
			case 'status2':
				$result = self::Status2($data);
				break;
		}
		if($result){
			$result = self::BuildResponse($result);
			echo $result;
		}
		Log::Set($result,3);
	}
	
	public static function StartService($data){
		sleep(1);
		$callID = $data['callid'];
		$from = $data['from'];
		$to = $data['to'];
		$direction = $data['direction'];
		
		$dbOrderTrack = new DB_OrderTrack();
		$orderTrack = $dbOrderTrack->fetch($callID,'call_id');
		
		if(!$orderTrack){
			return false;
		}
		
		$libOrder = new Lib_Order();
		$order = $libOrder->getOrderInfo($orderTrack['order_id']);
		
		if(!$order){
			return false;
			$order = $libOrder->getOrderInfo(8);
		}
		
		$updateRow = array(
			'call_time' => time(),
		);
		$dbOrderTrack = new DB_OrderTrack();
		$dbOrderTrack->update(array('id' => $orderTrack['id']), $updateRow);
		
		$orderRouteText = self::getOrderRouteText($order);
		
		$text = "您好,这里是打车系统,下面有一叫车信息, {$orderRouteText},接受请按 1,  拒绝请按2, 重听请按3";
		
		$result = "<Get action='status1' numdigits='1'><PlayTTS>{$text}</PlayTTS></Get>";
		return $result;
	} 
	
	public static function StopService($data){
		$callID = $data['callid'];
		
		$dbOrderTrack = new DB_OrderTrack();
		$orderTrack = $dbOrderTrack->fetch($callID,'call_id');
		
		if(!$orderTrack){
			return false;
		}
		
		$libOrder = new Lib_Order();
		$order = $libOrder->getOrderInfo($orderTrack['order_id']);
		
		if(!$order){
			return false;
		}
		
		if($orderTrack['status'] == DB_OrderTrack::STATTUS_CALLING){
			$libOrderBusiness = new Lib_Order_Business();
			$libOrderBusiness->refuse(orderTrack);
		}
		return;
	}
	
	public static function Status1($data){ //状态1 处理
		$callID = $data['callid'];
		$digits = $data['digits'];
		
		$dbOrderTrack = new DB_OrderTrack();
		$orderTrack = $dbOrderTrack->fetch($callID,'call_id');
		
		if(!$orderTrack){
			return false;
		}
		
		$libOrder = new Lib_Order();
		$order = $libOrder->getOrderInfo($orderTrack['order_id']);
		
		if(!$order){
			return false;
		}
		
		$digits = intval($digits);
		switch ($digits){
			case 1: //接受订单
				$libOrderBusiness = new Lib_Order_Business();
				$libOrderBusiness->accept($orderTrack);
				
				$text = self::getOrderConnectText($order);
				$text = $text . ",重听请按1";
				$result = "<Get action='status2' numdigits='1' timeout='20'><PlayTTS>{$text}</PlayTTS></Get>";
				break;
			case 2: //拒绝订单
				$result = "<PlayTTS>已拒绝,再见!</PlayTTS>";
				break;
			case 3: //重复订单
				$text = self::getOrderRouteText($order);
				$text = "{$text},接受请按 1,  拒绝请按2, 重听请按3";
				$result = "<Get action='status1' numdigits='1' timeout='20'><PlayTTS>{$text}</PlayTTS></Get>";
				break;
			default: //等待
				$result = "<Get action='status1' numdigits='1' timeout='10'></Get>";
				break;
		}
		return $result;
	}
	
	public static function Status2($data){ //状态1 处理
		$callID = $data['callid'];
		$digits = $data['digits'];
	
		$dbOrderTrack = new DB_OrderTrack();
		$orderTrack = $dbOrderTrack->fetch($callID,'call_id');
	
		if(!$orderTrack){
			return false;
		}
	
		$libOrder = new Lib_Order();
		$order = $libOrder->getOrderInfo($orderTrack['order_id']);
	
		if(!$order){
			return false;
		}
	
		$digits = intval($digits);
		switch ($digits){
			case 1: //重听
				$text = self::getOrderConnectText($order);
				$text = $text . ",重听请按1";
				$result = "<Get action='status2' numdigits='1' timeout='20'><PlayTTS>{$text}</PlayTTS></Get>";
				break;
			default: //等待
				$result = "<Get action='status2' numdigits='1' timeout='10'></Get>";
				break;
		}
		return $result;
	}
	
	public static function getOrderRouteText($order){
		$libOrder = new Lib_Order();
		$orderInfo = $libOrder->GetReadableOrder($order);
		$time = date('m月d日 H点i分',$order['time']);
		$text = "出发时间:{$time},出发地:{$orderInfo['departure']},目的地:{$orderInfo['destination']},人数:{$order['num']}人";
		return $text;
	}
	
	public static function getOrderConnectText($order){
		$mobile = $order['contact_mobile'];
		$mobile = self::MakeHearableMobile($mobile);
		$text = "请记住对方电话,电话是:{$mobile}. 重复一次,电话是{$mobile},";
		return $text;
	}
	
	
	public static function BuildResponse($result){
		if(!$result){
			return '';
		}
		$xml = <<<ETO
<?xml version="1.0" encoding="UTF-8"?>
<Response>
    {$result}
</Response>
ETO;
		return $xml;
	}
	
	public static function MakeHearableMobile($mobile){
		if(strlen($mobile) == 11){
			$mobile1 = substr($mobile, 0,3);
			$mobile2 = substr($mobile, 3,4);
			$mobile3 = substr($mobile, 7,4);
			$mobile = "$mobile1-$mobile2-$mobile3";
		}
		return $mobile;
	}
}