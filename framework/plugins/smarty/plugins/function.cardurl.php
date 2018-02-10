<?php
/**
 * 所有卡验证中心的地址
 * smarty插件自动生成后台URL
 * 使用方法{cardurl action="docs/list" params=$array}
 * 生成的格式如: http://mgr.phpff.com/docs/list?uid=2&type=category
 *
 */
function smarty_function_cardurl($params){
	$url = CARD_SERVER;
	return $url;
}

?>