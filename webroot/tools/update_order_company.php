<?php
include_once dirname(dirname(dirname(__FILE__))).'/app.php';

$dbOrder = new DB_Order();
$condition = array(
	'(status = 1 or status = 10)',
);

$orders = $dbOrder->get($condition);
$orderIDs = Util_Array::GetColumn($orders, 'id');


$dbOrderTrack = new DB_OrderTrack();
$condition = array(
	'order_id' => $orderIDs,
	'status' => DB_OrderTrack::STATTUS_ACCEPT,
);
$orderTracks = $dbOrderTrack->get($condition);
$orderTracks = Util_Array::AssColumn($orderTracks, 'order_id');

DB::Debug(true);
foreach ($orders as $order){
	$orderTrack = $orderTracks[$order['id']];
	if($orderTrack){
		$updateRow = array(
			'company_id' => $orderTrack['company_id'],
		);
		$dbOrder->update(array('id' => $order['id']), $updateRow);
	}
}