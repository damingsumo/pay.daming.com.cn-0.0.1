<?php
/**
 * 微信会员卡支付
 * @author spring
 * @version 1.0.0
 */
class WebApi_Wx_Card_Payment extends WebApi {
	
	public static $instance__;
	
	static public function &instance() {
		if(!isset(self::$instance__)) {
			$class = __CLASS__;
			self::$instance__ = new $class();
		}
		
		return self::$instance__;
	}
	
	/**
	 * 根据条件获取微信会员卡支付数量
	 * 
	 * @param $params
	 * @return int
	 * @author spring
	 */
	public function getCardPaymentCountByParams($params) {
		if(!is_array($params)) {
			return 0;
		}
		
		return CoreApi_Wx_Card_Payment::instance()->getCardPaymentCountByParams($params);
	}
	
	/**
	 * 根据条件获取微信会员卡支付信息
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 * @author spring
	 */
	public function getCardPaymentsByParams($params, $page = 1, $pagesize = 20) {
	    if(!is_array($params) || $page <= 0) {
	        return array();
	    }
	
	    return CoreApi_Wx_Card_Payment::instance()->getCardPaymentsByParams($params, $page, $pagesize);
	}
	
	/**
	 * 添加微信会员卡支付信息
	 *
	 * @param array $params
	 * @return bool
	 * @author spring
	 */
	public function add($params) {
	    if(!is_array($params) || empty($params)) {
	        return false;
	    }
	
	    $cardData = array();
	    $cardData['card_recharge_id'] = isset($params['card_recharge_id']) ? $params['card_recharge_id'] : 0;
	    $cardData['payment_account_id'] = isset($params['payment_account_id']) ? $params['payment_account_id'] : 0;
	    $cardData['uid'] = isset($params['uid']) ? $params['uid'] : 0;
	    $cardData['card_id'] = isset($params['card_id']) ? $params['card_id'] : 0;
	    $cardData['card_type_id'] = isset($params['card_type_id']) ? $params['card_type_id'] : 0;
	    $cardData['stadium_id'] = isset($params['stadium_id']) ? $params['stadium_id'] : 0;
	    $cardData['open_id'] = isset($params['open_id']) ? $params['open_id'] : '';
	    $cardData['card_name'] = isset($params['card_name']) ? $params['card_name'] : '';
	    $cardData['card_number'] = isset($params['card_number']) ? $params['card_number'] : '';
	    $cardData['card_type_name'] = isset($params['card_type_name']) ? $params['card_type_name'] : '';
	    $cardData['stadium_name'] = isset($params['stadium_name']) ? $params['stadium_name'] : '';
	    $cardData['out_trade_no'] = isset($params['out_trade_no']) ? $params['out_trade_no'] : '';
	    $cardData['trade_no'] = isset($params['trade_no']) ? $params['trade_no'] : '';
	    $cardData['money'] = isset($params['money']) ? $params['money'] : 0;
	    $cardData['pay_type'] = isset($params['pay_type']) ? $params['pay_type'] : 0;
	    $cardData['channel'] = isset($params['channel']) ? $params['channel'] : 0;
	    $cardData['pay_source'] = isset($params['pay_source']) ? $params['pay_source'] : 0;
	    $cardData['create_time'] = date('Y-m-d H:i:s');
	    return CoreApi_Wx_Card_Payment::instance()->insert($cardData);
	}
}
?>