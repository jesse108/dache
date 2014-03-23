<?php
class DB_Company extends DB_Model{
	const STATUS_NORMAL = 0;//正常
	const STATUS_DEL = 1;//删除
	
	public $tableName = 'company';
}