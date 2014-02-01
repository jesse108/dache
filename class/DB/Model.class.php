<?php
class DB_Model{
	public $tableName = '';
	public $error = '';
	public $readDB ='ro';
	public $writeDB = 'rw';
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
	
	public function update($condition,$updateRow){
		$result = DB::Update($this->tableName, $condition, $updateRow,$this->writeDB);
		return $result;
	}
	
	
	public function fetch($id,$key = null){
		if(!$id){
			return false;
		}
		
		$key = $key ? $key : $this->primaryKey;
		
		$condition = array(
			$key => $id,
		);
		
		if(Util_Array::IsArrayValue($id)){
			$one = false;
		} else {
			$one = true;
		}
		$option =array('one' => $one);
		
		$result = DB::LimitQuery($this->tableName,$condition,$option,$this->readDB);
		if(!$result){
			$this->error = DB::$error;
		}
		return $result;
	}
}