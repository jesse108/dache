<?php
/**
 * 使用curl 构造http请求 及相关操作
 * 
 * 注意如果要使用这个类必须确认服务器的curl处于加载状态
 * 
 * @author zhaojian@didatuan.com
 *
 */
class Util_HttpRequest{

    ///////////http parameter
    public static $connecttimeout = 30;
    public static $timeout = 30;
    public static $ssl_verifypeer = FALSE;
    public static $http_info;
    public static $url;
    public static $http_code;
    public static $http_response;
    public static $boundary = '';
    public static $error = '';
    public static $http_postdata = '';
    public static $request_number = 0;
    /**
     * 构造get请求
     * 
     * 默认会将返回的数据用json进行解码
     * 若不想解码请将本类的self::$decode_json 设为false
     * 
     * @param string $url 请求url
     * @param array $parameters 需要传递的参数
     * @return mix 请求结果
     */
    public static function Get($url, $parameters = array()) {
        $response = self::Request($url, 'GET', $parameters);
        return $response;
    }
    
    /**
     * post请求
     * 
     * 默认会将返回的数据用json进行解码
     * 若不想解码请将本类的self::$decode_json 设为false
     * 
     * @param string $url 请求url
     * @param array $parameters 需要传递的参数
     * @param boolean $multi 是否有图片数据
     * @return mix 请求结果
     */
    public static function Post($url, $parameters = array() , $multi = false) {    
        $response = self::Request($url, 'POST', $parameters , $multi );
        return $response;
    }
    
    /**
     * 构造http请求
     * 
     * @param string $url 请求url
     * @param string $method 请求方式 GET/POST
     * @param array $parameters 请求需要传递的参数
     * @param boolean $multi 是否有图片数据
     * @return mix 请求结果
     */
    public static function Request($url, $method, $parameters , $multi = false){
        $parameters = $parameters ? $parameters : array();
        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) {
            self::$error = 'Request fail for url is not correct!';
            return false;
        }
        switch($method){
            case 'GET':
                    $url = self::buidQueryUrl($url, $parameters);
                    return self::http($url, 'GET');
            case 'POST':
                return self::http($url, $method, self::to_postdata($parameters,$multi) , $multi );
                break;
        }
    }
    /**
     * 构造HTTP请求 底层函数
     *
     * @param string $url 请求url
     * @param string $method 请求方式POST/GET
     * @param string $postfields post域传送的参数
     * @param boolean $multi 是否有图片参数数据
     * @return 请求结果
     */
    public static function http($url, $method, $postfields = NULL , $multi = false) {
        self::$request_number ++;
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, self::$connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, self::$timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, self::$ssl_verifypeer);
    
