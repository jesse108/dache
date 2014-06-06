<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';
$adminEnv = Lib_Admin::getEnv();
$pageSize = 30;
$orderStatus = DB_Order::$staticArray;
$status = -1;

if($_GET['action'] == 'query'){
	$condition = array();
	$companyID = $_GET['company_id'];
	$startID = $_GET['start_id'];
	$endID = $_GET['end_id'];
	$orderStartDate = $_GET['order_start_date'];
	$orderEndDate = $_GET['order_end_date'];
	$userID = $_GET['user_id'];	
	$status = $_GET['status'];
	
	
	$companyID   	&& $condition['company_id'] = $companyID;
	$startID    	&& $condition['departure'] = $startID;
	$endID       	&& $condition['destination'] = $endID;
	$status >= 0     && $condition['status'] = $status;
	$orderStartDate && $condition[] = "create_time >= " . strtotime($orderStartDate);
	$orderEndDate	&& $condition[] = "create_time <= " . strtotime($orderEndDate);
	
	
	$dbOrder = new DB_Order();
	$dbCompany = new DB_Company();
	
	$count = $dbOrder->count($condition);
	$page = new Html_Page($pageSize, $count);
	$offset = $page->getOffset();
	$option = array(
		'offset' => $offset,
		'size' => $pageSize
	);
	$orderList  = $dbOrder->get($condition,$option);

	$companyIDs = Util_Array::GetColumn($orderList, 'company_id');	
	$companyList = $dbCompany->fetch($companyIDs);
	$companyList = Util_Array::AssColumn($companyList, 'id');
	
	foreach ($orderList as $index => $order){
		$orderShow = Lib_Order::GetReadableOrder($order);
		foreach ($orderShow as $i => $v){
			$order[$i] = $v;
		}
		$company = $companyList[$order['company_id']];
		$order['company'] = "{$company['name']}({$order['company_id']})";
		$order['id'] = "<a href='/admin/order/detail.php?id={$order['id']}' target='_blank'>{$order['id']}</a>";
		$orderList[$index] = $order;
	}
	
	$htmlTable = new Html_Bootstrap_Table($orderList);
	$thInfo = array(
		'id' => '订单ID',
		'user_id' => '用户ID',
		'departure' => '出发地',
		'destination' => '目的地',
		'company' => '接单公司',
		'show_time' => '乘车时间',
		'num' => '人数',
		'status_title' => '状态',
		'contact_mobile' => '联系电话',
		'create_time' => '创建时间',
	);
	$htmlTable->setTableInfo($thInfo);
	
	$html= $htmlTable->createHtml();
	$pageStr = $page->getHtml();
}


Template::Show();