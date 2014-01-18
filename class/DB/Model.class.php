<?php
class DB_Model{
	public $tableName = '';
	public $error = '';
	public $readDB ='';
	public $writeDB = '';
	public $primaryKey = 'id';
	
	public function create($condition,$duplicateCondition = null){
		$table = $this->tableName;
		if(!$table){
			$this->error = 'Table name is null';
			return false;
		}
		$insertID = DB::Insert($table, $condition,$duplicateCondition,$this->writeDB);
		return $insertID;	
	}
	
	public function get($condition,$option = array()){
		$table = $this->tableName;
		$dbType = $dbType ? $dbType : $this-> readDB;
		if(!$table){
			$this->error = 'Table name is null';
			return false;
		}
		$result = DB::LimitQuery($table,$condition,$option,$dbType);
		return $result;
	}
	
	public function update(){
		
	}
}