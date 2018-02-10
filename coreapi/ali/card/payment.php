<?php
/**
 * 支付宝会员卡支付
 * @author wy
 * @version 1.0.0
 */
class CoreApi_Ali_Card_Payment extends CoreApi {
	
	protected static $instance__; //必要,
	protected $_module = 'yd_payment';
	protected $_tableName = 'ali_card_payment';
	protected $_primaryKey = 'card_payment_id';
	protected $_fields = array(
	    'card_payment_id' => 'bigint',
	    'card_recharge_id' => 'bigint',
	    'payment_account_id' => 'bigint',
	    'uid' => 'bigint',
	    'card_id' => 'bigint',
	    'card_type_id' => 'bigint',
	    'stadium_id' => 'bigint',
	    'card_name' => 'varchar',
	    'card_number' => 'varchar',
	    'card_type_name' => 'varchar',
	    'stadium_name' => 'varchar',
	    'out_trade_no' => 'varchar',
	    'trade_no' => 'varchar',
	    'buyer_id' => 'varchar',
	    'buyer_email' => 'varchar',
	    'seller_id' => 'varchar',
	    'seller_email' => 'varchar',
	    'money' => 'int',
	    'pay_type' => 'int',
	    'channel' => 'int',
	    'pay_source' => 'int',
	    'create_time' => 'datetime',
	);
	
	/**
	 * 根据条件获取支付宝会员卡支付信息
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 * @author wy
	 */
	public function getCardPaymentsByParams($params, $page = 1, $pagesize = 20, $returnType = 'Array', $orderBy = 'card_payment_id') {
		$sql = 'select * from ' . $this->_tableName . ' where 1 ';
		
		if(isset($params['card_name'])) {
		    $sql .= ' and card_name like ' . "'%" . $params['card_name'] . "%'";
		    unset($params['card_name']);
		}
		
		if(isset($params['card_type_name'])) {
		    $sql .= ' and card_type_name like ' . "'%" . $params['card_type_name'] . "%'";
		    unset($params['card_type_name']);
		}
		
		if(isset($params['stadium_name'])) {
			$sql .= ' and stadium_name like ' . "'%" . $params['stadium_name'] . "%'";
			unset($params['stadium_name']);
		}
		
		if(isset($params['card_number'])) {
		    $sql .= ' and card_number like ' . "'%" . $params['card_number'] . "%'";
		    unset($params['card_number']);
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
	 * 根据条件获取支付宝会员卡支付数量
	 *
	 * @param $params
	 * @return int
	 * @author wy
	 */
	public function getCardPaymentCountByParams($params) {
		$sql = 'select count(*) as total from ' . $this->_tableName . ' where 1 ';
		
        if(isset($params['card_name'])) {
		    $sql .= ' and card_name like ' . "'%" . $params['card_name'] . "%'";
		    unset($params['card_name']);
		}
		
		if(isset($params['card_type_name'])) {
		    $sql .= ' and card_type_name like ' . "'%" . $params['card_type_name'] . "%'";
		    unset($params['card_type_name']);
		}
		
		if(isset($params['stadium_name'])) {
			$sql .= ' and stadium_name like ' . "'%" . $params['stadium_name'] . "%'";
			unset($params['stadium_name']);
		}
		
		if(isset($params['card_number'])) {
		    $sql .= ' and card_number like ' . "'%" . $params['card_number'] . "%'";
		    unset($params['card_number']);
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