<?php
/**
 * 支付宝异步返回接收接口
 * @author huwl
 */
class Controller_Ali_Asyncnotify extends Controller_Base {
	
	/**
     * 接收支付异步通知(会员卡充值)
     * @param string out_trade_no
     * @param int total_amount
     * @param int payment_account_id
     * @author wy
     * @return json
     */
    public function cardRechargePayNotification() {
        $tradeOrderNo = isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : '';
        $tradeStatus = isset($_POST['trade_status']) ? $_POST['trade_status'] : '';
        
        if($tradeStatus != 'TRADE_SUCCESS') {
        	file_put_contents('error_card_recharge_log.txt', var_export($_POST, TRUE));exit;
        }
        
        $temArr = explode('x', $tradeOrderNo);
        $cardId = $temArr[1];
        $paymentAccountId = $temArr[2];
        
        //获取支付信息
        $cardPayments = WebApi_Ali_Card_Payment::instance()->getCardPaymentsByParams(array('trade_no' => $_POST['trade_no']), 1, -1);
        if(!empty($cardPayments)) {
        	echo 'success';exit;
        }
        
        //获取会员卡信息
        $card = Remote::instance()->get(CARD_DOMIAN, 'card/detail', array('card_id' => $cardId));
        if($card['code'] != 200) {
            return $this->error($card['msg']);
        }
        //添加会员卡充值
        $res = Remote::instance()->post(CARD_DOMIAN, 'card/recharge/recharge', array('card_id' => $cardId , 'source' => '1', 'money' => $_POST['total_amount'])); 
        if($res['code'] != 200) {
            return $this->error($res['msg']);
        }
        $cardRechargeId = $res['data']['card_recharge_id'];
        
        $card = $card['data']['card'];
        $cardTypeId = $card['cardTypeInfo']['card_type_id'];
        $cardTypeStadiuminfo = Remote::instance()->get(CARD_DOMIAN, 'cardtype/getCardtypestadiumByParams', array('card_type_id' => $cardTypeId));
        if($cardTypeStadiuminfo['code'] != 200) {
            return $this->error($cardTypeStadiuminfo['msg']);
        }
        $cardTypeStadium = $cardTypeStadiuminfo['data'];
        $stadiumId = $cardTypeStadium[0]['stadium_id'];
        $stadium = Remote::instance()->get(RES_DOMIAN, 'stadium/detail', array('stadium_id' => $stadiumId));
        if($stadium['code'] != 200) {
            return $this->error($stadium['msg']);
        }
        $stadiumName = $stadium['data']['stadium']['name']; 
        //获取支付账户信息
        $paymentAccount = WebApi_Ali_Account::instance()->row('*', $paymentAccountId);
        if(empty($paymentAccount)) {
            return $this->error('支付账户信息错误');
        }
        
        //添加支付宝会员卡支付记录
        $data = array();
        $data['card_recharge_id'] = $cardRechargeId;
        $data['payment_account_id'] = $paymentAccountId;
        $data['uid'] = $card['uid'];
        $data['card_id'] = $cardId;
        $data['card_type_id'] = $card['card_type_id'];
        $data['stadium_id'] = $stadiumId;
        $data['card_name'] = $card['name'];
        $data['card_number'] = $card['number'];
        $data['card_type_name'] = $card['cardTypeInfo']['name'];
        $data['stadium_name'] = $stadiumName;
        $data['out_trade_no'] = $tradeOrderNo;
        $data['trade_no'] = isset($_POST['trade_no']) ? $_POST['trade_no'] : 0;
        $data['buyer_id'] = isset($_POST['buyer_id']) ? $_POST['buyer_id'] : 0;
        $data['buyer_email'] = isset($_POST['buyer_logon_id']) ? $_POST['buyer_logon_id'] : 0;
        $data['seller_id'] = $paymentAccount['seller_id'];
        $data['seller_email'] = $paymentAccount['seller_email'];
        $data['money'] = $_POST['total_amount']*100;
        $data['pay_type'] = $paymentAccount['pay_type'];
        $data['channel'] = $paymentAccount['channel'];
        $data['pay_source'] = 1;//会员卡充值
        WebApi_Ali_Card_Payment::instance()->add($data);
    }
    
