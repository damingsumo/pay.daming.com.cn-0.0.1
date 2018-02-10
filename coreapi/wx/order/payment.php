<?php
/**
 * 微信订单支付
 * @author spring
 * @version 1.0.0
 */
class CoreApi_Wx_Order_Payment extends CoreApi {
	
	protected static $instance__; //必要,
	protected $_module = 'yd_payment';
	protected $_tableName = 'wx_order_payment';
	protected $_primaryKey = 'order_payment_id';
	protected $_fields = array(
	    'order_payment_id' => 'bigint',
	    'payment_account_id' => 'bigint',
	    'uid' => 'bigint',
	    'oid' => 'bigint',
	    'stadium_id' => 'bigint',
	    'venue_id' => 'bigint',
	    'stadium_name' => 'varchar',
	    'venue_name' => 'varchar',
	    'open_id' => 'varchar',
	    'out_trade_no' => 'varchar',
	    'trade_no' => 'varchar',
	    'money' => 'int',
	    'pay_type' => 'int',
	    'channel' => 'int',
	    'create_time' => 'datetime',
	);
	
	/**
	 * 根据条件获取微信订单支付信息
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 * @author spring
	 */
	public function getOrderPaymentsByParams($params, $page = 1, $pagesize = 20, $returnType = 'Array', $orderBy = 'order_payment_id') {
		$sql = 'select * from ' . $this->_tableName . ' where 1 ';
		
		if(isset($params['stadium_name'])) {
			$sql .= ' and stadium_name like ' . "'%" . $params['stadium_name'] . "%'";
			unset($params['stadium_name']);
		}
		
		if(isset($params['venue_name'])) {
		    $sql .= ' and venue_name like ' . "'%" . $params['venue_name'] . "%'";
		    unset($params['venue_name']);
		}
		
		if(isset($params['create_time_start'])) {
		    $sql .= " and create_time >= '" . $params['create_time_start'] . "'";
		    unset($params['create_time_start']);
		}
		
		if(isset($params['create_time_end'])) {
		    $createTimeEnd = $params['create_time_end'] . ' 23:59:59';
		    $sql .= " and create_time <= '" . $createTimeEnd . "'";
		    unset($params['create_time_end']);
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
	 * 根据条件获取微信订单支付数量
	 *
	 * @param $params
	 * @return int
	 * @author spring
	 */
	public function getOrderPaymentCountByParams($params) {
		$sql = 'select count(*) as total from ' . $this->_tableName . ' where 1 ';
		
        if(isset($params['stadium_name'])) {
			$sql .= ' and stadium_name like ' . "'%" . $params['stadium_name'] . "%'";
			unset($params['stadium_name']);
		}
		
		if(isset($params['venue_name'])) {
		    $sql .= ' and venue_name like ' . "'%" . $params['venue_name'] . "%'";
		    unset($params['venue_name']);
		}
		
		if(isset($params['create_time_start'])) {
		    $sql .= " and create_time >= '" . $params['create_time_start'] . "'";
		    unset($params['create_time_start']);
		}
		
		if(isset($params['create_time_end'])) {
		    $createTimeEnd = $params['create_time_end'] . ' 23:59:59';
		    $sql .= " and create_time <= '" . $createTimeEnd . "'";
		    unset($params['create_time_end']);
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