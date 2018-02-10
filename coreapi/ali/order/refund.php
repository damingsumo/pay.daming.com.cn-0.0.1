<?php
/**
 * 支付宝订单退款
 * @author wy
 * @version 1.0.0
 */
class CoreApi_Ali_Order_Refund extends CoreApi {
	
	protected static $instance__; //必要,
	protected $_module = 'yd_payment';
	protected $_tableName = 'ali_order_refund';
	protected $_primaryKey = 'order_refund_id';
	protected $_fields = array(
	    'order_refund_id' => 'bigint',
	    'payment_account_id' => 'bigint',
	    'uid' => 'bigint',
	    'oid' => 'bigint',
	    'stadium_id' => 'bigint',
	    'venue_id' => 'bigint',
	    'buyer_logon_id' => 'bigint',
		'refund_money' => 'int',
	    'fund_change' => 'varchar',
	    'out_trade_no' => 'varchar',
		'trade_no' => 'varchar',
	    'pay_type' => 'int',
	    'channel' => 'int',
	    'create_time' => 'datetime',
	);
	
	/**
	 * 根据条件获取支付宝订单退款信息
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 * @author wy
	 */
	public function getOrderRefundsByParams($params, $page = 1, $pagesize = 20, $returnType = 'Array', $orderBy = 'order_Refund_id') {
		$sql = 'select * from ' . $this->_tableName . ' where 1 ';
		
		$binds = array();
		foreach($params as $key => $value) {
			$sql .= ' and ' . $key . '=:' . $key;
			$binds[':' . $key] = $value;
		}
		$sql .= ' order by ' . $orderBy . ' desc';
		return $this->db->page($sql, $binds, $page, $pagesize, $returnType);
	}
	
	/**
	 * 根据条件获取退款宝订单退款数量
	 *
	 * @param $params
	 * @return int
	 * @author wy
	 */
	public function getOrderRefundCountByParams($params) {
		$sql = 'select count(*) as total from ' . $this->_tableName . ' where 1 ';
		
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