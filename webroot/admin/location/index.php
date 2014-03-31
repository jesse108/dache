<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';
$adminEnv = Lib_Admin::getEnv();
$level = intval($_GET['level']);


$allLocation = Lib_Location::GetAllLocatoin();

//////////
$levelArray = array(
	0 => array('value' => 0, 'name' => '全部'),
	Lib_Location::LEVEL_PROVINCE => array('value' => Lib_Location::LEVEL_PROVINCE , 'name' => '省'),
	Lib_Location::LEVEL_CITY => array('value' => Lib_Location::LEVEL_CITY , 'name' => '市'),
	Lib_Location::LEVEL_DISTRICT => array('value' => Lib_Location::LEVEL_DISTRICT , 'name' => '区'),
);

/////////省市筛选
if($level){
	foreach ($allLocation as $location){
		if($level == $location['level']){
			$showLocation[$location['id']] = $location;
		}
	}
} else {
	$showLocation = $allLocation;
}


foreach ($showLocation as &$one){
	$levelName = $levelArray[$one['level']]['name'];
	$parent = $allLocation[$one['parent_id']];
	
	$one['level_name'] = $levelName;
	$one['parent_name'] = $parent ? $parent['name'] : '';
	
	$one['operate'] = "<a href='/admin/location/update.php?loaction_id={$one['id']}' target='_blank'>修改</a> 
			<a href='/admin/location/update.php?parent_id={$one['id']}' target='_blank'>创建子地区</a>";
}





$thInfo = array(
	'id' => 'id',
	'name' => '名称',
	'ename' => 'ename',
	'level_name' => '等级',
	'parent_name' => '上级',
	'operate' => '操作',
);


$htmlObj = new Html_Bootstrap_Table($showLocation);
$htmlObj->setTableInfo($thInfo);
$tableHtml = $htmlObj->createHtml();

Template::Show();