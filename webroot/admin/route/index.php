<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';
$adminEnv = Lib_Admin::getEnv();
$companyID = $_GET['company_id'];
$company = Lib_Company::Fetch($companyID);
if(!Util_Array::IsArrayValue($company)){
	Lib_Admin::SetError('公司ID 错误');
	Utility::Redirect('/admin/company/index.php');
}



$allLocation = Lib_Location::GetAllLocatoin();
/////////////////
$routes = Lib_Company::getRoute($companyID);
$routes = Util_Array::IsArrayValue($routes) ? $routes : array();

$libRouter = new Lib_Router();
foreach ($routes as &$one){
	$one['departure'] = $libRouter->getRouteLocationShowStr($one['departure']);
	$one['destination'] = $libRouter->getRouteLocationShowStr($one['destination']);
	$one['company'] = $company['name'] . "[{$companyID}]";
	$one['operate'] = "<a href='/admin/route/update.php?route_id={$one['id']}&company_id={$companyID}' target='_blank'>修改</a>";
}

$thInfo = array(
	'id' => 'id',
	'company' => '公司',
	'departure' => '出发地',
	'destination' => '目的地',
	'operate' => '操作',
);


$htmlObj = new Html_Bootstrap_Table($routes);
$htmlObj->setTableInfo($thInfo);
$tableHtml = $htmlObj->createHtml();

Template::Show();