<?php
class WebApi_Order extends WebApi{

    private static $instance__;

    public static function &instance (){
        if (!isset(self::$instance__)) {
            $class = __CLASS__;
            self::$instance__ = new $class;
        }
        return self::$instance__;
    }
    
    public function add($data) {
        $params = array();
        $params['uid'] = isset($data['uid']) ? $data['uid'] : '';
        $params['logistics_id'] = isset($data['logistics_id']) ? $data['logistics_id'] : '';
        $params['brand_id'] = isset($data['brand_id']) ? $data['brand_id'] : 0;
        $params['brand_name'] = isset($data['brand_name']) ? $data['brand_name'] : '';
        $params['status'] = isset($data['status']) ? $data['status'] : 1;
        $params['money'] = isset($data['price']) ? $data['price'] : 0;
        $params['pay_money'] = isset($data['pay_money']) ? $data['pay_money'] : 0;
        $params['pay_method'] = isset($data['pay_method']) ? $data['pay_method'] : 2;
        $params['pay_time'] = isset($data['pay_time']) ? $data['pay_time'] : '';
        $params['reduce_money'] = isset($data['reduce_money']) ? $data['reduce_money'] : 0;
        $params['refund_money'] = isset($data['refund_money']) ? $data['refund_money'] : 0;
        $params['refund_method'] = isset($data['refund_method']) ? $data['refund_method'] : 0;
        $params['refund_time'] = isset($data['refund_time']) ? $data['refund_time'] : '';
        $params['create_time'] = date('Y-m-d H:i:s');
        $params['update_time'] = date('Y-m-d H:i:s');
        return CoreApi_Order::instance()->insert($params);
    }
    
    public function getOrdersByParams($params, $page = 1, $pageSize = -1) {
        if(!is_array($params)) {
            return array();
        }
        return CoreApi_Order::instance()->getOrdersByParams($params, $page, $pageSize);
    }
    
    
    public function getOrdersCountByParams($params) {
        if(!is_array($params)) {
            return false;
        }
        return CoreApi_Order::instance()->getOrdersCountByParams($params);
    }
}