	/**
     * 接收支付异步通知(会员卡购买)
     * @param string out_trade_no
     * @param int total_amount
     * @param int payment_account_id
     * @author wy
     * @return json
     */
    public function cardBuyPayNotification() {
        $tradeOrderNo = isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : '';
        $tradeStatus = isset($_POST['trade_status']) ? $_POST['trade_status'] : '';
        
        if($tradeStatus != 'TRADE_SUCCESS') {
        	file_put_contents('error_card_recharge_log.txt', var_export($_POST, TRUE));exit;
        }
        
        $temArr = explode('x', $tradeOrderNo);
        $cardId = isset($temArr[1]) ? $temArr[1] : 0;
        $paymentAccountId = isset($temArr[2]) ? $temArr[2] : 0;
        $uid = isset($temArr[3]) ? $temArr[3] : 0;
        
        //获取支付信息
        $cardPayments = WebApi_Ali_Card_Payment::instance()->getCardPaymentsByParams(array('trade_no' => $_POST['trade_no']), 1, -1);
        if(!empty($cardPayments)) {
        	echo 'success';exit;
        }
        
        //获取会员卡信息
        $card = Remote::instance()->get(CARD_DOMIAN, 'card/detail', array('card_id' => $cardId));
        if($card['code'] != 200) {
            echo '会员卡信息错误';exit;
        }
        
        //获取该会员卡所属场馆
        $card = $card['data']['card'];
        $cardTypeStadiuminfo = Remote::instance()->get(CARD_DOMIAN, 'cardtype/getCardtypestadiumByParams', array('card_type_id' => $card['card_type_id']));
        if($cardTypeStadiuminfo['code'] != 200) {
        	echo '场馆信息错误';exit;
        }
        
        $cardTypeStadium = reset($cardTypeStadiuminfo['data']);
        $stadium = Remote::instance()->get(RES_DOMIAN, 'stadium/detail', array('stadium_id' => $cardTypeStadium['stadium_id']));
        if($stadium['code'] != 200) {
            echo '场馆信息错误';exit;
        }
        
        //获取支付账户信息
        $paymentAccount = WebApi_Ali_Account::instance()->row('*', $paymentAccountId);
        if(empty($paymentAccount)) {
            echo '支付账户信息错误';exit;
        }
        
        //将会员卡绑定到用户上
        $params = array();
    	$params['uid'] = $uid;
    	$params['card_id'] = $cardId;
    	$params['sex'] = $card['sex'];
    	$params['name'] = $card['name'];
    	$params['mobile'] = $card['mobile'];
    	$params['card_type_id'] = $card['card_type_id'];
    	$params['card_id'] = $cardId;
    	$params['status'] = 1;
        $res = Remote::instance()->post(CARD_DOMIAN, 'card/edit', $params);
        if($res['code'] != 200) {
            echo $res['msg'];exit;
        }
        
    	//添加会员卡充值
        $res = Remote::instance()->post(CARD_DOMIAN, 'card/recharge/buy', array('card_id' => $cardId , 'source' => '1', 'money' => $_POST['total_amount'], 'type' => 2)); 
        if($res['code'] != 200) {
            echo $res['msg'];exit;
        }
        
        //添加支付宝会员卡支付记录
        $data = array();
        $data['payment_account_id'] = $paymentAccountId;
        $data['card_recharge_id'] = $res['data']['card_recharge_id'];
        $data['uid'] = $uid;
        $data['card_id'] = $cardId;
        $data['card_type_id'] = $card['card_type_id'];
        $data['stadium_id'] = $cardTypeStadium['stadium_id'];
        $data['card_name'] = $card['name'];
        $data['card_number'] = $card['number'];
        $data['card_type_name'] = $card['cardTypeInfo']['name'];
        $data['stadium_name'] = $stadium['data']['stadium']['name'];
        $data['out_trade_no'] = $tradeOrderNo;
        $data['trade_no'] = isset($_POST['trade_no']) ? $_POST['trade_no'] : 0;
        $data['buyer_id'] = isset($_POST['buyer_id']) ? $_POST['buyer_id'] : 0;
        $data['buyer_email'] = isset($_POST['buyer_logon_id']) ? $_POST['buyer_logon_id'] : 0;
        $data['seller_id'] = $paymentAccount['seller_id'];
        $data['seller_email'] = $paymentAccount['seller_email'];
        $data['money'] = $_POST['total_amount']*100;
        $data['pay_type'] = $paymentAccount['pay_type'];
        $data['channel'] = $paymentAccount['channel'];
        $data['pay_source'] = 2;//会员卡购买
        WebApi_Ali_Card_Payment::instance()->add($data);
    }
    
