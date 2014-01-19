<?php
class Lib_WeiXin_RequestHandler implements WeiXin_Handler{
	public $msgType;
	public $requestData;
	public $config;
	
	public function __construct(){
		$config = Config::Get('weixin');
		$this->config = $config['default'];
	}
	
	/* (non-PHPdoc)
	 * @see WeiXin_Handler::handleRquest()
	 */
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
			$url = $server . "?user_open_id={$userOpenID}";
			
			$text = "需要打车服务? 请访问我们的打车平台 ";
			$text .= "\n <a href='{$url}'>点击进入</a>";
			
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
				$text = $textConfig['subscribe'];
				$url = $config['server'];
				$userOpenID = $requestData['FromUserName'];
				$url .= "?user_open_id={$userOpenID}";
				$text = $text."\n <a href='{$url}'>点击进入打车平台</a>";
				$result = $this->buildTextData($text);
				break;
			default:
				$result = false;
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
			'content' => $text,
		);
		return $ret;
	}
	
}