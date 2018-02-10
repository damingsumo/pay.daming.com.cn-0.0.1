<?php
/**
 * 支付宝订单交易支付
 * @author wy
 * @version 1.2.0
 */
class WebApi_Ali_Order_Deal extends WebApi {
	
	public static $instance__;
	
	static public function &instance() {
		if(!isset(self::$instance__)) {
			$class = __CLASS__;
			self::$instance__ = new $class();
		}
		
		return self::$instance__;
	}
	
	/**
	 * 根据条件获取支付宝订单交易支付数量
	 * 
	 * @param $params
	 * @return int
	 * @author wy
	 */
	public function getOrderDealPaymentCountByParams($params) {
		if(!is_array($params)) {
			return 0;
		}
		
		return CoreApi_Ali_Order_Deal::instance()->getOrderDealPaymentCountByParams($params);
	}
	
	/**
	 * 根据条件获取支付宝订单交易支付信息
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 * @author wy
	 */
	public function getOrderDealPaymentsByParams($params, $page = 1, $pagesize = -1) {
	    if(!is_array($params) || $page <= 0) {
	        return array();
	    }
	
	    return CoreApi_Ali_Order_Deal::instance()->getOrderDealPaymentsByParams($params, $page, $pagesize);
	}
	
	/**
	 * 编辑
	 *
	 * @param array $params
	 * @param int $orderDealPaymentId
	 * @return bool
	 * @author wy
	 */
	public function edit($params, $orderDealPaymentId) {
	    if(!is_array($params) || empty($params) || $orderDealPaymentId <= 0) {
	        return false;
	    }
	
	    $AliOrderDeal = CoreApi_Ali_Order_Deal::instance()->row('*', $orderDealPaymentId);
	    if(empty($AliOrderDeal)) {
	        return false;
	    }
	     
	    $data = array();
	    $data['payment_account_id'] = isset($params['payment_account_id']) ? $params['payment_account_id'] : $AliOrderDeal['payment_account_id'];
	    $data['uid'] = isset($params['uid']) ? $params['uid'] : $AliOrderDeal['uid'];
	    $data['oid'] = isset($params['oid']) ? $params['oid'] : $AliOrderDeal['oid'];
	    $data['stadium_id'] = isset($params['stadium_id']) ? $params['stadium_id'] : $AliOrderDeal['stadium_id'];
	    $data['venue_id'] = isset($params['venue_id']) ? $params['venue_id'] : $AliOrderDeal['venue_id'];
	    $data['stadium_name'] = isset($params['stadium_name']) ? trim($params['stadium_name']) : $AliOrderDeal['stadium_name'];
	    $data['venue_name'] = isset($params['venue_name']) ? trim($params['venue_name']) : $AliOrderDeal['venue_name'];
	    $data['out_trade_no'] = isset($params['out_trade_no']) ? trim($params['out_trade_no']) : $AliOrderDeal['out_trade_no'];
	    $data['trade_no'] = isset($params['trade_no']) ? trim($params['trade_no']) : $AliOrderDeal['trade_no'];
	    $data['buyer_id'] = isset($params['buyer_id']) ? trim($params['buyer_id']) : $AliOrderDeal['buyer_id'];
	    $data['buyer_email'] = isset($params['buyer_email']) ? trim($params['buyer_email']) : $AliOrderDeal['buyer_email'];
	    $data['seller_id'] = isset($params['seller_id']) ? trim($params['seller_id']) : $AliOrderDeal['seller_id'];
	    $data['seller_email'] = isset($params['seller_email']) ? trim($params['seller_email']) : $AliOrderDeal['seller_email'];
	    $data['money'] = isset($params['money']) ? $params['money'] : $AliOrderDeal['money'];
	    $data['pay_type'] = isset($params['pay_type']) ? $params['pay_type'] : $AliOrderDeal['pay_type'];
	    $data['channel'] = isset($params['channel']) ? $params['channel'] : $AliOrderDeal['channel'];
	    return CoreApi_Ali_Order_Deal::instance()->update($data, $orderDealPaymentId);
	}
	
	/**
	 * 添加
	 *
	 * @param array $params
	 * @return bool
	 * @author wy
	 */
	public function add($params) {
	    if(!is_array($params) || empty($params)) {
	        return false;
	    }
	     
	    $data = array();
	    $data['payment_account_id'] = isset($params['payment_account_id']) ? $params['payment_account_id'] : 0;
	    $data['uid'] = isset($params['uid']) ? $params['uid'] : 0;
	    $data['oid'] = isset($params['oid']) ? $params['oid'] : 0;
	    $data['stadium_id'] = isset($params['stadium_id']) ? $params['stadium_id'] : 0;
	    $data['venue_id'] = isset($params['venue_id']) ? $params['venue_id'] : 0;
	    $data['stadium_name'] = isset($params['stadium_name']) ? trim($params['stadium_name']) : '';
	    $data['venue_name'] = isset($params['venue_name']) ? trim($params['venue_name']) : '';
	    $data['out_trade_no'] = isset($params['out_trade_no']) ? trim($params['out_trade_no']) : '';
	    $data['trade_no'] = isset($params['trade_no']) ? trim($params['trade_no']) : '';
	    $data['buyer_id'] = isset($params['buyer_id']) ? trim($params['buyer_id']) : '';
	    $data['buyer_email'] = isset($params['buyer_email']) ? trim($params['buyer_email']) : '';
	    $data['seller_id'] = isset($params['seller_id']) ? trim($params['seller_id']) : '';
	    $data['seller_email'] = isset($params['seller_email']) ? trim($params['seller_email']) : '';
	    $data['money'] = isset($params['money']) ? $params['money'] : 0;
	    $data['pay_type'] = isset($params['pay_type']) ? $params['pay_type'] : 1;
	    $data['channel'] = isset($params['channel']) ? $params['channel'] : 1;
	    $data['create_time'] = date('Y-m-d H:i:s');
	    return CoreApi_Ali_Order_Deal::instance()->insert($data);
	}
	
}
?>