<?php
/**
 * 微信会员卡支付
 * @author spring
 * @version 1.0.0
 */
class CoreApi_Wx_Card_Payment extends CoreApi {
	
	protected static $instance__; //必要,
	protected $_module = 'yd_payment';
	protected $_tableName = 'wx_card_payment';
	protected $_primaryKey = 'card_payment_id';
	protected $_fields = array(
	    'card_payment_id' => 'bigint',
	    'card_recharge_id' => 'bigint',
	    'payment_account_id' => 'bigint',
	    'uid' => 'bigint',
	    'card_id' => 'bigint',
	    'card_type_id' => 'bigint',
	    'stadium_id' => 'bigint',
	    'open_id' => 'varchar',
	    'card_name' => 'varchar',
	    'card_number' => 'varchar',
	    'card_type_name' => 'varchar',
	    'stadium_name' => 'varchar',
	    'out_trade_no' => 'int',
	    'trade_no' => 'int',
	    'money' => 'int',
	    'pay_type' => 'datetime',
	    'channel' => 'datetime',
	    'pay_source' => 'datetime',
	    'create_time' => 'datetime',
	);
	
	/**
	 * 根据条件获取微信会员卡支付信息
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 * @author spring
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
	 * 根据条件获取微信会员卡支付数量
	 *
	 * @param $params
	 * @return int
	 * @author spring
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