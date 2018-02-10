<?php
/**
 * 微信订单退订
 * @author spring
 * @version 1.0.0
 */
class WebApi_Wx_Order_Refund extends WebApi {
	
	public static $instance__;
	
	static public function &instance() {
		if(!isset(self::$instance__)) {
			$class = __CLASS__;
			self::$instance__ = new $class();
		}
		
		return self::$instance__;
	}
	
	/**
	 * 根据条件获取微信订单退订数量
	 * 
	 * @param $params
	 * @return int
	 * @author spring
	 */
	public function getOrderRefundCountByParams($params) {
		if(!is_array($params)) {
			return 0;
		}
		
		return CoreApi_Wx_Order_Refund::instance()->getOrderRefundCountByParams($params);
	}
	
	/**
	 * 根据条件获取微信订单退订信息
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 * @author spring
	 */
	public function getOrderRefundsByParams($params, $page = 1, $pagesize = 20) {
	    if(!is_array($params) || $page <= 0) {
	        return array();
	    }
	
	    return CoreApi_Wx_Order_Refund::instance()->getOrderRefundsByParams($params, $page, $pagesize);
	}
	
	/**
	 * 添加微信订单退订信息
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
	    $orderData['oid'] = isset($params['oid']) ? $params['oid'] : 0;
	    $orderData['stadium_id'] = isset($params['stadium_id']) ? $params['stadium_id'] : 0;
	    $orderData['venue_id'] = isset($params['venue_id']) ? $params['venue_id'] : 0;
	    $orderData['refund_money'] = isset($params['refund_money']) ? $params['refund_money'] : 0;
	    $orderData['out_trade_no'] = isset($params['out_trade_no']) ? $params['out_trade_no'] : '';
	    $orderData['trade_no'] = isset($params['trade_no']) ? $params['trade_no'] : '';
	    $orderData['pay_type'] = isset($params['pay_type']) ? $params['pay_type'] : 0;
	    $orderData['channel'] = isset($params['channel']) ? $params['channel'] : 0;
	    $orderData['create_time'] = date('Y-m-d H:i:s');
	    return CoreApi_Wx_Order_Refund::instance()->insert($orderData);
	}
}
?>