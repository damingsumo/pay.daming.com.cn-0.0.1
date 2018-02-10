<?php
/**
 * 微信支付账户
 * @author spring
 * @version 1.0.0
 */
class CoreApi_Wx_Account extends CoreApi {
	
	protected static $instance__; //必要,
	protected $_module = 'yd_payment';
	protected $_tableName = 'wx_payment_account';
	protected $_primaryKey = 'payment_account_id';
	protected $_fields = array(
	    'payment_account_id' => 'bigint',
	    'name' => 'varchar',
	    'wx_appid' => 'varchar',
	    'wx_token' => 'varchar',
	    'wx_appsecret' => 'varchar',
	    'wx_sign_str' => 'varchar',
	    'mchid' => 'varchar',
	    'mch_key' => 'varchar',
	    'sslcert_path' => 'varchar',
	    'sslkey_path' => 'varchar',
	    'pay_type' => 'int',
	    'channel' => 'int',
	    'status' => 'int',
	    'create_time' => 'datetime',
	    'update_time' => 'datetime',
	);
	
	/**
	 * 根据条件获取微信支付账户信息
	 *
	 * @param array $params
	 * @param int $page
	 * @param int $pagesize
	 * @author spring
	 */
	public function getWxAccountsByParams($params, $page = 1, $pagesize = 20, $returnType = 'Array', $orderBy = 'payment_account_id') {
		$sql = 'select * from ' . $this->_tableName . ' where status != -2 ';
		
		if(isset($params['name'])) {
			$sql .= ' and name like ' . "'%" . $params['name'] . "%'";
			unset($params['name']);
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
	 * 根据条件获取微信支付账户数量
	 *
	 * @param $params
	 * @return int
	 * @author spring
	 */
	public function getWxAccountCountByParams($params) {
		$sql = 'select count(*) as total from ' . $this->_tableName . ' where status != -2 ';
		
		if(isset($params['name'])) {
			$sql .= ' and name like ' . "'%" . $params['name'] . "%'";
			unset($params['name']);
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