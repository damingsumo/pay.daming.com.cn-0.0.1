<?php
/**
 * 网站核心工作类
 * Project::config();
 */
class Project {
	/**
	 * 自动加载配置文件
	 *
	 * @param string $configFileName
	 */
	public static function config($configFileName, $key1 = '', $key2 = '') {
		$fileName = PROJECT_CONFIG.'/'.$configFileName.'.php';
		$data = self::load($fileName);
		
		if(empty($data)) {
			return '';
		}
		if(is_array($data) && $key1 != '') {
			$tmp = isset($data[$key1]) ? $data[$key1] : '';
			if(is_array($tmp) && $key2 != '') {
				return isset($tmp[$key2]) ? $tmp[$key2] : '';
			}
			return $tmp;
		}
		return $data;
	}
	
	
	public static  function load($file) {
		if(file_exists($file)) {
			return require($file);
		}
		return array();
	}
}

?>