    /**
     * 接收支付异步通知(订单)
     * @param string out_trade_no
     * @param int total_amount
     * @param int payment_account_id
     * @author wy
     * @return json
     */
    public function orderPayNotification() {
        $tradeOrderNo = isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : '';
        $tradeStatus = isset($_POST['trade_status']) ? $_POST['trade_status'] : '';
        
        if($tradeStatus != 'TRADE_SUCCESS') {
            file_put_contents('error_card_recharge_log.txt', var_export($_POST, TRUE));exit;
        }
        
        $temArr = explode('x', $tradeOrderNo);
        $oid = $temArr[1];
        $paymentAccountId = $temArr[2];
        
        $orderInfo = Remote::instance()->get(ORDER_DOMIAN, 'order/detail', array('oid' => $oid));
        if($orderInfo['code'] != 200) {
            echo $orderInfo['msg'];exit;
        }
        
        $order = $orderInfo['data']['order'];
        if($order['status'] != 0) {
            echo 'success';exit;
        }
        
        //更新订单,明细 ,凭证状态,推送订单消息
        $res = Remote::instance()->post(ORDER_DOMIAN, 'order/pay', array('oid' => $oid ,'pay_channel' => 5));
        if($res['code'] != 200) {
            return $this->error($res['msg']);
        } 
        
        //获取支付账户信息
        $paymentAccount = WebApi_Ali_Account::instance()->row('*', $paymentAccountId);
        if(empty($paymentAccount)) {
            return $this->error('支付账户信息错误');
        }
        //添加支付宝订单支付记录
        $data = array();
        $data['payment_account_id'] = $paymentAccountId;
        $data['uid'] = $order['uid'];
        $data['oid'] = $order['oid'];
        $data['stadium_id'] = $order['stadium_id'];
        $data['venue_id'] = $order['venue_id'];
        $data['stadium_name'] = $order['stadium_name'];
        $data['venue_name'] = $order['venue_name'];
        $data['out_trade_no'] = $tradeOrderNo;
        $data['trade_no'] = isset($_POST['trade_no']) ? $_POST['trade_no'] : 0;
        $data['buyer_id'] = isset($_POST['buyer_id']) ? $_POST['buyer_id'] : 0;
        $data['buyer_email'] = isset($_POST['buyer_logon_id']) ? $_POST['buyer_logon_id'] : 0;
        $data['seller_id'] = $paymentAccount['seller_id'];
        $data['seller_email'] = $paymentAccount['seller_email'];
        $data['money'] = $order['pay_money'];
        $data['pay_type'] = $paymentAccount['pay_type'];
        $data['channel'] = $paymentAccount['channel'];
        WebApi_Ali_Order_Payment::instance()->add($data);
        
        $stadium = $orderInfo['data']['stadium'];
        if($stadium['is_use_system'] == 1) {
            //向用户推送预定成功短信
            Msgtpl::msg('BADMINTON_PAY_SUCCESS', $order);
            //向场馆推送短信提醒
            $orderItemStr = '';
            foreach($order['items'] as $orderItemVal) {
                if(!strstr($orderItemVal['start_hour'], ':')) {
                    $startHour = $orderItemVal['start_hour'] > 9 ? $orderItemVal['start_hour'].':00' : '0'.$orderItemVal['start_hour'].':00';
                    $endHour = $orderItemVal['end_hour'] > 9 ? $orderItemVal['end_hour'].':00' : '0'.$orderItemVal['end_hour'].':00';
                } else {
                    $startHour = $orderItemVal['start_hour'];
                    $endHour = $orderItemVal['end_hour'];
                }
        
                $orderItemStr .= $orderItemVal['piece_name'].'，'.$startHour.'-'.$endHour.'，';
            }
            $orderItemStr = trim($orderItemStr, '，');
             
            $params = array($order['oid'], $order['stadium_name'], $order['venue_name'], $order['book_day'], $orderItemStr);
             
            //发送推送短信
            $stadiumAccounts = Remote::instance()->post(ACCOUNT_DOMIAN, 'stadium/account/getStadiumAccounts', array('stadium_id' => $order['stadium_id']));
            if($stadiumAccounts['code'] != 200) {
                echo $stadiumAccounts['msg'];exit;
            }
        
            $stadiumAccounts = $stadiumAccounts['data'];
            if(!empty($stadiumAccounts)) {
                foreach($stadiumAccounts as $stadiumAccount) {
                    if($stadiumAccount['is_admin'] == 1) {
                        sms::send($stadiumAccount['mobile'], $params, '135821');
                    }
                }
            }
        } else {//向客服推送订单提醒
            Remote::instance()->post(WX_URL, 'weixin/push/orderPaySuccessService', array('oid' => $oid));
        }
        
    } 
    
