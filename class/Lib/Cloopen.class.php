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
	
	public $respUrl = "http://www.dachequ.com/cloopen.php"; //默认回调地址
	
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
	
	/**
	 * 营销呼叫
	 * 
	 * @param string $to 呼叫号码
	 * @param string $mediaTxt 呼叫内容
	 * @param string $mediaName 呼叫音频名字
	 * @param string $displayNum  对方显示号码
	 * @param number $playTimes 播放次数
	 * @param string $respUrl  回调地址
	 * @return boolean|mixed
	 */
	public function landingCalls($to,$mediaTxt = '',$mediaName = '',$displayNum = '',$playTimes = 2,$respUrl = ''){
		if(!$mediaName && !$mediaTxt){
			return false;
		}
		$respUrl = $respUrl ? $respUrl : $this->respUrl;
		
		$data = array(
			'appId' => $this->appID,
			'to' => $to,
			'playTimes' => $playTimes,
		);
		
		if($mediaName){
			$data['mediaName'] = $mediaName;
		}
		
		if($mediaTxt){
			$data['mediaTxt'] = $mediaTxt;
		}
		
		if($displayNum){
			$data['displayNum'] = $displayNum;
		}
		
		if($respUrl){
			$data['respUrl'] = $respUrl;
		}
		
		$action = 'Calls/LandingCalls';
		$result = $this->request($action,$data);
		return $result;
	}
	
	
	public function callVoiceVerify($verifyCode,$to,$displayNum = '', $playTimes = 3,$respUrl = ''){
		$respUrl = $respUrl ? $respUrl : $this->respUrl;
		$data = array(
			'appId' => $this->appID,
			'verifyCode' => $verifyCode,
			'to' => $to,
			'playTimes' => $playTimes,
			'respUrl' => $respUrl,
		);
		if($displayNum){
			$data['displayNum'] = $displayNum;
		}
		$action = 'Calls/VoiceVerify';
		$result = $this->request($action,$data);
		return $result;		
	}
	
	
	////////////////IVR 呼叫
	
	public function ivrDial($number,$record = false){
		$data = array(
			'Appid' => $this->appID,
			'Dial' => array(
				'attribute' => array('number' => $number),
			),
			'record' => $record ? 'true' : 'false',
			'userdata' => '1234'
		);
		$data = self::BuildXML($data, 'Request');
		$action = 'ivr/dial';
		$result = $this->request($action,$data,'POST',true,'xml');
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
	public function request($action,$data = null,$method = "POST",$main = true,$format = 'json'){
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
		
		
		if($format == 'xml'){
			$header = array("Accept:application/xml","Content-Type:application/xml;charset=utf-8","Authorization:$authen");
		} else {//json
			$header = array("Accept:application/json","Content-Type:application/json;charset=utf-8","Authorization:$authen");
			if($data){
				$data = json_encode($data);
			}
		}
		$result = Util_HttpRequest::Http($url, $method,$data,$header);
		if($result){
			if($format == 'xml'){
				$result = simplexml_load_string($result);
				$result = $result ? Util_Array::ObjectToArray($result) : $result;
			} else {
				$result = json_decode($result,true);
			}
		}
		return $result;
	}
	
	public static function BuildXML($data,$parentTag){
		if(!Util_Array::IsArrayValue($data)){
			return $data;
		}
		
		$xml = '';
		foreach ($data as $index => $one){
			if(is_array($one)){
				if($one['attribute']){
					$attribute = '';
					foreach ($one['attribute'] as $akey => $aval){
						$attribute .= " {$akey}=\"{$aval}\"";
					}
				}
				$value = strval($one['value']);
				$xml .= "<{$index}{$attribute}>{$value}</{$index}>";
			} else {
				$xml .= "<{$index}>{$one}</{$index}>";
			}
		}
		
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><{$parentTag}> {$xml} </{$parentTag}>";
		return $xml;
	}
}