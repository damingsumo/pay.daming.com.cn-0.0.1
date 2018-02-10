<?php
/**
 * 
 * {pages action='docs/text'}
 *
 */
function smarty_function_pages($params){
	$url = PAGES_URL;
	$action = isset($params['action']) ? $params['action'] : '';
	if($action == '') {
		return $url;
	}
	
	$url .= $action;
	return $url;
}

?>