<?php
/**
 * 主动请求逻辑处理
 * 
 * @author zhaojian01
 *
 */
class Lib_WeiXin_Dache{
	public $token = '';
	public $server = '';
	public $appID = '';
	public $appSecret = '';
	
	public function __construct($weixinConfig){
		$this->token = $weixinConfig['token'];
		$this->server = $weixinConfig['server'];
		$this->appID = $weixinConfig['app_id'];
		$this->appSecret = $weixinConfig['app_secret'];
	}
	
	
	
	public function createMenu(){
		$tokenResult = WeiXin_Util::GetAccessToken($this->appID, $this->appSecret);
		
		$accessToken = $tokenResult['access_token'];
		if(!$accessToken){
			return false;
		}
		
		
		
		$menu = array(
			'button' => array(
				array(
					'type' => WeiXin_Handler::MENU_BUTTON_TYPE_CLICK,
					'name' => '马上叫车',
					'key' => Lib_WeiXin_RequestHandler::MENU_ACTION_CALL,
				),
				array(
					'type' => WeiXin_Handler::MENU_BUTTON_TYPE_CLICK,
					'name' => '叫车记录',
					'key' => Lib_WeiXin_RequestHandler::MENU_ACTION_RECORD,
				),
				array(
					'name' => '投诉/其他',
					'sub_button' => array(
						array(
							'type'=>WeiXin_Handler::MENU_BUTTON_TYPE_CLICK,
							'name' => '投诉建议',
							'key' => Lib_WeiXin_RequestHandler::MENU_ACTION_COMPLAIN,
						),
						array(
							'type'=>WeiXin_Handler::MENU_BUTTON_TYPE_CLICK,
							'name' => '车主求加入',
							'key' => Lib_WeiXin_RequestHandler::MENU_ACTION_JOIN,
						),
					),
				),
			),
		);
		$result = WeiXin_Util::CreateMenu($menu, $accessToken);
		dump($result);
	}
}