<?php
/**
 * smarty插件--广告调取
 * 使用方法{ad assign="datas" adid=""}
 * 数组返回
 *
 */
function smarty_function_ad($params, &$smarty){
	$assign = isset($params['assign']) ? $params['assign'] : '';	
	$adId = isset($params['adid']) ? $params['adid'] : 0;
		
	if (!$assign || !$adId) {
		$ad = '';
		$smarty->assign($assign, $ad);//自赋值
	}
	
	$ad = WebApi_Ad::instance()->getAdByAdId($adId);
	if (empty($ad)) {
		$ad = '';
		$smarty->assign($assign, $ad);//自赋值
	}
	
	if ($ad['status'] != 1) {
		$ad = '';
		$smarty->assign($assign, $ad);//自赋值
	}
	
		
	$ad = isset($ad['content']) ? $ad['content'] : '';
	
	/*$ad = str_replace('<p>', '', $ad);
	$ad = str_replace('</p>', '', $ad);
	$ad = str_replace("\r\n", '', $ad);*/
	
	//var_dump($smarty);
	$smarty->assign($assign, $ad);//自赋值
	
}

?>