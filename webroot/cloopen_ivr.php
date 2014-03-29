<?php
/**
 * 处理cloopen的ivr请求
 */

include_once dirname(dirname(__FILE__)).'/app.php';

Lib_Cloopen_IVRHandler::HandleRequest();