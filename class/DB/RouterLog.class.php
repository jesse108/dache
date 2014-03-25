<?php
/**
 * order table DB class 
 * 
 * @author zhaojian
 *
 */
class DB_RouterLog extends DB_Model{
	public $tableName = 'router_log';
	
	public function create($condition,$duplicateCondition = NULL){
		$time = time();
		$condition['create_time'] = $condition['create_time'] ? $condition['create_time'] : $time;
		
		if(Util_Array::IsArrayValue($duplicateCondition)){
			$duplicateCondition['update_time'] = $duplicateCondition['update_time'] ? $duplicateCondition['update_time'] : $time;
		}
		
		return parent::create($condition,$duplicateCondition);
	}
}