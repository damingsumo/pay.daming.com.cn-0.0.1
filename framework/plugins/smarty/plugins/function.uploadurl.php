<?php
/**
 * 上传地址生成的URL地址
 * 使用方法{uploadurl module="mgr" sort="head"}
 * 生成的格式如: http://mgr.phpff.com/docs/list?uid=2&type=category
 *
 */
function smarty_function_uploadurl($params){
	$module = isset($params['module']) ? $params['module'] : '';
	$sort = isset($params['sort']) ? $params['sort'] : '';
	
	$url = FILE_SERVER.'?module='.$module.'&sort='.$sort;
	return $url;
}

?>