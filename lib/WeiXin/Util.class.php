<?php
class WeiXin_Util{
	public static $url = "https://api.weixin.qq.com/cgi-bin";
	public static $accessTokens = array();
	
	public static function CreateMenu($menuData,$accessToken){
		$menuData = self::JsonEncode($menuData);
		$param = array(
			'access_token' => $accessToken,
		);
		$postData = $menuData;
		$type = "menu/create";
		$result = self::Request($type, $param, $postData,'POST');
		
		return $result;
	}
	
	
	public static function GetAccessToken($appid,$secret){
		if(self::$accessTokens[$appid]){
			return self::$accessTokens[$appid];
		}
		
		$param = array(
			'grant_type' => 'client_credential',
			'appid' => $appid,
			'secret' => $secret,
		);
		$type = 'token';
		$result = self::Request($type, $param);
		
		if($result['access_token']){
			self::$accessTokens[$appid] = $result;
		}
		
		return $result;
	}
	
	public static function Request($type,$parameters,$postData,$method = 'GET'){
		$url = self::$url;
		$url = "{$url}/{$type}";
		
		$url = Util_HttpRequest::BuildQueryUrl($url, $parameters);
		$result = Util_HttpRequest::Http($url, $method,$postData);

		if(!$result){
			return false;
		}
		$result = json_decode($result,true);
		return $result;
	}
	
	public static function JsonEncode($object,$format = false){
		if(is_string($object)){
			return "\"{$object}\"";
		}
		
		if(is_numeric($object)){
			return $object;
		}
		
		$sep = '';
		if($format){
			$sep = "\n";
		}
		
		if(self::IsJsonObj($object)){
			$str = "";
			
			foreach ($object as $key => $value){
				$valueStr = self::JsonEncode($value,$format);
				$str .= "\"{$key}\":{$valueStr}," . $sep;
			}
			$str = trim($str,',');
			$str = "{{$sep}{$str}{$sep}}";
			return $str;
		}
		
		if(Util_Array::IsArrayValue($object)){
			$str = "";
				
			foreach ($object as $key => $value){
				$valueStr = self::JsonEncode($value,$format);
				$str .= "{$valueStr}," . $sep;
			}
			$str = trim($str,',');
			$str = "[{$sep}{$str}{$sep}]";
			return $str;			
		}
		
	}
	
	/**
	 * 判断是否是json对象
	 * @param array $obj
	 */
	public static function IsJsonObj($obj){
		if(is_object($obj)){
			return true;
		}
		
		if(!Util_Array::IsArrayValue($obj)){
			return false;
		}
		
		
		$index = 0;
		foreach ($obj as $key => $value){
			if(!is_numeric($index)){
				return true;
			}
			
			if($key !== $index){
				return true;
			}
			
			$index++;
		}
		return false;
	}
}