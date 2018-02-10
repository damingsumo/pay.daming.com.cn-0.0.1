<?php
/**
 * 订单
 *
 */
class Model_Order extends Model {
	//订单来源
	public static $platformArray = array (1 => '微信', 2 => 'Android', 3 => 'IOS', 4 => 'PC', 5 => '电话', 6 => 'pos', 7 => '场馆' );
	public static $statusArray = array (-3 => '逾期未下场', - 2 => '已退订', - 1 => '已取消', 0 => '待支付', 1 => '已支付', 2 => '已完成' );
	public static $unsubscribeStatusArray = array(3 => '退款中', 4 => '已退款');
}
?>