<?php
/**
 * @package     Framework
 * @copyright   bestdo.com
 * @author      liu
 * @version    	2013-5-27
 */

/**
 * url Class
 *
 */
class url {
	
	static function image($image) {
		return STATIC_URL . 'images/' . $image;
	}
	
	static function js($js) {
		return STATIC_URL . 'js/' . $js;
	}
	
	static function css($css) {
		return STATIC_URL . 'css/' . $css;
	}
	
	/**
	 * 框架自动生成URL方法
	 * smarty 插件也会调用此方法
	 * @param string $entity 模块名
	 * @param array $params 参数数据
	 * @param string $prefix 前缀
	 * @param string $domain 域名
	 */
	public static function make($entity, $params = array(), $prefix = '', $domain = '') {
		$url = '';
		
		if($domain == '' && $prefix == '') {
			$url = HOME_URL.$entity;
		} else {
			if($domain == '') {
				$domain = DOMAIN;
			}
			
			if(defined('BD_TEXT') && BD_TEXT==true) {
				$url = 'http://test.'.$prefix.'.'.$domain.'/'.$entity;
			} else {
				if($prefix == '') {
					$url = 'http://'.$domain.$entity;
				} else {
					$url = 'http://'.$prefix.'.'.$domain.'/'.$entity;
				}
			}
		}
		
		if(is_array($params) && !empty($params)) {
			$url .= PARAMS_SEPARATOR.http_build_query($params);
		} else {
			if(!empty($params)) {
				$url .= PARAMS_SEPARATOR.$params;
			}
		}
		return $url;
	}
	
	/**
	 * 框架后台统一URL生成路径
	 *
	 * @param string $entity 模块
	 * @param array $params 参数
	 * @return string url
	 */
	public static function makemgr($entity, $params = array()) {
		return self::make ( $entity, $params );
	}
}
?>