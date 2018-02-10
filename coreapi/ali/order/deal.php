<?php
/**
 * 支付宝订单交易支付
 * @author wy
 * @version 1.2.0
 */
class CoreApi_Ali_Order_Deal extends CoreApi {
	
	protected static $instance__; //必要,
	protected $_module = 'yd_payment';
	protected $_tableName = 'ali_order_deal_payment';
	protected $_primaryKey = 'order_deal_payment_id';
	protected $_fields = array(
	    'order_deal_payment_id' => 'bigint',
	    'payment_account_id' => 'bigint',
	    'uid' => 'bigint',
	    'oid' => 'bigint',
	    'stadium_id' => 'bigint',
	    'venue_id' => 'bigint',
	    'stadium_name' => 'varchar',
	    'venue_name' => 'varchar',
	    'out_trade_no' => 'varchar',
	    'trade_no' => 'varchar',
	    'buyer_id' => 'varchar',
	    'buyer_email' => 'varchar',
	    'seller_id' => 'varchar',
	    'seller_email' => 'varchar',
	    'money' => 'int',
	    'pay_type' => 'int',
	    'channel' => 'int',
	    'create_time' => 'datetime',
	);
	
	/**
	 * 根据条件获取支付宝订单交易支付信息
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 * @author wy
	 */
	public function getOrderDealPaymentsByParams($params, $page = 1, $pagesize = -1, $returnType = 'Array', $orderBy = 'order_deal_payment_id') {
		$sql = 'select * from ' . $this->_tableName . ' where 1 ';
		
		if(isset($params['create_time_start'])) {
		    $sql .= " and create_time >= '" . $params['create_time_start'] . "'";
		    unset($params['create_time_start']);
		}
		if(isset($params['create_time_end'])) {
		    $createTimeEnd = $params['create_time_end'] . ' 23:59:59';
		    $sql .= " and create_time <= '" . $createTimeEnd . "'";
		    unset($params['create_time_end']);
		}
		if(isset($params['stadium_name'])) {
			$sql .= ' and stadium_name like ' . "'%" . $params['stadium_name'] . "%'";
			unset($params['stadium_name']);
		}
		
		if(isset($params['venue_name'])) {
		    $sql .= ' and venue_name like ' . "'%" . $params['venue_name'] . "%'";
		    unset($params['venue_name']);
		}
		
		$binds = array();
		foreach($params as $key => $value) {
			$sql .= ' and ' . $key . '=:' . $key;
			$binds[':' . $key] = $value;
		}
		$sql .= ' order by ' . $orderBy . ' desc';
		return $this->db->page($sql, $binds, $page, $pagesize, $returnType);
	}
	
	/**
	 * 根据条件获取支付宝订单交易支付数量
	 *
	 * @param $params
	 * @return int
	 * @author wy
	 */
	public function getOrderDealPaymentCountByParams($params) {
		$sql = 'select count(*) as total from ' . $this->_tableName . ' where 1 ';
		
		if(isset($params['create_time_start'])) {
		    $sql .= " and create_time >= '" . $params['create_time_start'] . "'";
		    unset($params['create_time_start']);
		}
		
		if(isset($params['create_time_end'])) {
		    $createTimeEnd = $params['create_time_end'] . ' 23:59:59';
		    $sql .= " and create_time <= '" . $createTimeEnd . "'";
		    unset($params['create_time_end']);
		}
        if(isset($params['stadium_name'])) {
			$sql .= ' and stadium_name like ' . "'%" . $params['stadium_name'] . "%'";
			unset($params['stadium_name']);
		}
		
		if(isset($params['venue_name'])) {
		    $sql .= ' and venue_name like ' . "'%" . $params['venue_name'] . "%'";
		    unset($params['venue_name']);
		}
		
		$binds = array();
		foreach($params as $key => $value) {
			$sql .= ' and ' . $key . '=:' . $key;
			$binds[':' . $key] = $value;
		}
		$res = $this->db->select_one($sql, $binds);
		return isset($res['total']) ? $res['total'] : 0;
	}
}
?>