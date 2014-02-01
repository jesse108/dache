<?php
/**
 * order table DB class 
 * 
 * @author zhaojian
 *
 */
class DB_OrderTrack extends DB_Model{
	public $tableName = 'order_track';
	
	public function create($condition,$duplicateCondition = NULL){
		$time = time();
		$condition['create_time'] = $condition['create_time'] ? $condition['create_time'] : $time;
		
		return parent::create($condition,$duplicateCondition);
	}
}