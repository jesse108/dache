<?php
class Lib_Company{
	public static $error;
	
	public static function Create($company){
		$dbCompany = new DB_Company();
		$companyID = $dbCompany->create($company);
		
		if(!$companyID){
			self::$error = $dbCompany->error;
		}
		return $companyID;
	}
	
	public static function Update($oldCompany,$updateRow){
		$condition = array('id' => $oldCompany['id']);
		
		$dbCompany = new DB_Company();
		$result = $dbCompany->update($condition, $updateRow);
		
		if(!$result){
			self::$error = $dbCompany->error;
		}
		return $result;
	}
	
	
	public static function Fetch($companyID){
		$dbCompany = new DB_Company();
		return $dbCompany->fetch($companyID);
	}
	
	public static function getList(){
		$condition = array('status' => DB_Company::STATUS_NORMAL);
		$dbCompany = new DB_Company();
		$companys = $dbCompany->get($condition);
		
		if(Util_Array::IsArrayValue($companys)){
			$companys = Util_Array::AssColumn($companys, 'id');
		}
		return $companys;
	}
	
	
	public static function getCompanyRouteCount($companyIDs){
		if(!$companyIDs){
			return false;
		}
		
		$condition = array(
			'company_id' => $companyIDs,
			'status' => DB_CompanyRoute::STATUS_NORMAL,
		);
		$option = array(
			'select' => 'count(1) count,company_id',
			'group' => 'group by company_id',
		);
		$dbCompanyRoute = new DB_CompanyRoute();
		$countInfos = $dbCompanyRoute->get($condition,$option);
		if(!Util_Array::IsArrayValue($countInfos)){
			return false;
		}
		$countInfos = Util_Array::AssColumn($countInfos, 'company_id');
		if(!is_array($companyIDs)){
			return $countInfos[$companyIDs];
		}
		return $countInfos;
	}
	
	public static function getRoute($companyIDs){
		if(!$companyIDs){
			return false;
		}
		$dbCompanyRoute =new DB_CompanyRoute();
		
		$condition = array(
			'company_id' => $companyIDs,
			'status' => DB_CompanyRoute::STATUS_NORMAL,
		);
		
		$routes = $dbCompanyRoute->get($condition);
		if(!Util_Array::IsArrayValue($routes)){
			return false;
		}
		
		$companyRoutes = array();
		foreach ($routes as $route){
			$companyRoutes[$route['company_id']][$route['id']] = $route;
		}
		
		if(is_array($companyIDs)){
			return $companyRoutes;
		} else {
			return $companyRoutes[$companyIDs];
		}
	}
	
	
	public static function getCallStatusNames(){
		$stautsNames = array(
			DB_Company::CALL_STATUS_CALLING => '呼叫中',
			DB_Company::CALL_STATUS_NO_CALL => '空闲',
		);
		return $stautsNames;
	}
}