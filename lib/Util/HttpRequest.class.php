<?php
/**
 * 使用curl 构造http请求 及相关操作
 * 
 * 注意如果要使用这个类必须确认服务器的curl处于加载状态
 * 
 * @author zhaojian@didatuan.com
 *
 */
class Util_HttpRequest {
	
	// /////////http parameter
	public static $connecttimeout = 30000; //ms
	public static $timeout = 30000;//ms
	public static $ssl_verifypeer = FALSE;
	public static $http_info;
	public static $url;
	public static $http_code;
	public static $http_response;
	public static $boundary = '';
	public static $error = '';
	public static $http_postdata = '';
	public static $request_number = 0;
	
	
	public static function Get($url, $parameters = array(), $header = array()) {
		$response = self::Request ( $url, 'GET', $parameters );
		return $response;
	}
	public static function Post($url, $parameters = array(), $header = array()) {
		$response = self::Request ( $url, 'POST', $parameters );
		return $response;
	}
	
	public static function Request($url, $method, $parameters, $header = array()) {
		$parameters = $parameters ? $parameters : array ();
		if (strrpos ( $url, 'http://' ) !== 0 && strrpos ( $url, 'https://' ) !== 0) {
			self::$error = 'Request fail for url is not correct!';
			return false;
		}
		
		switch ($method) {
			case 'GET' :
				$url = self::BuildQueryUrl ( $url, $parameters);
				return self::Http ( $url, 'GET' ,null, $header);
			case 'POST' :
				$postStr = self::BuildHttpQuery($parameters);
				return self::Http ( $url, $method, $postStr,$header);
				break;
		}
	}
	
	
	public static function Http($url, $method, $postFields= null, $header = array()) {
		self::$request_number ++;
		$ci = curl_init ();
		
		curl_setopt ( $ci, CURLOPT_CONNECTTIMEOUT_MS, self::$connecttimeout ); // 链接超时
		curl_setopt ( $ci, CURLOPT_TIMEOUT_MS, self::$timeout );
		curl_setopt ( $ci, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt ( $ci, CURLOPT_SSL_VERIFYHOST, FALSE); //cloopen 设置
		curl_setopt ( $ci, CURLOPT_SSL_VERIFYPEER, self::$ssl_verifypeer );
		curl_setopt ( $ci, CURLOPT_HEADER, FALSE );
		curl_setopt ( $ci, CURLOPT_HTTPHEADER, $header );
		curl_setopt ( $ci, CURLINFO_HEADER_OUT, TRUE );
		curl_setopt ( $ci, CURLOPT_URL, $url );
		
		switch ($method) {
			case 'POST' :
				curl_setopt ( $ci, CURLOPT_POST, TRUE );
				if ($postFields) {
					curl_setopt ( $ci, CURLOPT_POSTFIELDS, $postFields );
				}
				break;
			case 'DELETE' :
				curl_setopt ( $ci, CURLOPT_CUSTOMREQUEST, 'DELETE' );
				if ($postFields) {
					$url = "{$url}?{$postFields}";
				}
				break;
		}
		
		
		$response = curl_exec ( $ci );
		
		
		self::$http_code [self::$request_number] = curl_getinfo ( $ci, CURLINFO_HTTP_CODE );
		self::$http_info [self::$request_number] = curl_getinfo ( $ci );
		self::$url [self::$request_number] = $url;
		self::$http_response [self::$request_number] = $response;
		self::$http_postdata [self::$request_number] = $postFields;
		
		if(!$response){
			self::$error = curl_error($ci);
		}
		
		curl_close ( $ci );
		return $response;
	}

	public static function BuildHttpQuery($params) {
		if (!$params){
			return '';
		}
		uksort ( $params, 'strcmp' );
		
		$pairs = array ();
		foreach ( $params as $parameter => $value ) {
			if (is_array ( $value )) {
				natsort ( $value );
				foreach ( $value as $duplicate_value ) {
					$pairs [] = $parameter . '[]=' . $duplicate_value;
				}
			} else {
				$pairs [] = $parameter . '=' . $value;
			}
		}
		return implode ( '&', $pairs );
	}
	
	/**
	 * 构造get请求URL
	 * 
	 * @param sting $url        	
	 * @param array $parameters        	
	 * @return string
	 */
	public static function BuildQueryUrl($url, $parameters) {
		$url = trim ( $url );
		$url_info = parse_url ( $url );
		$port = $url_info ['port'];
		$scheme = $url_info ['scheme'];
		$host = $url_info ['host'];
		$path = $url_info ['path'];
		$query = $url_info ['query'];
		
		$url = "$scheme://$host$path";
		if ($port) {
			$url = "$url:$port$path";
		}
		
		$para_str = self::BuildHttpQuery ( $parameters );
		
		if ($query && $para_str) {
			$query .= "&{$para_str}";
		} else if ($para_str) {
			$query = $para_str;
		}
		if ($query) {
			$url = $url . '?' . $query;
		}
		return $url;
	}
	
	
	
	
	//--------------------------------------------------
	/**
	 * debug 用函数 查看上次请求的详细情况
	 */
	public static function EchoHttpInfo($last_only = false) {
		if ($last_only) {
			self::echoLastHttpInfo ();
			return true;
		}
		echo '-------------------http info----------------------<br>';
		dump ( self::$http_info );
		dump ( self::$http_code );
		echo '-------------------http response----------------------<br>';
		dump ( self::$http_response );
		echo '-------------------http post----------------------<br>';
		dump ( self::$http_postdata );
	}
	
	public static function EchoLastHttpInfo() {
		$format = '-------------------%s (%s)----------------------<br>';
		
		printf ( $format, 'http info', self::$request_number );
		dump ( self::$http_info [self::$request_number] );
		
		printf ( $format, 'http response', self::$request_number );
		dump ( self::$http_response [self::$request_number] );
		
		printf ( $format, 'http post', self::$request_number );
		dump ( self::$http_postdata [self::$request_number] );
	}
}