<?php
$cloopenConfig = array( //主账号
	'rest_url' => 'https://sandboxapp.cloopen.com:8883',
	'sid' => 'aaf98fda4486da2c01448aab2d3501e5',
	'token' => 'b1e05c651363453fb90941f5bd5c1e61',
	'app_id' => 'aaf98fda44fd64490144fdbc31ee006f',
	'soft_version' => '2013-12-26',
);

$subAccount = array( //默认子账号
	'sid' => '1e09396ab59011e389eed89d672b9690',
	'token' => '4608f09621ea02146c0a0bb33ff20b4a',
	'voip_account' => '81186100000002',
	'voip_pwd' => 'osYiq3PQ',
);

$cloopenConfig['sub_account'] = $subAccount;

$config['cloopen'] = $cloopenConfig;