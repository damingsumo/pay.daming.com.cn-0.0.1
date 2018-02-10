<?php
/**
 * smarty插件--标签调取
 * 使用方法{position assign="datas" posid=""}
 * 数组返回
 *
 */
function smarty_function_position($params, &$smarty){
	$assign = isset($params['assign']) ? $params['assign'] : '';	
	$posId = isset($params['posid']) ? $params['posid'] : 0;
	$num = isset($params['num']) ? $params['num'] : 0;
	//城市id、运动类型id--在推荐位标签的时候用，其他的不起作用
	$cityId = isset($params['cityId']) ? $params['cityId'] : 0;
	$cid = isset($params['cid']) ? $params['cid'] : 0;

	//city="auto"
	
	if (!$assign || !$posId) {
		$res = array();
		$smarty->assign($assign, $res);//自赋值
	}
	
	$params = array();//置空前台传入的值
	
	$pos = WebApi_Position::instance()->getPositionByPositionId($posId);
	$num = $num > 0 ? $num : $pos['num'];//调去数量
	if (empty($pos)) {
		$res = array();
		$smarty->assign($assign, $res);//自赋值
	}
	
	if ($pos['status'] == -1) {//没有审核的推荐位
		$res = array();
		$smarty->assign($assign, $res);//自赋值
	}
	
	//$params['num'] = $num;
	
	/*
	判断该推荐位下有没有cityid 和cid 
	没有：说明这个推荐位不限定城市和运动类型 按照传过来的cityid 和cid 去推荐位和模型关联表中去调取符合条件的模型id 
	有：直接调去推荐位和模型关联表中去调取符合条件的模型id 
	*/

	if ($cityId == 'auto') {
		$cityId = http::COOKIE('cityid') > 0 ? http::COOKIE('cityid') : 52;//默认北京场馆
		//$cityId =  52;//默认北京场馆
	}

    $cityId = isset($_GET['city_id']) ? intval($_GET['city_id']) : $cityId;//接受城市参数--兼容模式

	$cityId = $pos['city_id'] > 0 ? $pos['city_id'] : $cityId;//城市id
	$cid = $pos['cid'] > 0 ? $pos['cid'] : $cid;//运动类型id
	
	if ($cityId) {//有城市限制的时候 在传参数限制城市
		$params['city_id'] = $cityId;
	}
	
	if ($cid) {//有运动类型限制的时候 在传参数限制运动类型
		$params['cid'] = $cid;
	}
	
	
	$model = $pos['model'];
	$params['position_id'] = $posId;//推荐位id

    $api = new Baseapi();
	//现在是把推荐位子表分成三种
	if ($model == 1) {//doc 目前没有cid和cityid 的限制
		$res = array();
	} elseif ($model = 2) {//stadium
		/*步骤1 通过推荐位场馆关联表回去 该推荐位下绑定的场馆信息(场馆ids)
		步骤2 通过公用API接口回调出场馆数据(包括服务等数据 看前台需求)
		步骤3 组装数据返回页面*/
		//步骤1
		//var_dump($params);exit;
		$positionStadiums = WebApi_Position_Stadium::instance()->getCmsStadiumsByParams($params, $num);
		if (empty($positionStadiums)) {
			$smarty->assign($assign, array());//自赋值
		}
		
		$stadiumIds = array();
        if(!empty($positionStadiums)) {
            foreach ($positionStadiums as $positionStadium) {
                $stadiumIds[] = $positionStadium['stadium_id'];
            }
        }
		//步骤2 sdk获取场官信息
		$stadiumResTem = array();
		$stadiumRes = $api->getStadiums($stadiumIds);
        if($stadiumRes['status'] != 200) {
            $smarty->assign($assign, array());//自赋值
        }

        $stadiums = isset($stadiumRes['data']) ? $stadiumRes['data'] : array();
        $stadiumsTem = array();
        $cityPositionIds = array();
        if(!empty($stadiums)) {
            foreach ($stadiums as $key => $stadium) {
                //组装商圈ids
//                print_r($stadium['is_book']);
				$isShow = isset($stadium['is_show']) ? $stadium['is_show'] : NULL;
                if($isShow == 1) {
                    $cityPositionIds[] = $stadium['city_position_id'];
                    $stadiumsTem[$key] = $stadium;
                }
            }
        }

        $cityPositions = $api->getCityPositionsByIds($cityPositionIds);
        if(!empty($stadiumsTem)) {
            foreach ($stadiumsTem as $key => $stadiumTem) {

                unset($stadiumTem['venues'], $stadiumTem['services']);

                //商圈
                $stadiumTem['city_position_name'] = isset($cityPositions['data'][$stadiumTem['city_position_id']]['name']) ? $cityPositions['data'][$stadiumTem['city_position_id']]['name'] : '';
                $stadiumResTem[$key] = $stadiumTem;
            }
        }

		//步骤3 
		$res = array();
        if(!empty($positionStadiums)) {
            foreach ($positionStadiums as $key => $positionStadium) {
                if(isset($stadiumResTem[$positionStadium['stadium_id']])) {
                    $res[$key]['info'] = $positionStadium;
                    $res[$key]['extend'] = $stadiumResTem[$positionStadium['stadium_id']];
                }

            }
        } else {
            $res = array();
            //$res[$key]['extend'] = array();
        }
//        echo '<pre>';
//		print_r($res);
	} elseif ($model == 3) {//coach
		$res = array();
	}

	//var_dump($smarty);
	$smarty->assign($assign, $res);//自赋值
	
}

?>