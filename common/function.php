<?php
//常用函数类

/**
 * var_dump的升级版 详细查看某个变量的详情 可以递归查看数组型变量以及对象
 *
 * @param mix $var 要查看的变量
 * @param boolean $echo 是否打印
 * @param string $label 开头标签 区分不同变量用
 * @param boolean $strict 底层调用函数  是var_dump 还是 print_r true 则使用var_dump
 */
function dump($var, $echo=true,$label=null, $format = 'var_dump')
{
	$label = ($label===null) ? '' : rtrim($label) . ' ';
	
	switch ($format){
		case 'var_dump':
			ob_start();
			var_dump($var);
			$output = ob_get_clean();
			if(!extension_loaded('xdebug')) {
				$output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
				$output = '<pre>'. $label. htmlspecialchars($output). '</pre>';
			}
			break;
		case 'print_r':
			if (ini_get('html_errors')) {
				$output = print_r($var, true);
				$output = "<pre>".$label.htmlspecialchars($output)."</pre>";
			} else {
				$output = $label . " : " . print_r($var, true);
			}
			break;
		case 'json':
			$output = "<pre> $label".json_encode($var)." </pre>";
			break;
	}
	
	
	if ($echo) {
		echo($output);
		return null;
	}else
		return $output;
}