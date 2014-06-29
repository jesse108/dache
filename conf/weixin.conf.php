<?php
$config['weixin'] = array(
	'text' => array(
		'subscribe' => "谢谢关注出行助手，我们致力于提供更便捷的出行服务 。",
	),
	
	'server' => array(
		'host' => 'http://test.dachequ.com',
		'index' => '/weixin/index.php',
		'record' => '/weixin/order/list.php',
		'redirect' => '/weixin/redirect.php',
	),
		
		
		
		
		
	'default' => array(
		'token' => 'dachequ_aliyun_123',
		'server' => 'http://115.28.23.17/weixin/index.php',
	),

		
	'test2' => array(   //测试账号
		'account' => 'gh_e49469bef001',
		'token' => 'dachequ_aliyun_123',
		'app_id' => 'wxdb8336173ec871c7',
		'app_secret' => '7941cf2a07e7d4d70ed804184a8514d1',
	),
);
