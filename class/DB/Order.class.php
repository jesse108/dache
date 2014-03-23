<?php
/**
 * order table DB class 
 * 
 * @author zhaojian
 *
 */
class DB_Order extends DB_Model{
	public $tableName = 'order';
	
	const STATUS_NORMAL = 0; //新创建 等待接单
	const STATUS_ACCEPT = 1; //订单被接
	const STATUS_REFUSE = 2; //订单被拒绝
	const STATUS_DEL = 3; //删除订单
	const STATUS_ACCEPT_ON = 10;//成功上车
	
	public static $staticArray = array(
		self::STATUS_NORMAL => array('title' => '等待接单'),
		self::STATUS_ACCEPT => array('title' => '已接单'),
		self::STATUS_REFUSE => array('title' => '无人接单'),
		self::STATUS_DEL => array('title' => '订单被删除'),
		self::STATUS_ACCEPT_ON => array('title' => '已上车'),
	);
	
	
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