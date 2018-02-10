<?php
/**
 * 支付宝同步返回接收接口
 * @author huwl
 */
class Controller_Ali_Syncnotify extends Controller_Base {
	
	/**
     * 接收支付同步通知(订单)
     * @author wy
     */
    public function orderPayNotification() {
        //file_put_contents('order_pay_notifiy.txt', var_export($_GET, true));
        $tradeOrderNo = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : '';
        //file_put_contents('trade_order_no.txt', var_export($tradeOrderNo, true));
       
        $enviroment = isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : '';
        $aliUrl = $enviroment == 'online' ? WX_ONLINE_URL : WX_TEST_URL;
        
        $temArr = explode('x', $tradeOrderNo);
        $type = $temArr[0];
        $oid = $temArr[1];
        
        //订单跳转页
        if(strstr($type, 'o')) {
            http::go($aliUrl.'/payment/paysuccess?oid='.$oid.'&type=1');
        } else {
            http::go($aliUrl.'/payment/payfail?oid='.$oid.'&type=1');
        }
    }
    
	/**
     * 接收支付同步通知(会员卡充值)
     * @author wy
     */
    public function cardRechargePayNotification() {
        //file_put_contents('card_pay_notifiy.txt', var_export($_GET, true));
        $tradeOrderNo = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : '';
        //file_put_contents('trade_order_no.txt', var_export($tradeOrderNo, true));
       
        $enviroment = isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : '';
        $aliUrl = $enviroment == 'online' ? WX_ONLINE_URL : WX_TEST_URL;
        
        $temArr = explode('x', $tradeOrderNo);
        $type = $temArr[0];
        $cardId = $temArr[1];
        
        //会员卡跳转页
        if(strstr($type, 'c')) {
            http::go($aliUrl."/payment/cardPaysuccess?card_id=".$cardId.'&type=1');
        }else{
            http::go($aliUrl."/payment/cardPayfail?card_id=".$cardId.'&money='.$_GET['total_amount'].'&type=1');
        } 
    }
    
	/**
     * 接收支付同步通知(会员卡购买)
     * @author wy
     */
    public function cardBuyPayNotification() {
        $tradeOrderNo = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : '';
        $enviroment = isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : '';
        $aliUrl = $enviroment == 'online' ? WX_ONLINE_URL : WX_TEST_URL;
        $temArr = explode('x', $tradeOrderNo);
        $type = $temArr[0];
        $cardId = $temArr[1];
        
        //会员卡跳转页
        if(strstr($type, 'c')) {
            http::go($aliUrl."/payment/cardPaysuccess?card_id=".$cardId.'&type=1');
        }else{
            http::go($aliUrl."/payment/cardPayfail?card_id=".$cardId.'&money='.$_GET['total_amount'].'&type=1');
        } 
    }
    
    /**
     * 接收订单交易支付同步通知
     * @author wy
     */
    public function orderDealPayNotification() {
        //file_put_contents('order_pay_notifiy.txt', var_export($_GET, true));
        $tradeOrderNo = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : '';
        //file_put_contents('trade_order_no.txt', var_export($tradeOrderNo, true));
         
        $enviroment = isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : '';
        $aliUrl = $enviroment == 'online' ? WX_ONLINE_URL : WX_TEST_URL;
    
        $temArr = explode('x', $tradeOrderNo);
        $type = $temArr[0];
        $oid = $temArr[1];
        $orderSellId = $temArr[3];
        
        $orderSellInfo = Remote::instance()->get(ORDER_DOMIAN, 'order/sell/detail', array('order_sell_id' => $orderSellId));
        $orderSell = $orderSellInfo['data']['orderSell'];
        
        //订单跳转页
        if(strstr($type, 'o')) {
            http::go($aliUrl.'/payment/dealOrderPaysuccess?oid='.$oid.'&type=1&buy_phone='.$orderSell['buy_phone']);
        } else {
            http::go($aliUrl.'/payment/dealOrderPayfail?oid='.$oid.'&type=1&buy_phone='.$orderSell['buy_phone'].'&order_sell_id='.$orderSellId);
        }
    }
    
    /**
     * 接收支付同步通知(订单APP)
     * @author wy
     */
    public function orderAppPayNotification() {
        //file_put_contents('order_pay_notifiy.txt', var_export($_GET, true));
        $tradeOrderNo = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : '';
        //file_put_contents('trade_order_no.txt', var_export($tradeOrderNo, true));
         
        $enviroment = isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : '';
        $aliUrl = $enviroment == 'online' ? WX_ONLINE_URL : WX_TEST_URL;
    
        $temArr = explode('x', $tradeOrderNo);
        $type = $temArr[0];
        $oid = $temArr[1];
    
        //订单跳转页
        if(strstr($type, 'o')) {
            //http::go($aliUrl."/payment/paysuccess?oid=".$oid);
        }else{
            //http::go($aliUrl."/payment/payfail?oid=".$oid);
        }
    }
    /***
     * 赛事报名
     * 
     */
    public function registrationDealPayNotification() {
        //file_put_contents('order_pay_notifiy.txt', var_export($_GET, true));
        $tradeOrderNo = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : '';
        //file_put_contents('trade_order_no.txt', var_export($tradeOrderNo, true));
         
        $enviroment = isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : '';
        $aliUrl = $enviroment == 'online' ? WX_ONLINE_URL : WX_MATCH_URL;
        $temArr = explode('x', $tradeOrderNo);
        $type = $temArr[0];
        $registrationId = $temArr[1];
        
        //订单跳转页
        if(strstr($type, 'm')) {
            http::go($aliUrl.'/contest/registration/paySuccess?contest_registration_id='.$registrationId.'&type=1');
        } else {
            http::go($aliUrl.'/contest/registration/payFail?contest_registration_id='.$registrationId.'&type=1');
        }
    }
}
?>