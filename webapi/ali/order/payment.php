<?php
/**
 * 支付宝订单支付
 * @author wy
 * @version 1.0.0
 */
class WebApi_Ali_Order_Payment extends WebApi {
	
	public static $instance__;
	
	static public function &instance() {
		if(!isset(self::$instance__)) {
			$class = __CLASS__;
			self::$instance__ = new $class();
		}
		
		return self::$instance__;
	}
	
	/**
	 * 根据条件获取支付宝订单支付数量
	 * 
	 * @param $params
	 * @return int
	 * @author wy
	 */
	public function getOrderPaymentCountByParams($params) {
		if(!is_array($params)) {
			return 0;
		}
		
		return CoreApi_Ali_Order_Payment::instance()->getOrderPaymentCountByParams($params);
	}
	
	/**
	 * 根据条件获取支付宝订单支付信息
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 * @author wy
	 */
	public function getOrderPaymentsByParams($params, $page = 1, $pagesize = 20) {
	    if(!is_array($params) || $page <= 0) {
	        return array();
	    }
	
	    return CoreApi_Ali_Order_Payment::instance()->getOrderPaymentsByParams($params, $page, $pagesize);
	}
	
	/**
	 * 新增支付宝订单支付信息
	 *
	 * @param array $params
	 * @return bool
	 * @author wy
	 */
	public function add($params) {
	    if(!is_array($params) || empty($params)) {
	        return false;
	    }
	
	    $data['payment_account_id'] = isset($params['payment_account_id']) ? $params['payment_account_id'] : 0;
	    $data['uid'] = isset($params['uid']) ? $params['uid'] : 0;
	    $data['oid'] = isset($params['oid']) ? $params['oid'] : 0;
	    $data['stadium_id'] = isset($params['stadium_id']) ? $params['stadium_id'] : 0;
	    $data['venue_id'] = isset($params['venue_id']) ? $params['venue_id'] : 0;
	    $data['stadium_name'] = isset($params['stadium_name']) ? $params['stadium_name'] : '';
	    $data['venue_name'] = isset($params['venue_name']) ? $params['venue_name'] : '';
	    $data['out_trade_no'] = isset($params['out_trade_no']) ? $params['out_trade_no'] : '';
	    $data['trade_no'] = isset($params['trade_no']) ? $params['trade_no'] : '';
	    $data['buyer_id'] = isset($params['buyer_id']) ? $params['buyer_id'] : '';
	    $data['buyer_email'] = isset($params['buyer_email']) ? $params['buyer_email'] : '';
	    $data['seller_id'] = isset($params['seller_id']) ? $params['seller_id'] : '';
	    $data['seller_email'] = isset($params['seller_email']) ? $params['seller_email'] :'';
	    $data['money'] = isset($params['money']) ? $params['money'] : 0;
	    $data['pay_type'] = isset($params['pay_type']) ? $params['pay_type'] : 1;
	    $data['channel'] = isset($params['channel']) ? $params['channel'] : 1;
	    $data['create_time'] = date('Y-m-d H:i:s');
	    return CoreApi_Ali_Order_Payment::instance()->insert($data);
	}
}
?>