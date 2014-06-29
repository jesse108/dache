<?php
/**
 * 被动请求处理
 * @author zhaojian01
 *
 */
class Lib_WeiXin_RequestHandler implements WeiXin_Handler{
	public $msgType;
	public $requestData;
	public $config;
	
	const MENU_ACTION_CALL = 'menu_action_call';
	const MENU_ACTION_RECORD = 'menu_action_record';
	const MENU_ACTION_COMPLAIN = 'menu_action_complain';
	const MENU_ACTION_JOIN = 'menu_action_jion';
	
	public function __construct(){
		$config = Config::Get('weixin');
		$this->config = $config;
	}
	
	
	public function handleRquest($msgType, $requestData) {
		if(!$msgType || !$requestData){
			return false;
		}
		$this->msgType = $msgType;
		$this->requestData = $requestData;
	
		
		switch ($msgType){
			case WeiXin_Handler::MSG_TYPE_EVENT: //事件
				$result = $this->handleEvent();
				break;
			case WeiXin_Handler::MSG_TYPE_TEXT: //发过来文字
				$result = $this->handleText();
				break;
			default:
				$result = '';
				break;
		}
		return  $result;
	}


	public function handleText(){
		$requestData = $this->requestData;
		$userOpenID = $requestData['FromUserName'];
		$content = $requestData['Content'];
		
		if(strpos('打车', $content) !== false){
			$config = $this->config;
			$server = $config['server'];
			$url = $this->getRedirctUrl($config['server']['index']);
			$url .= "&open_id={$userOpenID}";
			
			$text = "需要打车服务? 请访问我们的打车平台 ";
			$text .= "<a href='{$url}'>点击进入</a>";
			
			$result = $this->buildTextData($text);
		} else {
			$result = false;
		}
		
		return $result;
	}
	
	public function handleEvent(){
		$requestData = $this->requestData;
		$eventType = $requestData['Event'];
		$config = $this->config;
		$textConfig = $config['text'];
		
		switch ($eventType){
			case WeiXin_Handler::EVENT_TYPE_SUBSCRIBE: //关注
				$userOpenID = $requestData['FromUserName'];
				
				////绑定用户
				$libUser = new Lib_User();
				$libUser->bindWeiXinUser($userOpenID);
				
				//拼装返回数据
				$text = $textConfig['subscribe'];
				$url = $this->getRedirctUrl($config['server']['index']);
				$url .= "&open_id={$userOpenID}";
				
				$text = $text." <a href='{$url}'>点击进入打车平台</a>";
				$result = $this->buildTextData($text);
				
				break;
			case self::EVENT_TYPE_CLICK:
				
				$result = $this->handleMenuAction();
				break;
			default:
				$result = false;
				break;
		}
		
		return $result;
	}
	
	
	public function handleMenuAction(){
		$requestData = $this->requestData;
		$userOpenID = $requestData['FromUserName'];
		
		$key = $requestData['EventKey'];
		$config = $this->config;
		
		switch ($key){
			case self::MENU_ACTION_JOIN:
				$text = "终于等到你了，快快加入我们，一起接单赚大钱吧。把你的手机号和接单线路告诉我们，我们将在第一时间联系你。";
				$result = $this->buildTextData($text);
				break;
			case self::MENU_ACTION_COMPLAIN:
				$text = "请在聊天窗口中直接输入您的投诉建议，我们会认真对待每一条建议，并且及时反馈。";
				$result = $this->buildTextData($text);
				break;
			case self::MENU_ACTION_CALL: //叫车
				$url = $this->getRedirctUrl($config['server']['index']);
				$url .= "&open_id={$userOpenID}";
				
				$text = "<a href='{$url}'>点击这里,马上叫车。</a>";
				$result = $this->buildTextData($text);
				break;
			case self::MENU_ACTION_RECORD://打车记录
				$url = $this->getRedirctUrl($config['server']['record']);
				$url .= "&open_id={$userOpenID}";
				
				$text = "<a href='{$url}'>点击这里,查看叫车记录。</a>";
				$result = $this->buildTextData($text);				
				break;
			default:
				$result = '';
				break;
		}
		return $result;
	}
	
	public function buildTextData($text){
		$requestData = $this->requestData;
		$devUserID = $requestData['ToUserName'];//开发者微信号
		$userOpenID= $requestData['FromUserName'];//用户信息
		$text = strval($text);
		
		$ret = array(
			'ToUserName' => $userOpenID,
			'FromUserName' => $devUserID,
			'CreateTime' => time(),
			'MsgType' => WeiXin_Handler::MSG_TYPE_TEXT,
			'Content' => $text,
		);
		return $ret;
	}
	
	public function getRedirctUrl($url){
		$url = $this->getRealUrl($url);
		$redirectUrl = $this->getRealUrl($this->config['server']['redirect']);
		
		$url = "{$redirectUrl}?url={$url}";
		return $url;
	}
	
	public function getRealUrl($url){
		if(strstr($url,'http://')){
			return $url;
		}
		$host = $this->config['server']['host'];
		$url = "{$host}{$url}";
		return $url;
	}	
}