    /**
     * 接收订单交易支付异步通知
     * @param string out_trade_no
     * @param int total_amount
     * @param int payment_account_id
     * @author wy
     * @return json
     */
    public function orderDealPayNotification() {
        $tradeOrderNo = isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : '';
        $tradeStatus = isset($_POST['trade_status']) ? $_POST['trade_status'] : '';
        
        if($tradeStatus != 'TRADE_SUCCESS') {
            file_put_contents('error_card_recharge_log.txt', var_export($_POST, TRUE));exit;
        }
        
        $temArr = explode('x', $tradeOrderNo);
        $oid = $temArr[1];
        $paymentAccountId = $temArr[2];
        
        //获取支付账户信息
        $paymentAccount = WebApi_Ali_Account::instance()->row('*', $paymentAccountId);
        if(empty($paymentAccount)) {
            echo '支付账户信息错误';exit;
        }
        
        //订单信息
        $order = Remote::instance()->get(ORDER_DOMIAN, 'order/detail', array('oid' => $oid));
        if($order['code'] != 200) {
            echo $order['msg'];exit;
        }
        $order = $order['data']['order'];
        
        //订单售卖信息
        $orderSellsInfo = Remote::instance()->get(ORDER_DOMIAN, 'order/sell/list', array('oids' => $oid, 'status' => '1', 'time' => date('Y-m-d')));
        if($orderSellsInfo['code'] != 200 || empty($orderSellsInfo['data']['orderSells'])) {
            echo '该订单无有效的售卖信息';exit;
        }
        $orderSell = reset($orderSellsInfo['data']['orderSells']);
        if($orderSell['buy_uid'] <= 0 || empty($orderSell['buy_phone'])) {
            echo '用户绑定信息错误';exit;
        }
        
        //添加支付宝订单交易记录
        $data = array();
        $data['payment_account_id'] = $paymentAccountId;
        $data['uid'] = $orderSell['buy_uid'];
        $data['oid'] = $oid;
        $data['stadium_id'] = $order['stadium_id'];
        $data['venue_id'] = $order['venue_id'];
        $data['stadium_name'] = $order['stadium_name'];
        $data['venue_name'] = $order['venue_name'];
        $data['out_trade_no'] = $tradeOrderNo;
        $data['trade_no'] = isset($_POST['trade_no']) ? $_POST['trade_no'] : 0;
        $data['buyer_id'] = isset($_POST['buyer_id']) ? $_POST['buyer_id'] : 0;
        $data['buyer_email'] = isset($_POST['buyer_logon_id']) ? $_POST['buyer_logon_id'] : 0;
        $data['seller_id'] = $paymentAccount['seller_id'];
        $data['seller_email'] = $paymentAccount['seller_email'];
        $data['money'] = $orderSell['price'];
        $data['pay_type'] = $paymentAccount['pay_type'];
        $data['channel'] = $paymentAccount['channel'];
        $orderDealRes = WebApi_Ali_Order_Deal::instance()->add($data);
        if($orderDealRes == false) {
            echo '添加支付宝订单交易支付信息错误';exit;
        }
        
        $orderTransactionRes = Remote::instance()->post(ORDER_DOMIAN, 'order/transaction', array('oid' => $oid, 'pay_channel' => 5));
        if($orderTransactionRes['code'] != 200) {
            echo $orderTransactionRes['msg'];exit;
        }
    }
    
