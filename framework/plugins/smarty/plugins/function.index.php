<?php
/**
 * 
 * {index action='docs/text'}
 *
 */
function smarty_function_index($params){
	$url = INDEX_URL;
	$action = isset($params['action']) ? $params['action'] : '';
	if($action == '') {
		return $url;
	}
	
	$url .= $action;
	return $url;
}

?>