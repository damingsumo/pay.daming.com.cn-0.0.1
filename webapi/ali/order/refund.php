<?php
/**
 * 支付宝订单退款
 * @author wy
 * @version 1.0.0
 */
class WebApi_Ali_Order_Refund extends WebApi {
	
	public static $instance__;
	
	static public function &instance() {
		if(!isset(self::$instance__)) {
			$class = __CLASS__;
			self::$instance__ = new $class();
		}
		
		return self::$instance__;
	}
	
	/**
	 * 根据条件获取支付宝订单退款数量
	 * 
	 * @param $params
	 * @return int
	 * @author wy
	 */
	public function getOrderRefundCountByParams($params) {
		if(!is_array($params)) {
			return 0;
		}
		
		return CoreApi_Ali_Order_Refund::instance()->getOrderRefundCountByParams($params);
	}
	
	/**
	 * 根据条件获取支付宝订单退款信息
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 * @author wy
	 */
	public function getOrderRefundsByParams($params, $page = 1, $pagesize = 20) {
	    if(!is_array($params) || $page <= 0) {
	        return array();
	    }
	
	    return CoreApi_Ali_Order_Refund::instance()->getOrderRefundsByParams($params, $page, $pagesize);
	}
	
	/**
	 * 添加订单退款信息
	 *
	 * @param array $params
	 * @return bool
	 * @author huwl
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
		$data['buyer_logon_id'] = isset($params['buyer_logon_id']) ? $params['buyer_logon_id'] : '';
		$data['refund_money'] = isset($params['refund_money']) ? $params['refund_money'] : 0;
		$data['fund_change'] = isset($params['fund_change']) ? $params['fund_change'] : '';
		$data['out_trade_no'] = isset($params['out_trade_no']) ? $params['out_trade_no'] : '';
		$data['trade_no'] = isset($params['trade_no']) ? $params['trade_no'] : '';
		$data['pay_type'] = isset($params['pay_type']) ? $params['pay_type'] : 0;
		$data['channel'] = isset($params['channel']) ? $params['channel'] : 0;
		$data['create_time'] = date('Y-m-d H:i:s');
		return CoreApi_Ali_Order_Refund::instance()->insert($data);
	}
}
?>