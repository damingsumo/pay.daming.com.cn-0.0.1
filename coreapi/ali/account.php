<?php
/**
 * 支付宝账户管理
 * @author wy
 * @version 1.0.0
 */
class CoreApi_Ali_Account extends CoreApi {
	protected static  $instance__;//必要,
	protected $_module = 'yd_payment';
    protected $_tableName = 'ali_payment_account';
    protected $_primaryKey = 'payment_account_id';
    protected $_fields = array(
        'payment_account_id' => 'bigint',
        'appid' => 'varchar',
		'name' => 'varchar',
		'partner' => 'varchar',
        'seller_id' => 'varchar',
        'seller_email' => 'varchar',
		'private_key' => 'text',
    	'alipay_public_key' => 'text',
    	'sign_type' => 'varchar',
		'cacert_path' => 'varchar',
		'pay_type' => 'int',
		'channel' => 'int',
		'status' => 'int',
		'create_time' => 'datetime',
		'update_time' => 'datetime',
    );
	/**
	 * 获取支付宝账户列表 通过参数
	 * @param array		$params
	 * @param int		$page
	 * @param int		$pageSize
	 * @param string	$returnType
	 * @param string	$sort
	 * @return type 
	 */
 	public function getAliAccountsByParams($params, $page=1, $pageSize=20, $returnType = 'Array', $order = 'payment_account_id', $sequence = 'desc') {
 		$sql = 'select * from '.$this->_tableName.' where 1 ';
 		
 		if(isset($params['name'])) {
 			$sql .= ' and name like \'%'.$params['name'].'%\'';
 			unset($params['name']);
 		}
 		if(isset($params['seller_email'])) {
 			$sql .= ' and seller_email like \'%'.$params['seller_email'].'%\'';
 			unset($params['seller_email']);
 		}
 		if(isset($params['payment_account_ids'])) {
 		    $sql .= ' and payment_account_id in('.implode(',', $params['payment_account_ids']).')';
 		    unset($params['payment_account_ids']);
 		}
 		
 		$binds = array();
 		if(!empty($params)) {
 			foreach ($params as $key => $value) {
 				$sql .= ' and '.$key.'=:'.$key;
 				$binds[':'.$key] = $value;
 			}
 		}
		
 		$sql .= ' order by '.$order.' '.$sequence;
 		return $this->db->page($sql, $binds, $page, $pageSize, $returnType);
 	} 
	
	/**
	 * 获取支付宝账户个数 通过参数
	 * @param array $params
	 * @return int
	 * @author wy 
	 */
	public function getAliAccountCountByParams($params) {
		$sql = 'select count(*) as count from '.$this->_tableName.' where 1 ';
	    
	    if(isset($params['name'])) {
 			$sql .= ' and name like \'%'.$params['name'].'%\'';
 			unset($params['name']);
 		}
	    if(isset($params['seller_email'])) {
 			$sql .= ' and seller_email like \'%'.$params['seller_email'].'%\'';
 			unset($params['seller_email']);
 		}
 		if(isset($params['payment_account_ids'])) {
 		    $sql .= ' and payment_account_id in('.implode(',', $params['payment_account_ids']).')';
 		    unset($params['payment_account_ids']);
 		}
 		
 		$binds = array();
		if(!empty($params)) {
			foreach ($params as $key => $value) {
				$sql .= ' and '.$key.'=:'.$key;
				$binds[':'.$key] = $value;
			}
		}		
		$row = $this->db->select_one($sql, $binds);
		return isset($row['count']) ? $row['count'] : 0;
	}	
}
?>