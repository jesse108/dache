<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';
$adminEnv = Lib_Admin::getEnv();
$orderID = $_GET['id'];
$libOrder = new Lib_Order();
$orderInfo = $libOrder->getOrderInfo($orderID);
if(!Util_Array::IsArrayValue($orderInfo)){
	Lib_Admin::SetError("订单ID 错误 ");
	Utility::Redirect('/admin/index.php');
}

$company = Lib_Company::Fetch($orderInfo['company_id']);
$orderShow = Lib_Order::GetReadableOrder($orderInfo);
$evaluation = Lib_Evaluation::Fetch($orderID);


$orderInfo['departure'] = $orderShow['departure'];
$orderInfo['destination'] = $orderShow['destination'];
$orderInfo['statu_title'] = $orderShow['status_title'];
$orderInfo['company'] = "{$company['name']}(ID:{$orderInfo['company_id']})";
$orderInfo['create_time'] = date('Y-m-d H:i:s',$orderInfo['create_time']);
$orderInfo['update_time'] = date('Y-m-d H:i:s',$orderInfo['update_time']);
$orderInfo['time'] = date('Y-m-d H:i:s',$orderInfo['time']);



$titleArray = array(
	'id' => '订单ID',
	'user_id' => '用户ID',
	'departure' => '出发地',
	'destination' => '目的地',
	'company' => '接单公司',
	'statu_title' => '状态',
	'time' => '时间',
	'num' => '人数',
	'contact_mobile' => '联系电话',
	'create_time' => '创建时间',
	'update_time' => '最后更新时间',
);

if(Util_Array::IsArrayValue($evaluation)){
	$orderInfo['service_mark'] = $evaluation['service_mark'];
	$orderInfo['time_mark'] = $evaluation['time_mark'];
	$orderInfo['evluation'] = $evaluation['comment'];
	
	$titleArray['service_mark'] = '服务评分';
	$titleArray['time_mark'] = '时间评分 ';
	$titleArray['evluation'] = "评价";
}



$list = new Html_List($orderInfo);
$list->setTitle($titleArray);


$html = $list->createHtml();
Template::Show();