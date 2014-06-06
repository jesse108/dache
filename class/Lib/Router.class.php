<?php
/**
 * 打车路线
 * @author zhaojian0
 *
 */
class Lib_Router{
	public $dbCompanyRoute;
	public $dbLocation;
	
	public function __construct(){
		$this->dbCompanyRoute = new DB_CompanyRoute();
		$this->dbLocation = new DB_Location();	
	}
	
	/**
	 * 获取所有 出发地
	 * @return boolean|unknown
	 */
	public function getAllDeparture(){
		$allRoute = $this->dbCompanyRoute->getAllAvailableRoute();
		if(!Util_Array::IsArrayValue($allRoute)){
			return FALSE;
		}
		
		$departureIDs = Util_Array::GetColumn($allRoute, 'departure');
		$locations = Lib_Location::Fetch($departureIDs);
		
		$parentIDs = Util_Array::GetColumn($locations, 'parent_id');
		$cities = Lib_Location::Fetch($parentIDs);
		
		foreach ($locations as $location){
			$cityID = $location['parent_id'];
			$cities[$cityID]['sub_locations'][] = $location; 
		}
		
		return $cities;
	}
	
	/**
	 * 根据出发到底 获取错有目的地
	 * @param int $departureID
	 */
	public function getDestination($departureID){
		$condition = array(
			'departure' => $departureID,
			'status' => DB_CompanyRoute::STATUS_NORMAL,
		);
		$routes = $this->dbCompanyRoute->get($condition);
		$distinationIDs = Util_Array::GetColumn($routes, 'destination');
		$locations = $this->dbLocation->fetch($distinationIDs);
		
		$parentIDs = Util_Array::GetColumn($locations, 'parent_id');
		$cities = $this->dbLocation->fetch($parentIDs);
		$cities = Util_Array::AssColumn($cities, 'id');
		
		foreach ($locations as $location){
			$cityID = $location['parent_id'];
			$cities[$cityID]['sub_locations'][] = $location;
		}
		return $cities;
	}
	
	/**
	 * 获取路线地点 显示内容
	 * 获取父级地点,  且 默认地点 回找二级
	 * 
	 */
	public function getRouteLocationShowStr($locationID){
		$location = Lib_Location::Fetch($locationID);
		$parentLocation = Lib_Location::Fetch($location['parent_id']);
		
		$locationName = Lib_Location::getLocationShowStr($location);
		$parentLocationName = Lib_Location::getLocationShowStr($parentLocation);
		
		$str ="$parentLocationName {$locationName}";
		return $str;
	}
}