    /**
     * 接收支付异步通知(订单APP)
     * @param string out_trade_no
     * @param int total_amount
     * @param int payment_account_id
     * @author wy
     * @return json
     */
    public function orderAppPayNotification() {
        $tradeOrderNo = isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : '';
        $tradeStatus = isset($_POST['trade_status']) ? $_POST['trade_status'] : '';
        
        if($tradeStatus != 'TRADE_SUCCESS') {
            file_put_contents('error_card_recharge_log.txt', var_export($_POST, TRUE));exit;
        }
        
        $temArr = explode('x', $tradeOrderNo);
        $oid = $temArr[1];
        $paymentAccountId = $temArr[2];
        
        $order = Remote::instance()->get(ORDER_DOMIAN, 'order/detail', array('oid' => $oid));
        $order = $order['data']['order'];
         if($order['status'] != 0) {
            echo 'success';exit;
        }
        $msg = Msgtpl::msg('BADMINTON_PAY_SUCCESS', $order);
        
        //更新订单,明细 ,凭证状态,推送订单消息
        $res = Remote::instance()->post(ORDER_DOMIAN, 'order/pay', array('oid' => $oid ,'pay_channel' => 5));
        if($res['code'] != 200) {
            return $this->error($res['msg']);
        } 
        
        $orderItemStr = '';
        foreach($order['items'] as $orderItemVal){
            if(!strstr($orderItemVal['start_hour'], ':')) {
                $startHour = $orderItemVal['start_hour'] > 9 ? $orderItemVal['start_hour'].':00' : '0'.$orderItemVal['start_hour'].':00';
                $endHour = $orderItemVal['end_hour'] > 9 ? $orderItemVal['end_hour'].':00' : '0'.$orderItemVal['end_hour'].':00';
            } else {
                $startHour = $orderItemVal['start_hour'];
                $endHour = $orderItemVal['end_hour'];
            }
        
            $orderItemStr .= $orderItemVal['piece_name'].'，'.$startHour.'-'.$endHour.'，';
        }
        $orderItemStr = trim($orderItemStr, '，');
         
        $params = array($order['oid'], $order['stadium_name'], $order['venue_name'], $order['book_day'], $orderItemStr);
         
        //发送推送短信
        $stadiumAccounts = Remote::instance()->post(ACCOUNT_DOMIAN, 'stadium/account/getStadiumAccounts', array('stadium_id' => $order['stadium_id']));
        if($stadiumAccounts['code'] != 200) {
            return $this->error($stadiumAccounts['msg']);
        }
        $stadiumAccounts = $stadiumAccounts['data'];
        if(!empty($stadiumAccounts)) {
            foreach($stadiumAccounts as $stadiumAccount) {
                if($stadiumAccount['is_admin'] == 1) {
                    sms::send($stadiumAccount['mobile'], $params, '135821');
                }
            }
        } 
        
        //获取支付账户信息
        $paymentAccount = WebApi_Ali_Account::instance()->row('*', $paymentAccountId);
        if(empty($paymentAccount)) {
            return $this->error('支付账户信息错误');
        }
        //添加支付宝会员卡支付记录
        $data = array();
        $data['payment_account_id'] = $paymentAccountId;
        $data['uid'] = $order['uid'];
        $data['oid'] = $order['oid'];
        $data['stadium_id'] = $order['stadium_id'];
        $data['venue_id'] = $order['venue_id'];
        $data['stadium_name'] = $order['stadium_name'];
        $data['venue_name'] = $order['venue_name'];
        $data['out_trade_no'] = $tradeOrderNo;
        $data['trade_no'] = isset($_POST['trade_no']) ? $_POST['trade_no'] : 0;
        $data['buyer_id'] = isset($_POST['buyer_id']) ? $_POST['buyer_id'] : 0;
        $data['buyer_email'] = isset($_POST['buyer_logon_id']) ? $_POST['buyer_logon_id'] : 0;
        $data['seller_id'] = $paymentAccount['seller_id'];
        $data['seller_email'] = $paymentAccount['seller_email'];
        $data['money'] = $order['pay_money'];
        $data['pay_type'] = $paymentAccount['pay_type'];
        $data['channel'] = $paymentAccount['channel'];
        WebApi_Ali_Order_Payment::instance()->add($data);
    }    
    
