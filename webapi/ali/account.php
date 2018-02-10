<?php
/**
 * 支付宝账户管理
 * @author wy
 * @version 1.0.0
 */
class WebApi_Ali_Account extends WebApi {

    static public $instance__;

    static public function &instance () {
        if (!isset(self::$instance__)) {
            $class = __CLASS__;
            self::$instance__ = new $class;
        }
        return self::$instance__;
    }
	
    /**
     * 通过条件去查询支付宝账户列表
     *
     * @param array $params 参数
     * @param integer $page 第几页
     * @param integer $pagesize 条数
     * @param string $returnType 返回数据类型
     * @param $order 排序字段
     * @param $sequence desc|asc
     * @return arrayObject|array
     * @author wy
     */
    public function getAliAccountsByParams($params, $page, $pagesize, $returnType = 'Model_Stadium_Account', $order = 'payment_account_id', $sequence = 'desc') {
        if(!is_array($params) || $page <= 0) { //没有条件的时候也允许返回数据
            return $returnType == 'Model_Stadium_Account' ? new ArrayObject() : array();
        }
    
        return CoreApi_Ali_Account::instance()->getAliAccountsByParams($params, $page, $pagesize, $returnType, $order, $sequence);
    }
    
    /**
     * 通过条件查询支付宝账户数量
     *
     * @param array $params 查询参数
     * @return int
     * @author wy
     */
    public function getAliAccountCountByParams($params) {
        if(!is_array($params)) {
            return 0;
        }
    
        return CoreApi_Ali_Account::instance()->getAliAccountCountByParams($params);
    }
    
	/**
	 * 编辑支付宝账户
	 *
	 * @param array $params
	 * @param int $paymentAccountId
	 * @return bool
	 * @author wy
	 */
	public function edit($params, $paymentAccountId) {
		if(!is_array($params) || empty($params) || $paymentAccountId <= 0) {
			return false;
		}
		
		$aliAccount = CoreApi_Ali_Account::instance()->row('*', $paymentAccountId);
		if(empty($aliAccount)) {
		    return false;
		}
		
		$data = array();
		$data['appid'] = isset($params['appid']) ? $params['appid'] : $aliAccount['appid'];
		$data['name'] = isset($params['name']) ? $params['name'] : $aliAccount['name'];
		$data['partner'] = isset($params['partner']) ? $params['partner'] : $aliAccount['partner'];
		$data['seller_id'] = isset($params['seller_id']) ? $params['seller_id'] : $aliAccount['seller_id'];
		$data['seller_email'] = isset($params['seller_email']) ? $params['seller_email'] : $aliAccount['seller_email'];
		$data['private_key'] = isset($params['private_key']) ? $params['private_key'] : $aliAccount['private_key'];		
		$data['alipay_public_key'] = isset($params['alipay_public_key']) ? $params['alipay_public_key'] : $aliAccount['alipay_public_key'];	
		$data['sign_type'] = isset($params['sign_type']) ? $params['sign_type'] : $aliAccount['sign_type'];
		$data['cacert_path'] = isset($params['cacert_path']) ? $params['cacert_path'] : $aliAccount['cacert_path'];		
		$data['pay_type'] = isset($params['pay_type']) ? $params['pay_type'] : $aliAccount['pay_type'];
		$data['channel'] = isset($params['channel']) ? $params['channel'] : $aliAccount['channel'];
		$data['status'] = isset($params['status']) ? $params['status'] : $aliAccount['status'];
		$data['update_time'] = date('Y-m-d H:i:s');
		return CoreApi_Ali_Account::instance()->update($data, $paymentAccountId);
	}
	
	/**
	 * 新增账户
	 *
	 * @param array $params
	 * @return bool
	 * @author wy
	 */
	public function add($params) {
	    if(!is_array($params) || empty($params)) {
			return false;
		}
		
		$data['appid'] = isset($params['appid']) ? $params['appid'] : '';
		$data['name'] = isset($params['name']) ? $params['name'] : '';
		$data['partner'] = isset($params['partner']) ? $params['partner'] : '';
		$data['seller_id'] = isset($params['seller_id']) ? $params['seller_id'] : '';
		$data['seller_email'] = isset($params['seller_email']) ? $params['seller_email'] : '';
		$data['private_key'] = isset($params['private_key']) ? $params['private_key'] : '';		
		$data['alipay_public_key'] = isset($params['alipay_public_key']) ? $params['alipay_public_key'] : '';	
		$data['sign_type'] = isset($params['sign_type']) ? $params['sign_type'] : 'RSA';
		$data['cacert_path'] = isset($params['cacert_path']) ? $params['cacert_path'] : '';		
		$data['pay_type'] = isset($params['pay_type']) ? $params['pay_type'] : 1;
		$data['channel'] = isset($params['channel']) ? $params['channel'] : 1;
		$data['status'] = isset($params['status']) ? $params['status'] : 1;
		$data['update_time'] = '0000-00-00 00:00:00';
		$data['create_time'] = date('Y-m-d H:i:s');
		return CoreApi_Ali_Account::instance()->insert($data);
	}
}
?>