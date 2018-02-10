<?php
/**
 * smarty插件stadium的使用
 * 使用方法{userindex name=""}
 * 生成的格式如: http://mgr.phpff.com/docs/list?uid=2&type=category
 *
 */
function smarty_function_userindex($params) {
	$name = isset($params['name']) ? $params['name'] : '';
	
	$wechatId = http::SESSION($name);
	return !empty($wechatId) ? '/user/index?wechat_id='.$wechatId : '/user/index';
}
