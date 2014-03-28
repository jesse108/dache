<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';


$body = array('id' => 123,'name' => 'jesse');
//$body = Util_HttpRequest::BuildHttpQuery($body);
$body = json_encode($body);

$url = "http://local.dachequ.com/test/http/response.php";

$header = array("Accept:application/json","Content-Type:application/json;charset=utf-8");
$header = array();
$result = Util_HttpRequest::Http($url, 'POST',$body,$header);

Util_HttpRequest::EchoHttpInfo();

// echo $result;