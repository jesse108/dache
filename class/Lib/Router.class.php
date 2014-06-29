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
	public function getAllDeparture($parentID = 0){
		$allRoute = $this->dbCompanyRoute->getAllAvailableRoute();
		if(!Util_Array::IsArrayValue($allRoute)){
			return FALSE;
		}
		
		$departureIDs = Util_Array::GetColumn($allRoute, 'departure');
		$allLocations = Lib_Location::GetAllLocatoin(true);
		
		self::addFlag($allLocations, $departureIDs);
		
		
		if($parentID){
			$location = Util_Array::FindNodeInTree($allLocations, $parentID);
			$locations = array($location['id'] => $location);
		} else {
			//因为第一个是中国所以
			$locations = array_pop($allLocations);
			$locations = $locations['sub'];
		}
		
		return $locations;
	}
	
	public static function addFlag(&$locations,$departureIDs){
		if(!$locations){
			return false;
		}
		
		$ret = false;
		foreach ($locations as $index => $one){
			$subDeparture = false;
			$selfDeparture = false;
			
			if(in_array($one['id'], $departureIDs)){
				$selfDeparture = true;
			}
			
			if($one['sub']){
				$subDeparture = self::addFlag($one['sub'], $departureIDs);
			}
			$one['is_de'] = $selfDeparture;
			$one['sub_is_de'] = $subDeparture;
			$locations[$index] = $one;
			
			if($selfDeparture || $subDeparture){
				$ret = true;
			}
		}
		return $ret;
	}
	
	
	
	/**
	 * 获取出发地
	 * @param number $parentID
	 */
	public function getDeparture($parentID = 0){
		$condition = array(
			'status' => DB_CompanyRoute::STATUS_NORMAL,
		);
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