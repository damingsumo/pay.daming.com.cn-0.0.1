<?php
/**
 * smarty插件调取出不同运动类型的动态参数
 * 使用方法{property cid=1 properties=array()}
 * 生成的格式如:
 *
 */
function smarty_function_property($params){
    $cid = isset($params['cid']) ? $params['cid'] : 0;
    $properties = isset($params['properties']) ? $params['properties'] : array();
    //var_dump($cid, $properties);exit;
    $format = isset($params['format']) ? $params['format'] : 'span';
    //每中运动类型输出的动态参数数组 cid =》 array(prooertyId)
    //网球（场地类型 场地片数） 乒乓球（场地规格）羽毛球（场地片数、灯光）
    $outputArr = array(
        1 => array(83,88,134),//网球
        8 => array(86,87,90,130,128),//高尔夫
        11 => array(101,100,103,133),//篮球
        18 => array(110,111,135),//乒乓球（场地规格）？
        19 => array(94),//羽毛球 -差一个灯光
        20 => array(114,115,136,140),//高尔夫联系场
        21 => array(116),//健身
        22 => array(121,142,164,143),//游泳
        24 => array(144,161)//康体
    );

    $outputProperArr = isset($outputArr[$cid]) ? $outputArr[$cid] : array();
    if(empty($outputProperArr) || empty($properties)) {
        return '';
    }

    $outputStr = '';
    foreach($properties as $property) {
        if(in_array($property['stadium_venue_category_property_id'], $outputProperArr)) {
            $value = $property['value'] != '' ? $property['value'] : '未知';
            $outputStr .= '<'.$format.'>'.$property['name'].'：'.$value.'</'.$format.'>';
        }
    }

	return $outputStr;
}

?>