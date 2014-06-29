<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';

$location = Lib_Location::GetAllLocatoin();
$location = Util_Array::FormatInTree($location);


dump($location);