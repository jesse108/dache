<?php
/**
 * 订单确认页  POST数据到此页
 */
include_once dirname(dirname(dirname(__FILE__))).'/app.php';

$libUser = new Lib_User();
$loginUserID = $libUser->getLoginUserID();

/////post数据

$libOrder = new Lib_Order();
$loginUserID = intval($loginUserID);
$departure = $_REQUEST['departure'];
$destination = $_REQUEST['destination'];
$time = $_REQUEST['time'];
$num = $_REQUEST['num'];
$contactMobile = $_REQUEST['contactMobile'];
$action = $_REQUEST['action'];

$orderInfo = array(
	'user_id' => $loginUserID,
	'departure' => $departure,
	'destination' => $destination,
	'time' => $time,
	'num' => intval($num),
	'contact_mobile' => $contactMobile,
);

$validateResult = $libOrder->validateOrder($orderInfo);

if(!$validateResult['pass']){
	Session::Set('error', $validateResult['message']);
	Utility::Redirect();
}

if($action == 'confirm'){
	$orderID = $libOrder->submit($orderInfo);
	$url = "/order/info.php";
	Utility::Redirect($url);
}


Template::Show();