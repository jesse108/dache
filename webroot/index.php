<?php
include_once dirname(dirname(__FILE__)).'/app.php';
$orderID = 6;

$obj = new Lib_Order_Business();

Template::Show();