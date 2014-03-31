<?php
/**
 * 后台相关逻辑
 * @author zhaojian01
 *
 */
class Lib_Admin {
	
	/**
	 * 获取后台环境
	 */
	public static function getEnv() {
		$sideBar = self::getSideBar();
		
		$result = array(
			'side_bar_data' => $sideBar,
		);
		
		return $result;
	}
	
	public static function getSideBar() {
		$sideBar = array (
			'index' => array (
				'title' => '首页',
				'url' => "/admin/index.php",
				'key' => array (
						'/^\/admin\/index/',
						'/^\/admin$/',
						'/^\/admin\/$/' 
				) 
			),
			'location' => array (
				'title' => '地区管理 ',
				'url' => "/admin/location/index.php",
				'key' => '/admin\/location/' 
			),
			'company' => array (
				'title' => '公司管理',
				'url' => '/admin/company/index.php',
				'key' => '/admin\/compnay/' 
			) 
		);
		foreach ($sideBar as &$data){
			$key = $data['key'];
			if(self::isCurrentSection($key)){
				$data['current'] = true;
			} else {
				$data['current'] = false;
			}			
		}
		return $sideBar;
	}
	
	
	public static function isCurrentSection($keys){
		$keys = is_array($keys) ? $keys : array($keys);
		$uri = Utility::getRequestURI();
		foreach ($keys as $key){
			if(preg_match($key, $uri)){
				return true;
			}
		}
		return false;
	}
	
	public static function SetError($value){
		Session::Set('admin_error', $value);
	}
	
	public static function SetNotice($value){
		Session::Set('admin_notice', $value);
	}
	
	public static function GetError($once=true){
		$result =  Session::Get('admin_error',$once);
		return $result;
	}
	
	public static function GetNotice($once=true){
		return Session::Get('admin_notice',$once);
	}
}