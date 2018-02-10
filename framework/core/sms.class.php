<?php

/**
 * 短信发送公用类
 * @author liu
 * 
 */

class sms {
	
	/**
	  * 发送模板短信
	  * @param to 手机号码集合,用英文逗号分开
	  * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
	  * @param $tempId 模板Id,测试应用和未上线应用使用测试模板请填写1，正式应用上线后填写已申请审核通过的模板ID
	  * @return array
	  * 
	  *  模板ID111439 PAY_SUCCESS
	  * 
	  * 
	  */       
	public static function send($to, $datas, $tempId = 1) {
		include_once 'CCPRestSmsSDK.php';
		if(empty($to) || empty($datas)) {
			return array('status' => 400, 'msg' => '手机号、内容为空');
		}
		// 初始化REST SDK
		$rest = new REST('app.cloopen.com', '8883', '2013-12-26');
//		$rest = new REST('sandboxapp.cloopen.com', '8883', '2013-12-26');
		$rest->setAccount('aaf98f89512446e20151381a09bc3bbc', '8136a7d9e62040a2acafe38106836902');
		$rest->setAppId('8a216da856b66fb20156bb4bf6b0003a');
		
		// 发送模板短信
		$result = $rest->sendTemplateSMS($to,$datas,$tempId);
		if($result == NULL ) {
		   return array('status' => 400, 'msg' => 'error');
		}
		if($result->statusCode!=0) {
		    return array('status' => $result->statusCode, 'msg' => $result->statusMsg);
		    //TODO 添加错误处理逻辑
		}else{
		    return array('status' => 200, 'msg' => '成功');
		}
	}
}

?>