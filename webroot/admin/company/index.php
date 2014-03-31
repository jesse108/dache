<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';
$adminEnv = Lib_Admin::getEnv();
///////呼叫状态
$callStatusNames = Lib_Company::getCallStatusNames();

/////////
$companys = Lib_Company::getList();
$companyIDs = Util_Array::GetColumn($companys, 'id');
$routeCounts = Lib_Company::getCompanyRouteCount($companyIDs);

foreach ($companys as &$one){
	$count = intval($routeCounts[$one['id']]['count']);
	$one['route_count'] = "<a href='/admin/route/index.php?company_id={$one['id']}' target='_blank'>{$count}</a>";
	$one['create_time'] = date('Y-m-d H:i:s',$one['create_time']);
	$one['call_status_name'] = $callStatusNames[$one['call_status']];
	$one['operate'] = "<a href='/admin/company/update.php?company_id={$one['id']}' target='_blank'>修改</a>  
			<a href='/admin/route/update.php?company_id={$one['id']}' target=''>增加线路</a>";
}


$thInfo = array(
	'id' => 'id',
	'name' => '名称',
	'create_time' => '创建时间',
	'phone' => '电话',
	'route_count' => '线路数',
	'call_status_name' => '呼叫状态',
	'comment' => '备注',
	'operate' => '操作',
);


$htmlObj = new Html_Bootstrap_Table($companys);
$htmlObj->setTableInfo($thInfo);
$tableHtml = $htmlObj->createHtml();

Template::Show();