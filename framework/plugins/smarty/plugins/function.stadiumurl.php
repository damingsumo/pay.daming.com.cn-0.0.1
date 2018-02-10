<?php
/**
 * smarty插件stadium的使用
 * 使用方法{stadiumurl name=""}
 * 生成的格式如: http://mgr.phpff.com/docs/list?uid=2&type=category
 *
 */
function smarty_function_stadiumurl($params) {
	$name = isset($params['name']) ? $params['name'] : '';
	
	$wechatId = http::SESSION($name);
	return '/stadium/detail?wd='.$wechatId;
}
