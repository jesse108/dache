<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';
$libUser = new Lib_User();
$loginuserID = $libUser->getLoginUserID();

if(!$loginuserID){
	Utility::Redirect("/weixin/index.php");
}


$orderID = $_GET['order_id'];
$libOrder = new Lib_Order();
$order = $libOrder->getOrderInfo($orderID);

if(!$order){
	Lib_System::SetError("订单ID不对");
	Utility::Redirect('/weixin/index.php');
}

if($order['user_id'] != $loginuserID){
	Lib_System::SetError("这不是您的订单");
	Utility::Redirect('/weixin/index.php');
}

///订单信息
$orderShow = Lib_Order::GetReadableOrder($order);


///订单呼叫记录
$orderTracks = $libOrder->getOrderTrack($orderID);
$countTracks = count($orderTracks);
$orderTrackInfos = array();
foreach ($orderTracks as $orderTrack){
	$orderTrackInfos[$orderTrack['id']] = Lib_Order::getReadableOrderTrack($orderTrack);
	if($orderTrack['status'] ==  DB_OrderTrack::STATTUS_ACCEPT){
		$acceptCompanyID = $orderTrack['company_id'];
	}
}


///////////获取公司信息
$companyIDs = Util_Array::GetColumn($orderTracks, 'company_id');
$companys = Lib_Company::Fetch($companyIDs);
$companys = Util_Array::AssColumn($companys, 'id');


////////////评价
$evaluation = Lib_Evaluation::Fetch($orderID);

//默认评价
$defaultEvaluation = Lib_Evaluation::getDefaultEvaluation();
$serviceMark = $_GET['service_mark'] > 0 ? intval($_GET['service_mark']) : $defaultEvaluation['service_mark'];
$timeMark = $_GET['time_mark'] > 0 ? intval($_GET['time_mark']) : $defaultEvaluation['time_mark'];


switch ($order['status']){
	case DB_Order::STATUS_NORMAL:
		$companyCount = Lib_Order::getOrderCompanyCount($order);
		break;
	default:
	
		break;
}



////操作
if($_GET['action']){
	switch ($_GET['action']){
		case 'go_on'://上车	
			if($order['status'] == DB_Order::STATUS_ACCEPT){
				$libOrder->getOnBus($order);
			}
			Utility::Redirect('/weixin/order/status.php?order_id='.$orderID);
			break;
		case 'evaluation'://评价
			if($order['status'] == DB_Order::STATUS_ACCEPT_ON){
				$orderEvaluation = array(
					'service_mark' => intval($_GET['service_mark']),
					'time_mark' => intval($_GET['time_mark']),
					'comment' => $_GET['comment'],
				);
				Lib_Evaluation::Evaluate($order, $orderEvaluation);
			}
			Utility::Redirect('/weixin/order/status.php?order_id='.$orderID);
			break;
	}

}

Template::Show();