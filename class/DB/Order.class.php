<?php
/**
 * order table DB class 
 * 
 * @author zhaojian
 *
 */
class DB_Order extends DB_Model{
	public $tableName = 'order';
	
	const STATUS_NORMAL = 0;
	
	public function create($condition,$duplicateCondition = NULL){
		$time = time();
		$condition['create_time'] = $condition['create_time'] ? $condition['create_time'] : $time;
		$condition['update_time'] = $condition['update_time'] ? $condition['update_time'] : $time;
		
		if(Util_Array::IsArrayValue($duplicateCondition)){
			$duplicateCondition['update_time'] = $duplicateCondition['update_time'] ? $duplicateCondition['update_time'] : $time;
		}
		
		return parent::create($condition,$duplicateCondition);
	}
	
	public function update($condition,$updateRow){
		$updateRow['update_time'] = $updateRow['update_time'] ? $updateRow['update_time'] : time();
		
		return parent::update($condition, $updateRow);
	}
}