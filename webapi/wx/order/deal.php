<?php
/**
 * 微信订单交易支付
 * @author spring
 * @version 1.1.0
 */
class WebApi_Wx_Order_Deal extends WebApi {
	
	public static $instance__;
	
	static public function &instance() {
		if(!isset(self::$instance__)) {
			$class = __CLASS__;
			self::$instance__ = new $class();
		}
		
		return self::$instance__;
	}
	
	/**
	 * 根据条件获取微信订单交易支付数量
	 * 
	 * @param $params
	 * @return int
	 * @author spring
	 */
	public function getOrderDealCountByParams($params) {
		if(!is_array($params)) {
			return 0;
		}
		
		return CoreApi_Wx_Order_Deal::instance()->getOrderDealCountByParams($params);
	}
	
	/**
	 * 根据条件获取微信订单交易支付信息
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 * @author spring
	 */
	public function getOrderDealsByParams($params, $page = 1, $pagesize = 20) {
	    if(!is_array($params) || $page <= 0) {
	        return array();
	    }
	
	    return CoreApi_Wx_Order_Deal::instance()->getOrderDealsByParams($params, $page, $pagesize);
	}
	
	/**
	 * 添加微信订单交易支付信息
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
	    $orderData['stadium_name'] = isset($params['stadium_name']) ? $params['stadium_name'] : '';
	    $orderData['venue_name'] = isset($params['venue_name']) ? $params['venue_name'] : '';
	    $orderData['open_id'] = isset($params['open_id']) ? $params['open_id'] : '';
	    $orderData['out_trade_no'] = isset($params['out_trade_no']) ? $params['out_trade_no'] : '';
	    $orderData['trade_no'] = isset($params['trade_no']) ? $params['trade_no'] : '';
	    $orderData['money'] = isset($params['money']) ? $params['money'] : 0;
	    $orderData['pay_type'] = isset($params['pay_type']) ? $params['pay_type'] : 0;
	    $orderData['channel'] = isset($params['channel']) ? $params['channel'] : 0;
	    $orderData['create_time'] = date('Y-m-d H:i:s');
	    return CoreApi_Wx_Order_Deal::instance()->insert($orderData);
	}
	
	/**
	 * 编辑微信订单交易支付信息
	 *
	 * @param array $params
	 * @param int $orderDealPaymentId
	 * @return bool
	 * @author spring
	 */
	public function edit($params, $orderDealPaymentId) {
	    if(!is_array($params) || empty($params) || $orderDealPaymentId <= 0) {
	        return false;
	    }
	
	    $orderDeal = CoreApi_Wx_Order_Deal::instance()->row('*', $orderDealPaymentId);
	    if(empty($orderDeal)) {
	        return false;
	    }
	    
	    $orderData = array();
	    $orderData['payment_account_id'] = isset($params['payment_account_id']) ? $params['payment_account_id'] : $orderDeal['payment_account_id'];
	    $orderData['uid'] = isset($params['uid']) ? $params['uid'] : $orderDeal['uid'];
	    $orderData['oid'] = isset($params['oid']) ? $params['oid'] : $orderDeal['oid'];
	    $orderData['stadium_id'] = isset($params['stadium_id']) ? $params['stadium_id'] : $orderDeal['stadium_id'];
	    $orderData['venue_id'] = isset($params['venue_id']) ? $params['venue_id'] : $orderDeal['venue_id'];
	    $orderData['stadium_name'] = isset($params['stadium_name']) ? $params['stadium_name'] : $orderDeal['stadium_name'];
	    $orderData['venue_name'] = isset($params['venue_name']) ? $params['venue_name'] : $orderDeal['venue_name'];
	    $orderData['open_id'] = isset($params['open_id']) ? $params['open_id'] : $orderDeal['open_id'];
	    $orderData['out_trade_no'] = isset($params['out_trade_no']) ? $params['out_trade_no'] : $orderDeal['out_trade_no'];
	    $orderData['trade_no'] = isset($params['trade_no']) ? $params['trade_no'] : $orderDeal['trade_no'];
	    $orderData['money'] = isset($params['money']) ? $params['money'] : $orderDeal['money'];
	    $orderData['pay_type'] = isset($params['pay_type']) ? $params['pay_type'] : $orderDeal['pay_type'];
	    $orderData['channel'] = isset($params['channel']) ? $params['channel'] : $orderDeal['channel'];
	    return CoreApi_Wx_Order_Deal::instance()->update($orderData, $orderDealPaymentId);
	}
}
?>