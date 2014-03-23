<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';

$orderID = $_GET['order_id'];

$libOrder = new Lib_Order();

$order = $libOrder->getOrderInfo($orderID);
$orderShow = Lib_Order::GetReadableOrder($order);
$orderTrack = $libOrder->getOrderTrack($orderID);


Template::Show();