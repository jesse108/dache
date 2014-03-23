<?php
class Lib_System{
	public static function SetError($value){
		Session::Set('error', $value);
	}
	
	public static function SetNotice($value){
		Session::Set('notice', $value);
	}
	
	public static function GetError($once=true){
		$result =  Session::Get('error',$once);
		return $result;
	}
	
	public static function GetNotice($once=true){
		return Session::Get('notice',$once);
	}
}