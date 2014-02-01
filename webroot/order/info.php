<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';
$libUser = new Lib_User();
$libOrder = new Lib_Order();

$orderID = $_GET['order_id'];

if(!$orderID){
	Session::Set('error', '没有订单ID');
	Template::Show();
	exit;	
}

$loginUserID = $libUser->getLoginUserID();

$order = $libOrder->getOrderInfo($orderID);

///获取订单跟踪记录
$orderTracks = $libOrder->getOrderTrack($orderID);



Template::Show();