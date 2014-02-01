<?php
//
ini_set('precision', 16);  //这只浮点型精度
define('ROOT_PATH', dirname(__FILE__));
define('LIB_PATH', ROOT_PATH.'/lib');
define('CLASS_PATH', ROOT_PATH.'/class');
define('CONF_PATH', ROOT_PATH.'/conf');
define('LANG_PATH', ROOT_PATH.'/lang');
define('COM_PATH', ROOT_PATH.'/common');
define('PLUGIN_PATH', ROOT_PATH.'/plugins');
define('TEMPLATE_PATH', ROOT_PATH.'/template');
define('TEMP_PATH', ROOT_PATH.'/temp');
define('CACHE_PATH',TEMP_PATH . '/cache');
define('COMPILE_PATH',TEMP_PATH . '/compile');

function classAutoload($strClassName)
{
	$strClassName = str_replace('_', '/', $strClassName);
	$libClassPath = LIB_PATH.'/'.$strClassName.'.class.php';
	$localClassPath = CLASS_PATH.'/'.$strClassName.'.class.php';
	if(file_exists($libClassPath)){
		require_once $libClassPath;
	} else if(file_exists($localClassPath)){
		require_once $localClassPath;
	}
}

spl_autoload_register('classAutoload');
header('Content-Type: text/html; charset=UTF-8;');

//////////////require
require_once COM_PATH.'/function.php'; //常用函数
require_once CONF_PATH . '/constant.class.php';
require_once PLUGIN_PATH . '/Smarty3/libs/Smarty.class.php'; //加载模板文件

Config::Load();



