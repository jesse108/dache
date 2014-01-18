<?php
include_once dirname(dirname(__FILE__)).'/app.php';

$user = new DB_User();
$user->create();



//Template::Show();