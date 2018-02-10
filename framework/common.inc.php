<?php
//常用不变常量配置
require_once(FW_PATH."/conf/global.config.php");
// 注册自动加载
spl_autoload_register('__loadclass');
$enviroment = isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : '';
if($enviroment == '') {
    return Response::output('500','环境变量未配置');
}
//配置文件地址
define('CONFIG_ROOT', FW_PATH.'/conf/'.$enviroment.'/');
//网站配置文件路径
require_once(CONFIG_ROOT.'/config.php');
//domain配置
require_once(CONFIG_ROOT.'/domain.config.php');

if(isset($_SERVER['REQUEST_URI_REWRITE'])) {
	$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI_REWRITE'];
}

/**
 * Load class file from local disk
 *
 * @param string $class_name
 * @return bool true if success
 */
function __loadclass($class_name){
 	$file = strtolower(str_replace('_', '/', $class_name));
    $path = FW_PATH.'/core/'.$file.'.class';
    $path .= '.php';
    if(file_exists($path)) {
    	require_once($path);
        return true;
    }
 	$path = WWW_ROOT.'/'.$file;
    $path .= '.php';

    if(file_exists($path)) {
    	require_once($path);
        return true;
    }
    return false;
}

//全局加载网站配置文件
function config($key, $key2 = '') {
	global $defaultConfigMapping;
	if(array_key_exists($key, $defaultConfigMapping)) {
		$tmpConfigName = $defaultConfigMapping[$key];
		global $$tmpConfigName;
		$config = $$tmpConfigName;
		if(is_array($config) && !empty($key2)) {
			return isset($config[$key2]) ? $config[$key2] : "";
		} else {
			return $config;
		}
	}
	return '';
}

//全局用户自定义排序
function my_sort($a, $b){
	  if ($a['path'] == $b['path']) 
	  return 0;
	  return ($a['path'] < $b['path']) ? -1 : 1;
}
?>
