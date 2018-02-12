<?php
class CoreApi_Order extends CoreApi {

    protected static $instance__; //必要,
    protected $_module = 'yd_res';
    protected $_tableName = 'orders';
    protected $_primaryKey = 'oid';
    protected $_fields = array(
        'oid' => 'int',
        'uid' => 'int',
        'logistics_id' => 'int',
        'brand_id' => 'int',
        'brand_name' => 'string',
        'status' => 'string',
        'money' => 'string',
        'pay_money' => 'string',
        'logistics_id'=>'int',
        'pay_method' => 'string',
        'pay_time' => 'string',
        'reduce_money' => 'int',
        'refund_money' => 'int',
        'refund_method' => 'int',
        'refund_time' => 'string',
        'create_time' => 'string',
        'update_time' => 'string',
    );
    
    public function getOrdersByParams($params, $page, $pagesize, $returnFormat = 'Array', $order = "create_time", $desc = "desc") {
        $sql = 'select * from ' . $this->_tableName . ' where 1 ';
        $binds = array();
        if(!empty($params)) {
            foreach($params as $k => $v) {
                $binds[':' . $k] = $v;
                $sql .= ' and ' . $k . '=:' . $k;
            }
        }
    
        $sql .= ' order by ' . $order . ' ' . $desc;
        return $this->db->page($sql, $binds, $page, $pagesize, $returnFormat);
    }
    
    public function getOrdersCountByParams($params) {
        $sql = 'select count(*) as total from ' . $this->_tableName . ' where 1 ';
        $binds = array();
        foreach($params as $k => $v) {
            $binds[':' . $k] = $v;
            $sql .= ' and ' . $k . '=:' . $k;
        }
        $result = $this->db->select_one($sql, $binds);
        return isset($result['total']) ? $result['total'] : 0;
    }
    
}