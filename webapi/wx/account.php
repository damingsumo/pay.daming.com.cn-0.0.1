<?php
/**
 * 微信支付账户
 * @author spring
 * @version 1.0.0
 */
class WebApi_Wx_Account extends WebApi {
	
	public static $instance__;
	
	static public function &instance() {
		if(!isset(self::$instance__)) {
			$class = __CLASS__;
			self::$instance__ = new $class();
		}
		
		return self::$instance__;
	}
	
	/**
	 * 根据条件获取微信支付账户数量
	 * 
	 * @param $params
	 * @return int
	 * @author spring
	 */
	public function getWxAccountCountByParams($params) {
		if(!is_array($params)) {
			return 0;
		}
		
		return CoreApi_Wx_Account::instance()->getWxAccountCountByParams($params);
	}
	
	/**
	 * 根据条件获取微信支付账户信息
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 * @author spring
	 */
	public function getWxAccountsByParams($params, $page = 1, $pagesize = 20) {
	    if(!is_array($params) || $page <= 0) {
	        return array();
	    }
	
	    return CoreApi_Wx_Account::instance()->getWxAccountsByParams($params, $page, $pagesize);
	}
	
	/**
	 * 编辑微信支付账户
	 *
	 * @param array $params
	 * @param int $paymentAccountId
	 * @return bool
	 * @author spring
	 */
	public function edit($params, $paymentAccountId) {
	    if(!is_array($params) || empty($params) || $paymentAccountId <= 0) {
	        return false;
	    }

	    $account = CoreApi_Wx_Account::instance()->row('*', $paymentAccountId);
	    if(empty($account)) {
	        return false;
	    }
	    
	    $accountData = array();
	    $accountData['name'] = isset($params['name']) ? trim($params['name']) : $account['name'];
	    $accountData['wx_appid'] = isset($params['wx_appid']) ? trim($params['wx_appid']) : $account['wx_appid'];
	    $accountData['wx_token'] = isset($params['wx_token']) ? trim($params['wx_token']) : $account['wx_token'];
	    $accountData['wx_appsecret'] = isset($params['wx_appsecret']) ? trim($params['wx_appsecret']) : $account['wx_appsecret'];
	    $accountData['wx_sign_str'] = isset($params['wx_sign_str']) ? trim($params['wx_sign_str']) : $account['wx_sign_str'];
	    $accountData['mchid'] = isset($params['mchid']) ? trim($params['mchid']) : $account['mchid'];
	    $accountData['mch_key'] = isset($params['mch_key']) ? trim($params['mch_key']) : $account['mch_key'];
	    $accountData['sslcert_path'] = isset($params['sslcert_path']) ? trim($params['sslcert_path']) : $account['sslcert_path'];
	    $accountData['sslkey_path'] = isset($params['sslkey_path']) ? trim($params['sslkey_path']) : $account['sslkey_path'];
	    $accountData['pay_type'] = isset($params['pay_type']) ? $params['pay_type'] : $account['pay_type'];
	    $accountData['channel'] = isset($params['channel']) ? $params['channel'] : $account['channel'];
	    $accountData['status'] = isset($params['status']) ? $params['status'] : $account['status'];
	    $accountData['update_time'] = date('Y-m-d H:i:s');
	    return CoreApi_Wx_Account::instance()->update($accountData, $paymentAccountId);
	}
	
	/**
	 * 添加微信支付账户
	 *
	 * @param array $params
	 * @return bool
	 * @author spring
	 */
	public function add($params) {
	    if(!is_array($params) || empty($params)) {
	        return false;
	    }
	    
	    $accountData = array();
	    $accountData['name'] = isset($params['name']) ? trim($params['name']) : '';
	    $accountData['wx_appid'] = isset($params['wx_appid']) ? trim($params['wx_appid']) : '';
	    $accountData['wx_token'] = isset($params['wx_token']) ? trim($params['wx_token']) : '';
	    $accountData['wx_appsecret'] = isset($params['wx_appsecret']) ? trim($params['wx_appsecret']) : '';
	    $accountData['wx_sign_str'] = isset($params['wx_sign_str']) ? trim($params['wx_sign_str']) : '';
	    $accountData['mchid'] = isset($params['mchid']) ? trim($params['mchid']) : '';
	    $accountData['mch_key'] = isset($params['mch_key']) ? trim($params['mch_key']) : '';
	    $accountData['sslcert_path'] = isset($params['sslcert_path']) ? trim($params['sslcert_path']) : '';
	    $accountData['sslkey_path'] = isset($params['sslkey_path']) ? trim($params['sslkey_path']) : '';
	    $accountData['pay_type'] = isset($params['pay_type']) ? $params['pay_type'] : 0;
	    $accountData['channel'] = isset($params['channel']) ? $params['channel'] : 0;
	    $accountData['status'] = isset($params['status']) ? $params['status'] : 0;
	    $accountData['create_time'] = date('Y-m-d H:i:s');
	    $accountData['update_time'] = '0000-00-00 00:00:00';
	    return CoreApi_Wx_Account::instance()->insert($accountData);
	}
}
?>