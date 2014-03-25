<?php
class DB_CompanyRoute extends DB_Model{
	const STATUS_NORMAL = 0;
	const STATUS_DEL = 1;
	
	public $tableName = 'company_route';
	
	
	public function getAllAvailableRoute(){
		$condition = array(
			'status' => self::STATUS_NORMAL,
		);
		$result = $this->get($condition);
		return $result;
	}
}