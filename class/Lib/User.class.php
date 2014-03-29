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
		if(Session::Get(SESSION_LOGIN_USER_ID) == $userID){
			return true;
		}
		$user = $this->dbUser->fetch($userID);
		
		if(!Util_Array::IsArrayValue($user)){
			$this->error = "用户ID不正确";
		}
		$updateRow = array(
			'login_time' => time(),
			'login_ip' => Utility::getUserIP(),
		);
		$this->dbUser->update(array('id' => $userID), $updateRow);
		Session::Set(SESSION_LOGIN_USER_ID, $userID);
		return true;
	}
	
	public function weixinLogin($openID){
		if(Session::Get(SESSION_LOGIN_WEIXIN_ID) == $openID){
			return true;
		}
		$weixinUser = $this->dbWeiXinUser->fetch($openID,'open_id');
		if(!Util_Array::IsArrayValue($weixinUser)){
			$userID = $this->bindWeiXinUser($openID);
		} else {
			$userID = $weixinUser['user_id'];
		}
		
		if(!$userID){
			return false;
		}
		$loginResult = $this->login($userID); 
		if($loginResult){
			SESSION::Set(SESSION_LOGIN_WEIXIN_ID, $openID);
			return true;
		}
		return false;
	}
	
	public  function getLoginUserID(){
		return Session::Get(SESSION_LOGIN_USER_ID);
	}
	
	
	public function bindWeiXinUser($userOpenID){
		$weixinUser = $this->dbWeiXinUser->fetch($userOpenID,'open_id');
		if(!$weixinUser){
			///先创建user
			$user = array(
				'username' => "weixin_{$userOpenID}",
			);
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
	
	/**
	 * 获取用户打过的路线
	 */
	public function getLastUserRoute($userID,$num = 3){
		if(!$userID){
			return false;
		}
		$dbOrder = new DB_Order();
		$delStatus = DB_Order::STATUS_DEL;
		$condition = array(
			'user_id' => $userID,
			"status <> {$delStatus}",
		);
		
		$option = array(
			'select' => 'distinct departure,destination',
			'size' => $num,
		);
		$routes = $dbOrder->get($condition,$option); //最近的订单路线
		
		$result = array();
		if(Util_Array::IsArrayValue($routes)){
			foreach ($routes as $index => $one){
				$departure = $one['departure'];
				$destination = $one['destination'];
				
				$departureLocation = Lib_Location::Fetch($departure);
				$destinationLocation = Lib_Location::Fetch($destination);
				$departureCity = Lib_Location::Fetch($departureLocation['parent_id']);
				$destinationCity = Lib_Location::Fetch($destinationLocation['parent_id']);
				
				$route = array(
					'departure' => $departure,
					'departure_name' => $departureCity['name'] .'市'. $departureLocation['name'],
					'destination' => $destination,
					'destination_name' => $destinationCity['name'].'市'.$destinationLocation['name'],
				);
				$result[] = $route;
			}
		}
		
		
		return $result;
	}
	
}