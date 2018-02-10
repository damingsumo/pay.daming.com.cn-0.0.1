<?php
/**
 * smarty插件自动生成URL
 * 使用方法{url action="docs/list" params=$array}
 * 生成的格式如:http://www.phpff.com/docs/list?uid=2&type=category
 *
 */
function smarty_function_url($params){
	$action = isset($params['action']) ? $params['action'] : '';
	$url = HOME_URL.$action;
	return $url;
}

?>