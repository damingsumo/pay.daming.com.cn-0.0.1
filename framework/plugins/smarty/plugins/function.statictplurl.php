<?php
/**
 * smarty插件stadium的使用
 * 使用方法{statictplurl name=""}
 * 生成的格式如: http://mgr.phpff.com/docs/list?uid=2&type=category
 *
 */
function smarty_function_statictplurl($params) {
	$name = isset($params['name']) ? $params['name'] : '';
	
	$wechatId = http::SESSION($name);
	$statictpls = WebApi_Statictpl::instance()->getStatictplsByParams(array('wechat_id' => $wechatId), 1, -1);
	$statictpl = !empty($statictpls) ? current($statictpls) : array();
	$url = !empty($wechatId) ? '/index?wechat_id='.$wechatId : '/index';
	return isset($statictpl['url']) ? $statictpl['url'] : $url;
}
