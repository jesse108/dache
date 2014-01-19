<?php
class Log{
	const LOG_TYPE_DB = 1;
	const LOG_TYPE_FILE = 2;
	public static $logObj;
	
	public static function Init($type = self::LOG_TYPE_DB){
		if($type == self::LOG_TYPE_DB){
			$logObj = new Log_DB();
			self::$logObj = $logObj;
		}
	}
	
	public static function Set($data,$type = 1){
		if(!self::$logObj){
			self::Init();
		}
		return self::$logObj->log($data,$type);
	}
}