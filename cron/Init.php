<?php
header('content-type:text/html; charset=utf-8');
date_default_timezone_set('Asia/shanghai');
define('ROOT_PATH',dirname(__FILE__));
define('LIB_PATH',ROOT_PATH.'/Lib');
define('PROJECT_CONFIG', ROOT_PATH.'/Config');
require_once(ROOT_PATH.'/Conf/Conf.php');
require_once(ROOT_PATH.'/Lib/db.php');


// 设置程序内存使用 不限制
ini_set('memory_limit',-1);

// 程序最大运行时间 设置成 不限制
ini_set('max_execution_time',0);
//set_time_limit(0);

// 显示报错
ini_set('display_errors',1);
error_reporting(E_ALL ^ E_NOTICE);


function __autoload($className) {
	if(strpos($className,'_') !== false) {
		$classNameArr = explode('_',$className,2);
		$prefix = $classNameArr[0];
		$class = $classNameArr[1];
		$path = ROOT_PATH.DIRECTORY_SEPARATOR.$prefix.DIRECTORY_SEPARATOR.$class.'.php';
		if(file_exists($path)) {
			require_once($path);
			return true;
		}
		return false;
	}
	$prefix = 'Lib';
	$path = ROOT_PATH.DIRECTORY_SEPARATOR.$prefix.DIRECTORY_SEPARATOR.$className.'.php';
	if(file_exists($path)) {
		require_once($path);
		return true;
	}
	return false;
}

?>