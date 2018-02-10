<?php
/**
 * smarty插件--场馆地区调去调取
 * 使用方法{split pattern="-" string="北京-北京-海淀区"}
 * 返回字符串
 *
 */
function smarty_function_split($params){
	$pattern = isset($params['pattern']) ? $params['pattern'] : '';	
	$string = isset($params['string']) ? $params['string'] : '';
		
	if (!$pattern || !$string) {
		return '';
	}
	
	$reginArr = explode("$pattern", $string);
	return isset($reginArr[count($reginArr)-1]) ? $reginArr[count($reginArr)-1] : '';
	
}

?>