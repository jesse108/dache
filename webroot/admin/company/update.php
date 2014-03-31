<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';
$adminEnv = Lib_Admin::getEnv();

$companyID = $_REQUEST['company_id'];

if($companyID){
	$company = Lib_Company::Fetch($companyID);
	if(!Util_Array::IsArrayValue($company)){
		Lib_Admin::SetError("公司ID 不正确,请从正确入口进入!!");
		Utility::Redirect('/admin/company/index.php');
	}
}

if($_POST){
	$name = $_POST['name'];
	$phone = $_POST['phone'];
	$comment = $_POST['comment'];
	if($company){
		$oldCompany = $company;
	}
	$company = array(
		'name' => $name,
		'phone' => $phone,
		'comment' => $comment,
	);
	
	if($oldCompany){
		//修改
		$result = Lib_Company::Update($oldCompany, $company);
	} else {
		//创建
		$result = Lib_Company::Create($company);
		$companyID = intval($result);
	}
	
	if($result){
		Lib_Admin::SetNotice('更新成功');
		Utility::Redirect("/admin/company/update.php?company_id={$companyID}&success=1");
	}
	
	Lib_Admin::SetError('更新失败:'.Lib_Company::$error);
}


Template::Show();