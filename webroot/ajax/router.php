<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';

$action = $_REQUEST['action'];

switch ($action){
	case 'get_destination':
		$libRouter = new Lib_Router();
		$departureID = $_REQUEST['departure_id'];
		$destinations = $libRouter->getDestination($departureID);
		dump($destinations);
		break;
}