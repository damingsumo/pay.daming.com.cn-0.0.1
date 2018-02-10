<?php
/**
 * 支付账户与场馆关联表
 * @author wy
 * @version 1.0.0
 */
class WebApi_Mapping extends WebApi {
	
	public static $instance__;
	
	static public function &instance() {
		if(!isset(self::$instance__)) {
			$class = __CLASS__;
			self::$instance__ = new $class();
		}
		return self::$instance__;
	}
	
	/**
	 * 获取支付账户绑定的场馆
	 *
	 * @param int $stadiumAccountId
	 * @return array
	 * @author wy
	 */
	public function getBindStadiumsByParam($paymentAccountId,$type) {
	    if($paymentAccountId <= 0 || $type <= 0) {
	        return array();
	    }
	
	    $stadiumMappings= CoreApi_Mapping::instance()->getMappingByParams(array('payment_account_id' => $paymentAccountId , 'type' => $type));
	    if(empty($stadiumMappings)) {
	        return array();
	    }
	
	    $stadiumIds = array();
	    foreach ($stadiumMappings as $stadiumMapping) {
	        $stadiumIds[] = $stadiumMapping['stadium_id'];
	    }
	    $stadiumIds = implode(',', $stadiumIds);
	    $stadiumsInfo = Remote::instance()->get(RES_DOMIAN, 'stadium/list', array('stadium_ids' => $stadiumIds));
	    if($stadiumsInfo['code'] == 400) {
	        return $this->error('场馆信息错误');
	    }
	    $stadiums = $stadiumsInfo['data']['stadiums'];
	
	    return $stadiums;
	}
	
	/**
	 * 根据条件获取支付账户与场馆关联信息
	 *
	 * @param array $params
	 * @return array
	 * @author wy
	 */
	public function getMappingByParams($params) {
		if(!is_array($params)) {
			return array();
		}
		
		return CoreApi_Mapping::instance()->getMappingByParams($params);
	}
	
	/**
	 * 添加支付账户与场馆关联
	 *
	 * @param array $params
	 * @return bool
	 * @author wy
	 */
	public function add($params) {
		if(!is_array($params) || empty($params)) {
			return false;
		}
		
		$data['payment_account_id'] = isset($params['payment_account_id']) ? $params['payment_account_id'] : 0;
		$data['stadium_id'] = isset($params['stadium_id']) ? $params['stadium_id'] : 0;
		$data['type'] = isset($params['type']) ? $params['type'] : 1;
		$data['create_time'] = date('Y-m-d H:i:s');
		$data['update_time'] = '0000-00-00 00:00:00';
		return CoreApi_Mapping::instance()->insert($data);
	}
	
	/**
	 * 删除支付账户与场馆的关系
	 *
	 * @param array $params
	 * @return bool
	 * @author wy
	 */
	public function deleteMappingByParams($params) {
		if(!is_array($params) || empty($params)) {
			return false;
		}
		
		return CoreApi_Mapping::instance()->deleteMappingByParams($params);
	}
}
?>