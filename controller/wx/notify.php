<?php
/**
 * 微信异步返回接收接口
 * @author huwl
 */
class Controller_Wx_Notify extends Controller_Base {
	
	/**
     * 接收异步通知
     */
    public function receiptNotification() {
        include_once(FW_PATH."/plugins/wxpay/WxPayPubHelper.php");
        $key = WX_KEY;
        //使用通用通知接口
        $notify = new Notify_pub();
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);
        //@todo日志
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if($notify->checkSign($key) == FALSE) {
            $notify->setReturnParameter("return_code", "FAIL");//返回状态码
            $notify->setReturnParameter("return_msg", "签名失败");//返回信息
        } else {
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        //商户处理后同步返回给微信参数
        echo $returnXml;
        //@todo日志
        
        //==商户根据实际情况设置相应的处理流程，此处仅作举例=======
        if($notify->checkSign($key) == TRUE) {
            if($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
            } elseif($notify->data["result_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
            } else {
                $callBackData = $notify->data;
                //此处应该更新一下订单状态，商户自行增删操作
                $tradeOrderNo = isset($callBackData['out_trade_no']) ? $callBackData['out_trade_no'] : 0;
                $temArr = explode('x', $tradeOrderNo);
                $paymentAccountId = $temArr[2];
                
                $paymentAccount = WebApi_Wx_Account::instance()->row('*', $paymentAccountId);
                if(empty($paymentAccount)) {
                    echo '支付账户信息错误';exit;
                }
                
                if(strstr($temArr[0], 'o')) {
                    $oid = $temArr[1];
                    $orderInfo = Remote::instance()->get(ORDER_DOMIAN, 'order/detail', array('oid' => $oid));
                    if($orderInfo['code'] != 200) {
                        echo $orderInfo['msg'];exit;
                    }
                    
                    $order = $orderInfo['data']['order'];
                    if($order['status'] != 0) {
                        echo 'success';exit;
                    }
                    
                    //更新订单,明细 ,凭证状态,推送订单消息
                    $res = Remote::instance()->post(ORDER_DOMIAN, 'order/pay', array('oid' => $oid, 'pay_channel' => 1));
                    if($res['code'] != 200) {
                        echo $res['msg'];exit;
                    }
                    
                    //添加订单支付信息 
                    $orderData = array();
                    $orderData['payment_account_id'] = $paymentAccountId;
                    $orderData['uid'] = $order['uid'];
                    $orderData['oid'] = $order['oid'];
                    $orderData['stadium_id'] = $order['stadium_id'];
                    $orderData['venue_id'] = $order['venue_id'];
                    $orderData['stadium_name'] = $order['stadium_name'];
                    $orderData['venue_name'] = $order['venue_name'];
                    $orderData['open_id'] = $paymentAccount['wx_appid'];
                    $orderData['out_trade_no'] = $tradeOrderNo;
                    $orderData['trade_no'] = $callBackData['transaction_id'];
                    $orderData['money'] = $order['pay_money'];
                    $orderData['pay_type'] = $paymentAccount['pay_type'];
                    $orderData['channel'] = $paymentAccount['channel'];
                    $orderRes = WebApi_Wx_Order_Payment::instance()->add($orderData);
                    if($orderRes == false) {
                        echo '添加订单支付信息失败';exit;
                    }
                    
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
                    
                } elseif (strstr($temArr[0], 'm')) {
                    $registrationId = $temArr[1];
                    $res = Remote::instance()->get(MATCH_DOMIAN, 'contest/registration/changeStatus', array('registration_id' => $registrationId,'status'=>3));
                    file_put_contents('111.txt', var_export($res, TRUE));
                    if($res['code'] != 200) {
                        return $this->errorPay($res['msg']);
                    }
                    $registration = Remote::instance()->get(MATCH_DOMIAN, 'contest/registration/detail', array('registration_id' => $registrationId));
                    if($registration['code'] != 200) {
                        return $this->errorPay($registration['msg']);
                    }
                    $registration = $registration['data']['registration'];
                    file_put_contents('222.txt', var_export($registration, TRUE));
                    if($registration['status'] != 3) {
                        return $this->error('订单状态错误');
                    }
                    $contest = Remote::instance()->get(MATCH_DOMIAN, 'contest/detail', array('contest_id' => $registration['contest_id']));
                    if($contest['code'] != 200) {
                        return $this->errorPay($contest['msg']);
                    }
                    $contest = $contest['data']['contest'];
                    file_put_contents('333.txt', var_export($contest, TRUE));
                    $club = Remote::instance()->get(MATCH_DOMIAN, 'club/detail', array('club_id' => $contest['club_id']));
                    if($club['code'] != 200) {
                        return $this->errorPay($club['msg']);
                    }
                    $club = $club['data'];
                    file_put_contents('444.txt', var_export($club, TRUE));
                    $clubId = Remote::instance()->get(MATCH_DOMIAN, 'club/changeMoney', array('balance'=>$club['balance']+$registration['money'], 'club_id' => $club['club_id']));
                    file_put_contents('555.txt', var_export($clubId, TRUE));
                    if($clubId['code'] != 200) {
                        return $this->errorPay($clubId['msg']);
                    }
                    //添加订单支付信息
                    $orderData = array();
                    $orderData['payment_account_id'] = $paymentAccountId;
                    $orderData['uid'] = $registration['uid'];
                    $orderData['registration_id'] = $registration['contest_registration_id'];
                    $orderData['club_id'] = $club['club_id'];
                    $orderData['contest_id'] = $contest['contest_id'];
                    $orderData['club_name'] = $club['name'];
                    $orderData['contest_name'] = $contest['name'];
                    $orderData['open_id'] = $paymentAccount['wx_appid'];
                    $orderData['out_trade_no'] = $tradeOrderNo;
                    $orderData['trade_no'] = $callBackData['transaction_id'];
                    $orderData['money'] = $registration['money'];
                    $orderData['pay_type'] = $paymentAccount['pay_type'];
                    $orderData['channel'] = $paymentAccount['channel'];
                    $orderRes = WebApi_Wx_Contest_Payment::instance()->add($orderData);
                    if($orderRes == false) {
                        echo '添加订单支付信息失败';exit;
                    }
                    
                } elseif(strstr($temArr[0], 'd')) {//订单交易转让
                    $oid = $temArr[1];
                    //订单信息
                    $order = Remote::instance()->get(ORDER_DOMIAN, 'order/detail', array('oid' => $oid));
                    if($order['code'] != 200) {
                        echo $order['msg'];exit;
                    }
                    $order = $order['data']['order'];
                    
                    //订单售卖信息
                    $orderSellsInfo = Remote::instance()->get(ORDER_DOMIAN, 'order/sell/list', array('oids' => $oid, 'status' => '1', 'time' => date('Y-m-d')));
                    if($orderSellsInfo['code'] != 200 || empty($orderSellsInfo['data']['orderSells'])) {
                        echo '该订单无有效的转售信息';exit;
                    }
                    $orderSell = reset($orderSellsInfo['data']['orderSells']);
                    if($orderSell['buy_uid'] <= 0 || empty($orderSell['buy_phone'])) {
                        echo '用户绑定信息错误';exit;
                    }
                    
                    //添加订单交易支付信息
                    $orderData = array();
                    $orderData['payment_account_id'] = $paymentAccountId;
                    $orderData['uid'] = $orderSell['buy_uid'];
                    $orderData['oid'] = $oid;
                    $orderData['stadium_id'] = $order['stadium_id'];
                    $orderData['venue_id'] = $order['venue_id'];
                    $orderData['stadium_name'] = $order['stadium_name'];
                    $orderData['venue_name'] = $order['venue_name'];
                    $orderData['open_id'] = $paymentAccount['wx_appid'];
                    $orderData['out_trade_no'] = $tradeOrderNo;
                    $orderData['trade_no'] = $callBackData['transaction_id'];
                    $orderData['money'] = $orderSell['price'];
                    $orderData['pay_type'] = $paymentAccount['pay_type'];
                    $orderData['channel'] = $paymentAccount['channel'];
                    $orderRes = WebApi_Wx_Order_Deal::instance()->add($orderData);
                    if($orderRes == false) {
                        echo '添加订单交易支付信息失败';exit;
                    }
                    
                    $orderTransactionRes = Remote::instance()->post(ORDER_DOMIAN, 'order/transaction', array('oid' => $oid, 'pay_channel' => 1));
                    if($orderTransactionRes['code'] != 200) {
                        echo $orderTransactionRes['msg'];exit;
                    }
                } else {
                    $cardId = $temArr[1];
                	//获取会员卡信息
                    $card = Remote::instance()->get(CARD_DOMIAN, 'card/detail', array('card_id' => $cardId));
                    if($card['code'] != 200) {
                        echo $card['msg'];exit;
                    }
                    $card = $card['data']['card'];
                    
                 	//获取该会员卡对应的场馆信息
                   	$cardTypeStadiuminfo = Remote::instance()->get(CARD_DOMIAN, 'cardtype/getCardtypestadiumByParams', array('card_type_id' => $card['card_type_id']));
					if($cardTypeStadiuminfo['code'] != 200) {
					    echo $cardTypeStadiuminfo['msg'];exit;
					}
					$cardTypeStadium = reset($cardTypeStadiuminfo['data']);
					$stadium = Remote::instance()->get(RES_DOMIAN, 'stadium/detail', array('stadium_id' => $cardTypeStadium['stadium_id']));
					if($stadium['code'] != 200) {
					    echo $stadium['msg'];exit;
					}
					
					//添加会员卡支付信息
                    $cardPaymentData = array();
                    $cardPaymentData['payment_account_id'] = $paymentAccountId;
                    $cardPaymentData['card_id'] = $card['card_id'];
                    $cardPaymentData['card_type_id'] = $card['card_type_id'];
                    $cardPaymentData['stadium_id'] = $stadium['data']['stadium']['stadium_id'];
                    $cardPaymentData['open_id'] = $paymentAccount['wx_appid'];
                    $cardPaymentData['card_name'] = $card['name'];
                    $cardPaymentData['card_number'] = $card['number'];
                    $cardPaymentData['card_type_name'] = $card['cardTypeInfo']['name'];
                    $cardPaymentData['stadium_name'] = $stadium['data']['stadium']['name'];
                    $cardPaymentData['out_trade_no'] = $tradeOrderNo;
                    $cardPaymentData['trade_no'] = $callBackData['transaction_id'];
                    $cardPaymentData['money'] = $callBackData['total_fee'];
                    $cardPaymentData['pay_type'] = $paymentAccount['pay_type'];
                    $cardPaymentData['channel'] = $paymentAccount['channel'];
					 
                    if($temArr[3] == 'r') {//会员卡充值
                        //添加会员卡充值
                        $res = Remote::instance()->post(CARD_DOMIAN, 'card/recharge/recharge', array('card_id' => $cardId, 'source' => '1', 'money' => $callBackData['total_fee']/100));
                        if($res['code'] != 200) {
                            echo $res['msg'];exit;
                        }
                        
                        $cardPaymentData['card_recharge_id'] = $res['data']['card_recharge_id'];
                        $cardPaymentData['pay_source'] = 1;
                        $cardPaymentData['uid'] = $card['uid'];
                    } else if($temArr[3] == 'b') {//购买会员卡
                     	//添加会员卡购买
                        $res = Remote::instance()->post(CARD_DOMIAN, 'card/recharge/buy', array('card_id' => $cardId, 'source' => '1', 'money' => $callBackData['total_fee']/100, 'type' => 2));
                        if($res['code'] != 200) {
                            echo $res['msg'];exit;
                        }
                        
                        $cardRechargeId = $res['data']['card_recharge_id'];
                        
                    	//将会员卡绑定到用户上
                    	$uid = isset($temArr[4]) ? $temArr[4] : 0;
                    	if($uid <= 0) {
                    		echo '用户ID错误';exit;
                    	}
                    	
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
                        
                        
                        $cardPaymentData['uid'] = $uid;
                        $cardPaymentData['pay_source'] = 2;//支付来源 1会员卡充值 2会员卡购买 3缴年费
                        $cardPaymentData['card_recharge_id'] = $cardRechargeId;
                    }
                    
                	$cardRes = WebApi_Wx_Card_Payment::instance()->add($cardPaymentData);
                    if($cardRes == false) {
                        echo '添加会员卡支付信息失败';exit;
                    }
                }
            }
        }
    }
}
?>