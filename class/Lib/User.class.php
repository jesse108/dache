<?php
class Lib_User{
	public $dbUser;
	public $dbWeiXinUser;
	public $error;
	
	public function __construct(){
		$dbUser = new DB_User();
		$this->dbUser = $dbUser;
		
		$dbWeiXinUser = new DB_WeiXinUser();
		$this->dbWeiXinUser = $dbWeiXinUser;
	}
	
	public function login($userID){
		$user = $this->dbUser->fetch($userID);
		
		if(!Util_Array::IsArrayValue($user)){
			$this->error = "用户ID不正确";
		}
		$updateRow = array(
			'login_time' => time(),
			
		);
		
		return true;
	}
	
	public function weixinLogin($openID){
		$weixinUser = $this->dbWeiXinUser->fetch($openID,'open_id');
		if(!Util_Array::IsArrayValue($weixinUser)){
			$userID = $this->bindWeiXinUser($openID);
		} else {
			$userID = $weixinUser['user_id'];
		}
		
		if(!$userID){
			return false;
		}
		
		return $this->login($userID);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function bindWeiXinUser($userOpenID){
		$weixinUser = $this->dbWeiXinUser->fetch($userOpenID,'open_id');
		dump($weixinUser);exit;
		if(!$weixinUser){
			///先创建user
			$user = array();
			$userID = $this->dbUser->create($user);
			
			if($userID){
				$weixinUser = array(
					'user_id' => $userID,
					'open_id' => $userOpenID,
				);
				$result = $this->dbWeiXinUser->create($weixinUser);
				if(!$result){
					$this->error = $this->dbWeiXinUser->error;
				} else {
					$result = $userID;
				}
			} else {
				$this->error =  $this->dbUser->error;
				$result = false;
			}
		} else {
			$result =$weixinUser['user_id'];
		}
		
		return $result;
	}
	
	
	
}