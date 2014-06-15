<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';


$orderID = 68;

$dbOrder = new DB_Order();
$order = $dbOrder->fetch($orderID);


Lib_Order_Manage::CallOneOrder($order);