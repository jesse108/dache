<?php
include_once dirname(dirname(dirname(dirname(__FILE__)))).'/app.php';

$departure = $_GET['departure'];
if(!$departure){
	Lib_System::SetError('请先选择出发地');
	Utility::Redirect('/weixin/index.php');
}


$libRouter = new Lib_Router();

$cities = $libRouter->getDestination($departure);

Template::Show();