//         curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
    
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
    
        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                }
                break;
            case 'DELETE':
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if (!empty($postfields)) {
                    $url = "{$url}?{$postfields}";
                }
                break;
        }
    
        $header_array =array();
        if( $multi )
            $header_array = array("Content-Type: multipart/form-data; boundary=" . self::$boundary , "Expect: ");
    
    
            curl_setopt($ci, CURLOPT_HTTPHEADER, $header_array );
            curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );
            curl_setopt($ci, CURLOPT_URL, $url);
    
            $response = curl_exec($ci);
            self::$http_code[self::$request_number] = curl_getinfo($ci, CURLINFO_HTTP_CODE);
            self::$http_info[self::$request_number] = curl_getinfo($ci);
            self::$url[self::$request_number] = $url;
            self::$http_response[self::$request_number]=$response;
            self::$http_postdata[self::$request_number] = $postfields;
            curl_close ($ci);
            return $response;
    }
    
    function getHeader($ch, $header) {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
                    $value = trim(substr($header, $i + 2));
                    self::$http_header[$key] = $value;
        }
        return strlen($header);
   }
   
   public static function to_postdata($data,$multi = false){
       if($multi){
           return self::build_http_query_multi($data);
       } else {
           return self::build_http_query($data);
       }
   }
   
   public static function build_http_query($params) {
       if (!$params) return '';
   
       uksort($params, 'strcmp');
   
       $pairs = array();
       foreach ($params as $parameter => $value) {
           if (is_array($value)) {
               natsort($value);
               foreach ($value as $duplicate_value) {
                   $pairs[] = $parameter . '=' . $duplicate_value;
               }
           } else {
               $pairs[] = $parameter . '=' . $value;
           }
       }
       // For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
       // Each name-value pair is separated by an '&' character (ASCII code 38)
       return implode('&', $pairs);
   }
   
   public static function build_http_query_multi($params) {
       if (!$params) return '';
       $keys = array_keys($params);
       $values = array_values($params);
       $params = array_combine($keys, $values);
       uksort($params, 'strcmp');
       $pairs = array();
   
       self::$boundary = $boundary = uniqid('------------------');
       $MPboundary = '--'.$boundary;
       $endMPboundary = $MPboundary. '--';
       $multipartbody = '';
   
       foreach ($params as $parameter => $value) {
           if( in_array($parameter,array("pic","image")) && $value{0} == '@' )
           {
               $url = ltrim( $value , '@' );
               $content = file_get_contents( $url );
               $filename = reset( explode( '?' , basename( $url ) ));
               $mime = self::get_image_mime($url);
   
               $multipartbody .= $MPboundary . "\r\n";
               $multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
               $multipartbody .= 'Content-Type: '. $mime . "\r\n\r\n";
               $multipartbody .= $content. "\r\n";
           }
           else
           {
               $multipartbody .= $MPboundary . "\r\n";
               $multipartbody .= 'content-disposition: form-data; name="'.$parameter."\"\r\n\r\n";
               $multipartbody .= $value."\r\n";
           }
       }
       $multipartbody .=  $endMPboundary;
       return $multipartbody;
   }
   
   public static function buidQueryUrl($url,$parameters){
       $url_info = parse_url($url);
       $port = $url_info['port'];
       $scheme = $url_info['scheme'];
       $host = $url_info['host'];
       $path = $url_info['path'];
       $query = $url_info['query'];
       $url = "$scheme://$host$path";
       if($port){
       		$url = "$scheme://$host:$port$path";
       } 
       $para_str = self::build_http_query($parameters);
       if($query && $para_str ){
           $query .= "&{$para_str}";
       } else if($para_str){
           $query = $para_str;
       }
       if($query){
           $url  = $url .'?'.$query;
       }
       return $url;
   }
   
   /**
    * debug 用函数 查看上次请求的详细情况
    */
   public static function echoHttpInfo($last_only = false){
       if($last_only){
           self::echoLastHttpInfo();
           return true;
       }
       echo '-------------------http info----------------------<br>';
       dump(self::$http_info);
       echo '-------------------http response----------------------<br>';
       dump(self::$http_response);
       echo '-------------------http post----------------------<br>';
       dump(self::$http_postdata);
   }
   
   public static function echoLastHttpInfo(){
       $format = '-------------------%s (%s)----------------------<br>';
       
       printf($format, 'http info', self::$request_number);
       dump(self::$http_info[self::$request_number]);
       
       printf($format, 'http response', self::$request_number);
       dump(self::$http_response[self::$request_number]);
       
       printf($format, 'http post', self::$request_number);
       dump(self::$http_postdata[self::$request_number]);
   }
   
   
   /**
    * 平台上报项目, 只是适用于数据格式为xml的
    * @param string $url 上报的url
    * @param array $postdata 上报的数据
    * @return mixed
    */
   public static function postRequestPlatform($url, $postdata){
       $ch = curl_init();
       $header[]="Content-Type: multipart/form-data; charset=utf-8";
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,180);
       curl_setopt($ch, CURLOPT_TIMEOUT,180);
       $result = curl_exec($ch);
       if ($result === FALSE) {
           echo "cURL Error: " . curl_errno($ch) . curl_error($ch);
       }
       curl_close($ch);
       return $result;
   }
   
   
          
}