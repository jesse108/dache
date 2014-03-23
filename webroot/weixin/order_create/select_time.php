<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';

$currentTime = time();

$showTimeArray = Lib_OrderTemp::getSelectTimeInfo();

Template::Show();
