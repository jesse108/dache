<?php
/*
 *  Copyright (c) 2013 The CCP project authors. All Rights Reserved.
 *
 *  Use of this source code is governed by a Beijing Speedtong Information Technology Co.,Ltd license
 *  that can be found in the LICENSE file in the root of the web site.
 *
 *   http://www.cloopen.com
 *
 *  An additional intellectual property rights grant can be found
 *  in the file PATENTS.  All contributing project authors may
 *  be found in the AUTHORS file in the root of the source tree.
 */
  //获取POST数据
  $result = file_get_contents("php://input");
  //读取日志文件
  $filename="../log.txt";
  $handle = fopen($filename, 'a'); 
  //写入数据
  fwrite($handle,date("Ymd H:i:s")."\n");
  fwrite($handle,"result:".$result."\n"); 
  //解析XML
  $xml = simplexml_load_string(trim($result," \t\n\r"));
  //获取XML数据
  $action = $xml->action;
  $type = $xml->type;
  $orderid = $xml->orderid;
  $subid = $xml->subid;
  $caller = $xml->caller;
  $called = $xml->called; 
  $starttime = $xml->starttime; 
  $endtime = $xml->endtime; 
  $billdata = $xml->billdata; 
  $subtype = $xml->subtype; 
  $callSid = $xml->callSid; 
  $recordurl = $xml->recordurl; 
  $byetype = $xml->byetype; 
  //写入解析后数据
  fwrite($handle,date("Ymd H:i:s")."\n");
  fwrite($handle,"action:".$action." type:".$type." orderid:".$orderid." subid:".$subid." caller:".$caller." called:".$called." starttime:".$starttime." endtime:".$endtime." billdata:".$billdata." subtype:".$subtype." callSid:".$callSid." recordurl:".$recordurl." byetype:".$byetype."\n"); 
  //TODO 请在此处增加逻辑判断代码
       
  $strXML="<?xml version='1.0' encoding='utf-8'?>
              <Response>
              <statuscode>0000</statuscode>        
              <statusmsg>状态描述信息</statusmsg>
              <record>1</record>
              </Response>";     
  echo $strXML; 
 ?>