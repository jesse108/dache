<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';
$libUser =new Lib_User();
if($_GET['open_id']){
	$libUser->weixinLogin($_GET['open_id']);
	Utility::Redirect('/weixin/index.php');
}

$loginUserID = $libUser->getLoginUserID();
$orderTemp = new Lib_OrderTemp();

$timeArray = Lib_OrderTemp::getSelectTimeInfo();
$maxNum = 6;
///////////////订单设置
$orderInfo = array();
if($loginUserID){
	$orderInfo['user_id'] = $loginUserID;
}
if($_GET['departure']){
	$orderInfo['departure'] = $_GET['departure'];
}

if(isset($_GET['destination'])){
	$orderInfo['destination'] = $_GET['destination'];
}

if($_GET['time']){
	$orderInfo['time'] = $_GET['time'];
}

if($_GET['num']){
	$orderInfo['num'] = $_GET['num'];
}

if(isset($_GET['contact_mobile'])){
	$orderInfo['contact_mobile'] = $_GET['contact_mobile'];
}

$orderTemp->setOrder($orderInfo);
////////获取当前订单信息
$order = $orderTemp->getOrder();
if($_GET['action'] == 'submit'){
	//提交
	$checkResult = Lib_OrderTemp::CheckOrder($order);
	if($checkResult){
		Utility::Redirect("/weixin/order_create/confirm.php");
	} else{
		Lib_System::SetError(Lib_OrderTemp::$error);
		Utility::Redirect("/weixin/index.php");
	}
}



if($order['departure']){
	$locationDeparture = Lib_Location::Fetch($order['departure']);
}

if($order['destination']){
	$locationDestination = Lib_Location::Fetch($order['destination']);
}


//////////获取今天的订单信息
$libOrder = new Lib_Order();
$todayOrders = $libOrder->getTodayOrder($loginUserID);

foreach ($todayOrders as &$todayOrder){
	$currentOrderShow = Lib_Order::GetReadableOrder($todayOrder);
	$todayOrder['show'] = $currentOrderShow;
}
Template::Show();