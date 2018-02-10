<?php
/**
 * page 中基本的配置页面, 每个controller中必须有.
 * @author liub
 * 2013-05-18
 *
 */
class Controller_Base extends Controller {
	
    /**
     * 错误输出
     *
     * @param string $msg 提示信息
	 * @param string $url 跳转URL
	 * @param integer $delay 跳转URL延时
	 * 
     */
    public function error($msg, $data = array(), $status = 400, $isEncrypt = false){
		return Response::output($data, $status, $msg, $isEncrypt);
    }
 
    /**
     * 成功输出
     *
     * @param array $data
     * @param string $msg
     * @param int $status
     * @param bool $isEncrypt
     * @return json
     */
    public function output($data = array(), $msg = '', $status = 200, $isEncrypt = false){
		return Response::output($data, $status, $msg, $isEncrypt);
    }
    
    /**
     * 错误提示(页面)
     *
     * @param string $msg 提示信息
     * @param string $url 跳转URL
     * @param integer $delay 跳转URL延时
     *
     */
    public function errorPay($msg = '检测到用户尚未登录', $url = '', $delay = 3){
        if($url != '') {
            $params['url'] = $url;
        } else {
            $params['url'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/index';
        }
    
        $params = array();
        $params['msg'] = $msg;
        $params['delay'] = $delay*1000;
        return $this->display('layouts/error', $params);
    }
}
?>