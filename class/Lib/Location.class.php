<?php
class Lib_Location{
	const MAX_LEVEL = 3; //地区最深区域 为 3 区
	const LEVEL_PROVINCE = 1;  //省
	const LEVEL_CITY = 2;// 市
	const LEVEL_DISTRICT = 3; //区
	public static $error;
	
	private static $allLoaction = array();
	
	
	public static function Create($loaction){
		$condition = array(
			'name' => $loaction['name'],
			'parent_id' => $loaction['parent_id'],
		);
		if(DB::Exists('locatioin', $condition)){
			self::$error = "已经存在此地区请重新填写";
			return false;
		}
		
		$dbLocation = new DB_Location();
		$locationID = $dbLocation->create($condition);
		if(!$locationID){
			self::$error = $dbLocation->error;
		}
		return $locationID;
	}
	
	public static function Update($oldLocation,$updateRow){
		$condition = array(
			'id' => $oldLocation['id'],
		);
		
		$dbLocation = new DB_Location();
		$result = $dbLocation->update($condition, $updateRow);
		if($result){
			self::$error = $dbLocation->error;
		}
		return $result;
	}
	

	
	public static function Fetch($locationID){
		$allLocations = self::GetAllLocatoin();
		
		if(is_array($locationID)){
			$locations = array();
			foreach ($locationID as $id){
				if($allLocations[$id]){
					$locations[$id] = $allLocations[$id];
				}
			}
			return $locations;
		} else {
			$location = $allLocations[$locationID];
			return $location;
		}
	}
	
	public static function GetAllLocatoin(){
		if(!Util_Array::IsArrayValue(self::$allLoaction)){
			$dbLocation =new DB_Location();
			$condition = array(
					'status' => DB_Location::STATUS_NORMAL,
			);
			$locations = $dbLocation->get($condition);
			$locations = Util_Array::AssColumn($locations, 'id');
			self::$allLoaction = $locations;
		}
		return self::$allLoaction;
	}
	
	public static function getLocationShowStr($location){
		$name = $location['name'] . self::getLevelShowStr($location['level']);
		return $name;
	}
	
	public static function getLevelShowStr($level){
		$name = '';
		
		if($level == self::LEVEL_CITY){
			$name = '市';
		}
		return $name;
	}
}