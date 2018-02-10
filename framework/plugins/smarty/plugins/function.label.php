<?php
/**
 * smarty插件--标签调取
 * 使用方法{label assign="datas" labelid=""}
 * 数组返回
 *
 */
function smarty_function_label($params, &$smarty){
	$assign = isset($params['assign']) ? $params['assign'] : '';	
	$labelId = isset($params['labelid']) ? $params['labelid'] : 0;
	$num = isset($params['num']) ? $params['num'] : 0;
	//城市id、运动类型id--在推荐位标签的时候用，其他的不起作用
	$cityId = isset($params['cityId']) ? $params['cityId'] : 0;
	$cid = isset($params['cid']) ? $params['cid'] : 0;
	

	//city="auto"
	
	if (!$assign || !$labelId) {
		$res = array();
		$smarty->assign($assign, $res);//自赋值
	}
	
	$label = WebApi_Label::instance()->getLabelByLabelId($labelId);
	if (empty($label)) {
		$res = array();
		$smarty->assign($assign, $res);//自赋值
	}
	
	$method = $label['method'];//1模型(文档/产品)2SQL 3碎片 4推荐位
	$num = $num > 0 ? $num : $label['num'];//调去数量
	$sequence = $label['sequence'] == 1 ? 'desc' : 'asc';

    $api = new Baseapi();

	$params = array();//置空前台传入的值
	if ($method == 1) {//模型
		$model = $label['model'];
		
		$fields = $label['fields'];//筛选字段
		$is_thumb = $label['is_thumb'];//是否缩略图
		$doc_order = $label['doc_order'];//@todo 排序字段 现在sdk中有price/score/comment的排序 和现在标签的排序字段不一致
		$sequence = $label['sequence'];//@todo 和sdk中场馆搜索的自断保持一致 之后改称和sdk一直

		$is_page = $label['is_page'];//是否分页 ？
		

		if ($model == 1) {//新闻模型- 得去翻译cid
			$params['cid'] = $label['cid'];//新闻模型的有频道id选项
			$res = WebApi_Cms_Doc::instance()->getCmsDocs($params, $num, $label['fields'], $label['doc_order'], $sequence);
		} elseif ($model == 2) {//场馆
            //临时初始化 排序字段
            $doc_order = $label['doc_order'];
            //临时初始化 排序方式
            $sequence = $sequence == 1 ? 'desc' : 'asc';

            $stadiumRes = $api->searchStadium($params, 1, $num, $doc_order, $sequence);
            if($stadiumRes['status'] != 200) {
                $res = array();
            } else {
                $stadiums = $stadiumRes['data']['records'];
                if(!empty($stadiums)) {
                    foreach($stadiums as &$stadium) {
                        unset($stadium['stadium_description']);//去掉冗余数据 减少数据传输时间
                    }
                }

                $res = $stadiums;
            }


//            print_r($stadiumRes);exit;
			//$res = WebApi_Stadium::instance()->getCmsStadiums($params, $num, $label['fields'], $label['doc_order'], $sequence);
		} elseif ($model == 3) {//教练
			$res = WebApi_Coach::instance()->getCmsCoachs($params, $num, $label['fields'], $label['doc_order'], $sequence);
		}
		
	} elseif ($method == 2) {//sql
		$sql = isset($label['content']) ? $label['content'] : '';
		$sql = strtolower($sql);
		$replace = array(
			'insert' => '',
			'update' => '',
			'delete' => '',
			'drop' => '',
			'create' => '',
			'modify' => '',
			'rename'=>'',
			'alter' => '',
			'cas' => ''
		);
		$sql  = strtr($sql, $replace);//过滤
		
		$res = WebApi_Cms_Label::instance()->query($sql);
		
	} elseif ($method == 3) {//碎片
		$res = isset($label['content']) ? $label['content'] : '';
	} else {
		$res = array();
	}
	
	//var_dump($smarty);
	$smarty->assign($assign, $res);//自赋值
	
}

?>