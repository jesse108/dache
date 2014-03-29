<?php
class DB_Company extends DB_Model{
	const STATUS_NORMAL = 0;//正常
	const STATUS_DEL = 1;//删除
	
	const CALL_STATUS_NO_CALL = 0; //呼叫状态  无呼叫
	const CALL_STATUS_CALLING = 1; //呼叫状态  呼叫中
	
	public $tableName = 'company';
}