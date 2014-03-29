<?php
class Lib_Company{
	
	public static function Fetch($companyID){
		$dbCompany = new DB_Company();
		return $dbCompany->fetch($companyID);
	}
}