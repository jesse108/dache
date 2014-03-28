<?php
/**
 * 语音开放平台请求处理
 */

$result = file_get_contents("php://input");

Log::Set($result,$type=2);

