<?php
class DB_Company extends DB_Model{
	const STATUS_NORMAL = 0;//正常
	const STATUS_DEL = 1;//删除
	
	const CALL_STATUS_NO_CALL = 0; //呼叫状态  无呼叫
	const CALL_STATUS_CALLING = 1; //呼叫状态  呼叫中
	
	public $tableName = 'company';
	
	
	public function create($condition,$duplicateCondition = null){
		$time = time();
		$condition['create_time'] = $condition['create_time'] ? $condition['create_time'] : $time;
		$condition['update_time'] = $condition['update_time'] ? $condition['update_time'] : $time;
		
		if(Util_Array::IsArrayValue($duplicateCondition)){
			$duplicateCondition['update_time'] = $duplicateCondition['update_time'] ? $duplicateCondition['update_time'] : $time;
		}
		
		return parent::create($condition,$duplicateCondition);
	}
	
	public function update($condition, $updateRow){
		$updateRow['update_time'] = $updateRow['update_time'] ? $updateRow['update_time'] : time();
		return parent::update($condition, $updateRow);
	}
}