<?php
class DB_WeiXinUser extends DB_Model{
	public $tableName = 'weixin_user';
	public $primaryKey ='user_id';
	
	public function create($condition,$duplicateCondition = null){
		$time = time();
		$condition['create_time'] = $condition['create_time'] ? $condition['create_time'] : $time;
		$condition['update_time'] = $condition['update_time'] ? $condition['update_time'] : $time;
	
		if(Util_Array::IsArrayValue($duplicateCondition)){
			$duplicateCondition['update_time'] = $duplicateCondition['update_time'] ? $duplicateCondition['update_time'] : $time;
		}
	
		return parent::create($condition,$duplicateCondition);
	}
}