    /**
     * 接收退款异步通知
     */
    public function refundNotification() {
        
    }
    
    public function registrationDealPayNotification() {
        $tradeOrderNo = isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : '';
        $tradeStatus = isset($_POST['trade_status']) ? $_POST['trade_status'] : '';
    
        if($tradeStatus != 'TRADE_SUCCESS') {
            file_put_contents('error_card_recharge_log.txt', var_export($_POST, TRUE));exit;
        }
    
        $temArr = explode('x', $tradeOrderNo);
        $registrationId = $temArr[1];
        $paymentAccountId = $temArr[2];
        //获取支付账户信息
        $paymentAccount = WebApi_Ali_Account::instance()->row('*', $paymentAccountId);
        if(empty($paymentAccount)) {
            echo '支付账户信息错误';exit;
        }
        
        $res = Remote::instance()->get(MATCH_DOMIAN, 'contest/registration/changeStatus', array('registration_id' => $registrationId,'status'=>3));
        if($res['code'] != 200) {
            return $this->errorPay($res['msg']);
        }
        $registration = Remote::instance()->get(MATCH_DOMIAN, 'contest/registration/detail', array('registration_id' => $registrationId));
        if($registration['code'] != 200) {
            return $this->errorPay($registration['msg']);
        }
        $registration = $registration['data']['registration'];
        if($registration['status'] != 3) {
            return $this->error('订单状态错误');
        }
        $contest = Remote::instance()->get(MATCH_DOMIAN, 'contest/detail', array('contest_id' => $registration['contest_id']));
        if($contest['code'] != 200) {
            return $this->errorPay($contest['msg']);
        }
        $contest = $contest['data']['contest'];
        $club = Remote::instance()->get(MATCH_DOMIAN, 'club/detail', array('club_id' => $contest['club_id']));
        if($club['code'] != 200) {
            return $this->errorPay($club['msg']);
        }
        $club = $club['data'];
        $clubId = Remote::instance()->get(MATCH_DOMIAN, 'club/changeMoney', array('balance'=>$club['balance']+$registration['money'], 'club_id' => $club['club_id']));
        if($clubId['code'] != 200) {
            return $this->errorPay($clubId['msg']);
        }
        //添加支付宝会员卡支付记录
        $data = array();
        $data['payment_account_id'] = $paymentAccountId;
        $data['uid'] = $registration['uid'];
        $data['contest_id'] = $registration['contest_id'];
        $data['club_id'] = $contest['club_id'];
        $data['club_name'] = $club['name'];
        $data['registration_id'] = $registration['contest_registration_id'];
        $data['contest_name'] = $contest['name'];
        $data['out_trade_no'] = $tradeOrderNo;
        $data['trade_no'] = isset($_POST['trade_no']) ? $_POST['trade_no'] : 0;
        $data['buyer_id'] = isset($_POST['buyer_id']) ? $_POST['buyer_id'] : 0;
        $data['buyer_email'] = isset($_POST['buyer_logon_id']) ? $_POST['buyer_logon_id'] : 0;
        $data['seller_id'] = $paymentAccount['seller_id'];
        $data['seller_email'] = $paymentAccount['seller_email'];
        $data['money'] = $contest['money'];
        $data['pay_type'] = $paymentAccount['pay_type'];
        $data['channel'] = $paymentAccount['channel'];
        WebApi_Ali_Contest_Payment::instance()->add($data);
    }
}
?>