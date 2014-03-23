<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';

$libOrderTemp = new Lib_OrderTemp();

$order = $libOrderTemp->getOrder();
if(!Lib_OrderTemp::CheckOrder($order)){
	Lib_System::SetError(Lib_OrderTemp::$error);
	Utility::Redirect("/weixin/index.php");
}


if($_POST['action'] == 'confirm'){
	$libOrder = new Lib_Order();
	$orderID = $libOrder->submit($order);
	if($orderID){
		Utility::Redirect("/weixin/order/status.php?order_id={$orderID}");
	} else {
		Lib_System::SetError($libOrder->error);
		Utility::Redirect("/weixin/index.php");
	}
}

//////////前端显示
$orderShow = Lib_Order::GetReadableOrder($order);
Template::Show();