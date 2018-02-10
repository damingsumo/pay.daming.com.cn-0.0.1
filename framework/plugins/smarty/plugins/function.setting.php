<?php
/**
 * 
 * {setting key='INDEX-ABC-EFT'"}
 *
 */
function smarty_function_setting($params){
	
	$key = isset($params['key']) ? strtoupper($params['key']) : '';
	if(empty($key)) {
		return '';
	}
	
	$code = Webapi_Setting::instance()->getCode($key);
	return $code;

}

?>