<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';
$adminEnv = Lib_Admin::getEnv();

$locationID = $_REQUEST['location_id'];
$parentID = $_REQUEST['parent_id'];

if($locationID){
	$location = Lib_Location::Fetch($locationID);
	if(!Util_Array::IsArrayValue($location)){
		$error = Lib_Admin::GetError(false);
		Utility::Redirect("/admin/location/index.php");
	}
}

$parentID = $location ? $location['parent_id'] : $parentID;

if($_POST){
	$name = $_POST['name'];
	$ename = $_POST['ename'];
	$parentID = intval($_POST['parent_id']);
	$parentID = $parentID ? $parentID : 1; //默认中国
	$parentLocation = Lib_Location::Fetch($parentID);
	
	
	$oldLoaction = $location;
	
	$location = array(
		'name' => $name,
		'ename' => $ename,
		'level' => $parentLocation['level'] + 1,
		'parent_id' => $parentID,
	);
	if($oldLoaction){
		$location['id'] = $oldLoaction['id'];
	}
	
	if($oldLoaction){
		$result  = Lib_Location::Update($oldLoaction, $location);
	} else {
		$result = Lib_Location::Create($location);
		$locationID = $result;
	}
	
	if(!$result){
		Lib_Admin::SetError("更新失败:".Lib_Location::$error);
	} else {
		Lib_Admin::SetNotice("更新成功!!");
		Utility::Redirect("/admin/location/update.php?location_id={$locationID}&success=1");
	}
}



Template::Show();