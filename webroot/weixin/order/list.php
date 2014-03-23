<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';
$libUser = new Lib_User();
$loginuserID = $libUser->getLoginUserID();

if(!$loginuserID){
	Utility::Redirect("/weixin/index.php");
}

$libOrder = new Lib_Order();
$orders = $libOrder->getOrderList($loginuserID);
if($orders){
	foreach ($orders as &$order){
		$orderShow = Lib_Order::GetReadableOrder($order);
		$order['show'] = $orderShow;
	}
}

Template::Show();