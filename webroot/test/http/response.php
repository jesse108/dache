<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';


dump($GLOBALS['HTTP_RAW_POST_DATA']);
dump($_POST);

dump(file_get_contents("php://input") );