<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';
Lib_Order_Manage::CallOrders();//  呼叫订单电话

Lib_Order_Manage::RefuseTimeoutOrders();// 处理超时电话
