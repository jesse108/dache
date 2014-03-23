<?php
class Lib_Location{
	
	public static function Fetch($locationID){
		$dbLocation =new DB_Location();
		$location = $dbLocation->fetch($locationID);
		return $location;
	}
}