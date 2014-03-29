<?php
class Lib_Location{
	private static $allLoaction = array();
	
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
}