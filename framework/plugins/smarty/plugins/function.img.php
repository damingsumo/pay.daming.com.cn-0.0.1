<?php
/**
 * smarty插件自动生成图片URL
 * 使用方法{img src= type=1}
 * 生成的格式如:http://www.phpff.com/docs/list?uid=2&type=category
 *
 */
function smarty_function_img($params){
	$src = isset($params['src']) ? $params['src'] : '';
	$type = isset($params['type']) ? $params['type'] : 2;
	$url = file::makeUrl($src, $type);
	
	return $url;
}

?>