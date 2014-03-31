<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';
$adminEnv = Lib_Admin::getEnv();

$routeID = $_REQUEST['route_id'];
$companyID = $_REQUEST['company_id'];

$dbRoute = new DB_CompanyRoute();

$company = Lib_Company::Fetch($companyID);
if(!$company){
	Lib_Admin::SetError("公司错误");
	Utility::Redirect('/admin/company/index.php');
}

if($routeID){
	$companyRoute = $dbRoute->fetch($routeID);
	if(!Util_Array::IsArrayValue($companyRoute)){
		$error = Lib_Admin::GetError('路线ID不对');
		Utility::Redirect("/admin/company/index.php");
	}
}


if($_POST){
	$departure = $_POST['departure'];
	$destination = $_POST['destination'];
	$oldRoute = $companyRoute;
	
	$companyRoute = array(
		'departure' => $departure,
		'destination' => $destination,
		'company_id' => $companyID,
		'status' => DB_CompanyRoute::STATUS_NORMAL,
	);
	
	$condition = $companyRoute;
	if($routeID){
		$condition[] = "id <> {$routeID}";
	}
	if($dbRoute->get($condition)){
		Lib_Admin::SetError("更新失败,有重复的路线");
	} else {
		if($oldRoute){
			$result  = $dbRoute->update(array('id'=>$routeID), $companyRoute);
		} else {
			$result = $dbRoute->create($companyRoute);
			$routeID = intval($result);
		}
		
		if(!$result){
			Lib_Admin::SetError("更新失败:".$dbRoute->error);
		} else {
			Lib_Admin::SetNotice("更新成功!!");
			Utility::Redirect("/admin/route/update.php?route_id={$routeID}&company_id={$companyID}&success=1");
		}		
	}
}



Template::Show();