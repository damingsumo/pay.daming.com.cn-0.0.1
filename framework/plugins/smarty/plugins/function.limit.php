<?php
/**
 * smarty插件自动劫取字符串
 * 使用方法{limit str= 没有女朋友呵呵 length = 27 end = '...' }
 * 生成的格式如:http://www.phpff.com/docs/list?uid=2&type=category
 *
 */
function smarty_function_limit($params){
    $str = isset($params['str']) ? $params['str'] : '';

    $length = isset($params['length']) ? $params['length'] : 8;
    $end = isset($params['end']) ? $params['end'] : '...';
	$getlength = strlen($str);
    if($getlength > $length) {
        $newData = mb_substr($str, 0, $length,'utf-8');
        return $newData.''.$end;
    }
    return $str;
}

?>