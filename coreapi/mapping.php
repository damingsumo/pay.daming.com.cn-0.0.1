<?php
/**
 * 支付账户与场馆关联表
 * @author wy
 * @version 1.0.0
 */
class CoreApi_Mapping extends CoreApi {
	
	protected static $instance__; //必要,
	protected $_module = 'yd_payment';
	protected $_tableName = 'payment_account_stadium_mapping';
	protected $_primaryKey = 'payment_account_stadium_mapping_id';
	protected $_fields = array(
	    'payment_account_stadium_mapping_id' => 'int', 
	    'payment_account_id' => 'int', 
	    'stadium_id' => 'int', 
	    'type' => 'int', 
	    'create_time' => 'datetime', 
	    'update_time' => 'datetime', 
	);
	
	/**
	 * 根据条件获取支付账户与场馆关联信息
	 *
	 * @param array $params
	 * @return array
	 * @author wy
	 */
	public function getMappingByParams($params) {
		$sql = 'select * from ' . $this->_tableName . ' where 1 ';
		
		if(isset($params['type'])) {
		    $sql .= ' and type in('.implode(',', $params['type']).')';
		    unset($params['type']);
		}
		
		$binds = array();
		foreach($params as $k => $v) {
			$binds[':' . $k] = $v;
			$sql .= ' and ' . $k . '=:' . $k;
		}
		
		return $this->db->select($sql, $binds);
	}
	
	/**
	 * @note 删除支付账户与场馆的关系
	 *
	 * @param array $params
	 * @return bool
	 * @author wy
	 */
	public function deleteMappingByParams($params) {
		$sql = 'delete from ' . $this->_tableName . ' where 1 ';
		
		$binds = array();
		foreach($params as $k => $v) {
			$binds[':' . $k] = $v;
			$sql .= ' and ' . $k . '=:' . $k;
		}
		return $this->db->delete($sql, $binds);
	}
}
?>