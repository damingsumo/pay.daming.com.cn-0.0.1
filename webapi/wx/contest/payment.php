<?php
/**
 * 微信订单支付
 * @author spring
 * @version 1.0.0
 */
class WebApi_Wx_Contest_Payment extends WebApi {
	
	public static $instance__;
	
	static public function &instance() {
		if(!isset(self::$instance__)) {
			$class = __CLASS__;
			self::$instance__ = new $class();
		}
		
		return self::$instance__;
	}
	
	/**
	 * 根据条件获取微信订单支付数量
	 * 
	 * @param $params
	 * @return int
	 * @author spring
	 */
	public function getOrderPaymentCountByParams($params) {
		if(!is_array($params)) {
			return 0;
		}
		
		return CoreApi_Wx_Order_Payment::instance()->getOrderPaymentCountByParams($params);
	}
	
	/**
	 * 根据条件获取微信订单支付信息
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 * @author spring
	 */
	public function getOrderPaymentsByParams($params, $page = 1, $pagesize = 20) {
	    if(!is_array($params) || $page <= 0) {
	        return array();
	    }
	
	    return CoreApi_Wx_Order_Payment::instance()->getOrderPaymentsByParams($params, $page, $pagesize);
	}
	
	/**
	 * 添加微信订单支付信息
	 *
	 * @param array $params
	 * @return bool
	 * @author spring
	 */
	public function add($params) {
	    if(!is_array($params) || empty($params)) {
	        return false;
	    }
	     
	    $orderData = array();
	    $orderData['payment_account_id'] = isset($params['payment_account_id']) ? $params['payment_account_id'] : 0;
	    $orderData['uid'] = isset($params['uid']) ? $params['uid'] : 0;
	    $orderData['registration_id'] = isset($params['registration_id']) ? $params['registration_id'] : 0;
	    $orderData['club_id'] = isset($params['club_id']) ? $params['club_id'] : 0;
	    $orderData['contest_id'] = isset($params['contest_id']) ? $params['contest_id'] : 0;
	    $orderData['club_name'] = isset($params['club_name']) ? $params['club_name'] : '';
	    $orderData['contest_name'] = isset($params['contest_name']) ? $params['contest_name'] : '';
	    $orderData['open_id'] = isset($params['open_id']) ? $params['open_id'] : '';
	    $orderData['out_trade_no'] = isset($params['out_trade_no']) ? $params['out_trade_no'] : '';
	    $orderData['trade_no'] = isset($params['trade_no']) ? $params['trade_no'] : '';
	    $orderData['money'] = isset($params['money']) ? $params['money'] : 0;
	    $orderData['pay_type'] = isset($params['pay_type']) ? $params['pay_type'] : 0;
	    $orderData['channel'] = isset($params['channel']) ? $params['channel'] : 0;
	    $orderData['create_time'] = date('Y-m-d H:i:s');
	    return CoreApi_Wx_Contest_Payment::instance()->insert($orderData);
	}
}
?>