<?php
/**
 * cloopen 接口实现
 * 
 * @link http://docs.cloopen.com/
 * @author zhaojian01
 *
 */
class Lib_Cloopen{
	public $appID;
	public $mainAccount;  //主账号
	public $mainToken;    //主账号token
	public $restUrl;
	public $softVersion;
	
	public $subAccount;
	public $subToken;
	public $subVoipAccount;
	public $subVoipPwd;
	
	public function __construct(){
		$config = Config::Get('cloopen');
		$this->appID = $config['app_id'];
		$this->mainAccount = $config['sid'];
		$this->mainToken = $config['token'];
		$this->restUrl = $config['rest_url'];
		$this->softVersion = $config['soft_version'];
		
		//子账号信息
		$this->subAccount = $config['sub_account']['sid'];
		$this->subToken = $config['sub_account']['token'];
		$this->subVoipAccount = $config['sub_account']['voip_account'];
		$this->subVoipPwd = $config['sub_account']['voip_pwd'];
	}
	
	/**
	 * 获取主账号信息
	 * @return mixed
	 */
	public function mainAccountInfo(){
		$action = "AccountInfo";
		$result = $this->request($action,null,'GET');
		return $result;
	}
	
	/**
	 * 创建子账号
	 * 
	 * @param string $friendlyName 子账号名称
	 */
	public function createSubAccount($friendlyName){
		$data = array(
			'appId' => strval($this->appID),
			'friendlyName' => $friendlyName,
		);
		$action = 'SubAccounts';
		
		$result = $this->request($action, $data);
		return $result;
	}
	
	/**
	 * 获取子账号信息
	 * 
	 * @param number $startNo
	 * @param number $offset
	 */
	public function getSubAccounts($startNo = 0,$offset = 10){
		$data = array(
			'appId' => $this->appID,
			'startNo' => $startNo,
			'offset' => $offset,
		);
		$action = 'GetSubAccounts';
		$result = $this->request($action,$data);
		return $result;
	}
	
	/**
	 * 双向呼叫
	 * 
	 * @param $from 主叫号码
	 * @param $to 被叫号码
	 * @param $customerSerNum 被叫方看到的号码
	 * @param $from 主叫方看的的号码
	 */
	public function callBack($from,$to,$customerSerNum = '',$fromSerNum = ''){
		$data = array(
				'from' => $from,
				'to' => $to,
		);
		
		if($customerSerNum){
			$data['customerSerNum'] = $customerSerNum;
		}
		if($fromSerNum){
			$data['fromSerNum'] = $fromSerNum;
		}
		
		$action = 'Calls/Callback';
		$result = $this->request($action,$data,'POST',false);
		return $result;		
	}
	
	/**
	 * 关闭子账号
	 *
	 * @param number $startNo
	 */
	public function closeSubAccount($subAccountSid){
		$data = array(
				'subAccountSid' => $subAccountSid,
		);
		$action = 'CloseSubAccount';
		$result = $this->request($action,$data);
		return $result;
	}
	
	/**
	 * 发送短信
	 * 
	 * 如果信息体超出65个字 会自动变成长短信
	 * 
	 * @param $to      发送手机号  多个以 , 分割
	 * @param $body    发送信息体
	 * @param $msgType 0 普通短息  1 长短信
	 */
	public function smsMessge($to,$body,$msgType = 0){
		$messgeLen = mb_strlen($body,'utf8');
		if($messgeLen > 65){
			$msgType = 1;
		}
		$data = array(
				'to' => $to,
				'body' => $body,
				'msgType' => $msgType,
				'appId' => $this->appID,
				'subAccountSid' => $this->subAccount,
		);
		$action = 'SMS/Messages';
		$result = $this->request($action,$data);
		return $result;
	}
	
	
	public function landingCalls($to){
		$data = array(
				'subAccountSid' => $subAccountSid,
		);
		$action = 'CloseSubAccount';
		$result = $this->request($action,$data);
		return $result;
	}
	
	
	
	/**
	 * 请求
	 * @param string $action 请求方法
	 * @param array $data 请求参数  
	 * @param string $method GET?/POST
	 * @param boolean $sub 是否使用子账号
	 * @return mixed
	 */
	public function request($action,$data = null,$method = "POST",$main = true){
		$url = $this->restUrl;
		$timeStr = date('YmdHis');
		$softVersion = $this->softVersion;
		
		if($main){  //主账号
			$account = $this->mainAccount;
			$token = $this->mainToken;
			$accountType = "Accounts";
		} else { //
			$account = $this->subAccount;
			$token = $this->subToken;
			$accountType = "SubAccounts";
		}

		
		$sig =  strtoupper(md5($account . $token . $timeStr));
		
		
		$url = "{$url}/{$softVersion}/{$accountType}/{$account}/$action?sig={$sig}";
		$authen = base64_encode("{$account}:{$timeStr}");
		$header = array("Accept:application/json","Content-Type:application/json;charset=utf-8","Authorization:$authen");
		
		if($data){
			$data = json_encode($data);
		}
		$result = Util_HttpRequest::Http($url, $method,$data,$header);
		if($result){
			$result = json_decode($result,true);
		}
		return $result;
	}
}