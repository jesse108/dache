<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';


$body = array('id' => 123,'name' => 'jesse');
$body = json_encode($body);

$body = '<?xml version="1.0" encoding="UTF-8"?>
<Request>
    <action>SellingCall</action>
    <number>13800000000</number>
    <callSid>1307241452320369000100030000002f</callSid>
    <state>0</state>
    <duration>30</duration>
</Request>';


$url = "http://local.dachequ.com/test/http/response.php";

$header = array("Accept:application/json","Content-Type:application/json;charset=utf-8");



$result = Util_HttpRequest::Http($url, 'POST',$body,$header);

//Util_HttpRequest::EchoHttpInfo();

echo $result;