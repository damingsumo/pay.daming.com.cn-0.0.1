<?php
require_once 'framework/plugins/alipay/aop/AopClient.php';
header("Content-type: text/html; charset=UTF-8");
/**
 * 支付宝支付接口
 * @author huwl
 */
class Controller_Alipayment extends Controller_Base {
	
	/**
	 * 订单APP支付 
	 * 
	 * @param int oid
	 * @param int payment_account_id
	 * @author wy
	 * @return json
	 */
	public function actionOrderAppPay() {
	    $oid = isset($_REQUEST['oid']) ? $_REQUEST['oid'] : 0;
	    $paymentAccountId = isset($_REQUEST['payment_account_id']) ? $_REQUEST['payment_account_id'] : 0;
	    
	    if($oid <= 0) {
	        return $this->error('订单ID错误');
	    }
	    if($paymentAccountId <= 0) {
	        return $this->error('支付账户ID错误');
	    }
	    
	    //获取订单信息
	    $order = Remote::instance()->get(ORDER_DOMIAN, 'order/detail', array('oid' => $oid));
	    if($order['code'] != 200) {
	        return $this->error($order['msg']);
	    }
	    
	    $order = $order['data']['order'];
	    if($order['status'] != 0) {
	        return $this->error('订单状态错误,不允许支付');
	    }
	    
	    //获取支付账户信息
	    $paymentAccount = WebApi_Ali_Account::instance()->row('*', $paymentAccountId);
	    
	    if(empty($paymentAccount)) {
	        return $this->error('支付账户信息错误');
	    }
	    
	    if($paymentAccount['status'] != 1) {
	        return $this->error('支付账户未启用');
	    }
	    
	    require_once 'framework/plugins/alipay/aop/request/AlipayTradeAppPayRequest.php';
	    $aop = new AopClient();
	    $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
	    $aop->appId = $paymentAccount['appid'];
	    $aop->rsaPrivateKey = $paymentAccount['rsa_private_key'];
	    $aop->alipayPublicKey = $paymentAccount['alipay_public_key'];
	    $aop->apiVersion = '1.0';
	    $aop->postCharset='UTF-8';
	    $aop->format='json';
	    $request = new AlipayTradeAppPayRequest ();
	    $outTradeNo = date("YmdHis",time()).'ox'.$oid.'x'.$paymentAccountId;
	    $subject = $order['venue_name'];
	    $totalAmount = $order['pay_money']/100;
	    $request->setBizContent("{" .
	        "    \"body\":\"场馆预订\"," .
	        "    \"subject\":\"$subject\"," .
	        "    \"out_trade_no\":\"$outTradeNo\"," .
	        "    \"timeout_express\":\"90m\"," .
	        "    \"total_amount\":\"$totalAmount\"," .
	        "    \"product_code\":\"QUICK_APP_PAY\"" .
	        "  }");
	    
	    $request->setNotifyUrl(HOME_URL.'ali/asyncnotify/orderAppPayNotification');
	    $request->setReturnUrl(HOME_URL.'ali/syncnotify/orderAppPayNotification');
	    $aliData = $aop->sdkExecute($request);
	    
	    $data = array();
	    $data['order'] = $order;
	    $data['ali_data'] = $aliData;
	    return $this->output($data);
	}
	
	/**
	 * 订单web支付
	 * 
	 * @param int oid
	 * @param int payment_account_id
	 * @author huwl
	 * @return html
	 */
	public function actionOrderWebPay() {
		$oid = isset($_REQUEST['oid']) ? $_REQUEST['oid'] : 0;
		$paymentAccountId = isset($_REQUEST['payment_account_id']) ? $_REQUEST['payment_account_id'] : 0;
		$httpmethod = isset($_REQUEST['httpmethod']) ? $_REQUEST['httpmethod'] : 'POST';
		
		if($oid <= 0) {
			return $this->error('订单ID错误');
		}
		if($paymentAccountId <= 0) {
			return $this->error('支付账户ID错误');
		}
		
		//获取订单信息
		$order = Remote::instance()->get(ORDER_DOMIAN, 'order/detail', array('oid' => $oid));
		if($order['code'] != 200) {
			return $this->error($order['msg']);
		}
		
		$order = $order['data']['order'];
		if($order['status'] != 0) {
			return $this->error('订单状态错误,不允许支付');
		}
		
		//获取支付账户信息
		$paymentAccount = WebApi_Ali_Account::instance()->row('*', $paymentAccountId);
		
		if(empty($paymentAccount)) {
			return $this->error('支付账户信息错误');
		}
		
		if($paymentAccount['status'] != 1) {
			return $this->error('支付账户未启用');
		}
		
		require_once 'framework/plugins/alipay/aop/request/AlipayTradeWapPayRequest.php';
		$aop = new AopClient();
		$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
		$aop->appId = $paymentAccount['appid'];
		$aop->rsaPrivateKey = $paymentAccount['rsa_private_key'];
		$aop->alipayPublicKey = $paymentAccount['alipay_public_key'];
		$aop->apiVersion = '1.0';
		$aop->postCharset='UTF-8';
		$aop->format='json';
		$request = new AlipayTradeWapPayRequest ();
		$outTradeNo = date("YmdHis",time()).'ox'.$oid.'x'.$paymentAccountId;
		$subject = $order['venue_name'];
		$totalAmount = $order['pay_money']/100;
		$request->setBizContent("{" .
		"    \"body\":\"场馆预订\"," .
		"    \"subject\":\"$subject\"," .
		"    \"out_trade_no\":\"$outTradeNo\"," .
		"    \"timeout_express\":\"90m\"," .
		"    \"total_amount\":\"$totalAmount\"," .
		"    \"product_code\":\"QUICK_WAP_PAY\"" .
		"  }");
		
		$request->setNotifyUrl(HOME_URL.'ali/asyncnotify/orderPayNotification');
		$request->setReturnUrl(HOME_URL.'ali/syncnotify/orderPayNotification');
		$res = $aop->pageExecute($request, $httpmethod);
		if($httpmethod == 'POST') {
		    print_r($res);
		} else {
		    return $this->output($res);
		}
	}
	
	/**
	 * 会员卡充值web支付
	 *
	 * @param int card_id
	 * @param int payment_account_id
	 * @param int money(单位分)支付宝存入单位为元
	 * @author wy
	 * @return html
	 */
	public function actionCardRechargeWebPay() {
	    $cardId = isset($_REQUEST['card_id']) ? $_REQUEST['card_id'] : 0;
	    $paymentAccountId = isset($_REQUEST['payment_account_id']) ? $_REQUEST['payment_account_id'] : 0;
	    $money = isset($_REQUEST['money']) ? $_REQUEST['money'] : 0;
	
	    if($cardId <= 0) {
            return $this->error('会员卡号错误');
        }
	    if($paymentAccountId <= 0) {
	        return $this->error('支付账户ID错误');
	    }
	    if($money <= 0) {
	        return $this->error('金额必须大于零');
	    }
	
	    //获取会员卡信息
	    $card = Remote::instance()->get(CARD_DOMIAN, 'card/detail', array('card_id' => $cardId));
	    if($card['code'] != 200) {
	        return $this->error($card['msg']);
	    }
	    
	    $card = $card['data']['card'];
	    if($card['status'] != 1) {
	        return $this->error('会员卡不可用,不允许支付');
	    }
	
	    //获取支付账户信息
	    $paymentAccount = WebApi_Ali_Account::instance()->row('*', $paymentAccountId);
	
	    if(empty($paymentAccount)) {
	        return $this->error('支付账户信息错误');
	    }
	
	    if($paymentAccount['status'] != 1) {
	        return $this->error('支付账户未启用');
	    }
	
	    require_once 'framework/plugins/alipay/aop/request/AlipayTradeWapPayRequest.php';
	    $aop = new AopClient();
	    $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
	    $aop->appId = $paymentAccount['appid'];
	    $aop->rsaPrivateKey = $paymentAccount['rsa_private_key'];
	    $aop->alipayPublicKey = $paymentAccount['alipay_public_key'];
	    $aop->apiVersion = '1.0';
	    $aop->postCharset='UTF-8';
	    $aop->format='json';
	    $request = new AlipayTradeWapPayRequest ();
	    $outTradeNo = date("YmdHis",time()).'cx'.$cardId.'x'.$paymentAccountId;
	    $subject = $card['cardTypeInfo']['name'].'-'.$card['number'];
	    $totalAmount = $money/100;
	    $request->setBizContent("{" .
        "    \"body\":\"会员卡充值\"," .
        "    \"subject\":\"$subject\"," .
        "    \"out_trade_no\":\"$outTradeNo\"," .
        "    \"timeout_express\":\"90m\"," .
        "    \"total_amount\":\"$totalAmount\"," .
        "    \"product_code\":\"QUICK_WAP_PAY\"" .
        "  }");
		
	    $request->setNotifyUrl(HOME_URL.'ali/asyncnotify/cardRechargePayNotification');
	    $request->setReturnUrl(HOME_URL.'ali/syncnotify/cardRechargePayNotification');
	    $res = $aop->pageExecute($request);
	    print_r($res);
	}
	
	/**
	 * 会员卡购买web支付
	 *
	 * @param int card_id
	 * @param int payment_account_id
	 * @param int money(单位分)支付宝存入单位为元
	 * @author wy
	 * @return html
	 */
	public function actionCardBuyWebPay() {
	    $cardId = isset($_REQUEST['card_id']) ? $_REQUEST['card_id'] : 0;
	    $paymentAccountId = isset($_REQUEST['payment_account_id']) ? $_REQUEST['payment_account_id'] : 0;
	    $money = isset($_REQUEST['money']) ? $_REQUEST['money'] : 0;
	    $uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : 0;
	
	    if($cardId <= 0) {
            return $this->error('会员卡号错误');
        }
	    if($paymentAccountId <= 0) {
	        return $this->error('支付账户ID错误');
	    }
	    if($money <= 0) {
	        return $this->error('金额必须大于零');
	    }
	 	if($uid <= 0) {
	        return $this->error('用户ID错误');
	    }
	
	    //获取会员卡信息
	    $card = Remote::instance()->get(CARD_DOMIAN, 'card/detail', array('card_id' => $cardId));
	    if($card['code'] != 200) {
	        return $this->error($card['msg']);
	    }
	    
	    $card = $card['data']['card'];
	    //获取支付账户信息
	    $paymentAccount = WebApi_Ali_Account::instance()->row('*', $paymentAccountId);
	
	    if(empty($paymentAccount)) {
	        return $this->error('支付账户信息错误');
	    }
	
	    if($paymentAccount['status'] != 1) {
	        return $this->error('支付账户未启用');
	    }
	
	    require_once 'framework/plugins/alipay/aop/request/AlipayTradeWapPayRequest.php';
	    $aop = new AopClient();
	    $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
	    $aop->appId = $paymentAccount['appid'];
	    $aop->rsaPrivateKey = $paymentAccount['rsa_private_key'];
	    $aop->alipayPublicKey = $paymentAccount['alipay_public_key'];
	    $aop->apiVersion = '1.0';
	    $aop->postCharset='UTF-8';
	    $aop->format='json';
	    $request = new AlipayTradeWapPayRequest ();
	    $outTradeNo = date("YmdHis",time()).'cx'.$cardId.'x'.$paymentAccountId.'x'.$uid;
	    $subject = $card['cardTypeInfo']['name'].'-'.$card['number'];
	    $totalAmount = $money/100;
	    $request->setBizContent("{" .
        "    \"body\":\"会员卡购买\"," .
        "    \"subject\":\"$subject\"," .
        "    \"out_trade_no\":\"$outTradeNo\"," .
        "    \"timeout_express\":\"90m\"," .
        "    \"total_amount\":\"$totalAmount\"," .
        "    \"product_code\":\"QUICK_WAP_PAY\"" .
        "  }");
		
	    $request->setNotifyUrl(HOME_URL.'ali/asyncnotify/cardBuyPayNotification');
	    $request->setReturnUrl(HOME_URL.'ali/syncnotify/cardBuyPayNotification');
	    $res = $aop->pageExecute($request);
	    print_r($res);
	}
	
	/**
	 * 订单交易支付
	 *
	 * @param int oid
	 * @param int payment_account_id
	 * @author wy
	 * @return html
	 */
	public function actionOrderDealPay() {
	    $oid = isset($_REQUEST['oid']) ? $_REQUEST['oid'] : 0;
	    $paymentAccountId = isset($_REQUEST['payment_account_id']) ? $_REQUEST['payment_account_id'] : 0;
	    $httpmethod = isset($_REQUEST['httpmethod']) ? $_REQUEST['httpmethod'] : 'POST';
	    
	    if($oid <= 0) {
	        return $this->error('订单ID错误');
	    }
	    if($paymentAccountId <= 0) {
	        return $this->error('支付账户ID错误');
	    }
	    
	    //校验售卖信息
	    $orderSellsInfo = Remote::instance()->get(ORDER_DOMIAN, 'order/sell/list', array('oids' => $oid, 'status' => '1', 'time' => date('Y-m-d')));
	    if($orderSellsInfo['code'] != 200 || empty($orderSellsInfo['data']['orderSells'])) {
	        return $this->errorPay('该订单无有效的转售信息');
	    }
	    $orderSell = reset($orderSellsInfo['data']['orderSells']);
	    if($orderSell['uid'] == $orderSell['buy_uid']) {
	        return $this->errorPay('用户无法购买自己转售的订单');
	    }
	    if($orderSell['price'] <= 0) {
	        return $this->errorPay('订单转售价格错误');
	    }
	    if($orderSell['buy_uid'] <= 0) {
	        return $this->errorPay('购买用户ID错误');
	    }
	    if(empty($orderSell['buy_phone'])) {
	        return $this->errorPay('购买用户电话号错误');
	    }
	    
	    //获取订单信息
	    $order = Remote::instance()->get(ORDER_DOMIAN, 'order/detail', array('oid' => $oid));
	    if($order['code'] != 200) {
	        return $this->error($order['msg']);
	    }
	    $order = $order['data']['order'];
	    if($order['status'] != 1) {
	        return $this->error('订单状态错误,不允许支付');
	    }
	    
	    //获取支付账户信息
	    $paymentAccount = WebApi_Ali_Account::instance()->row('*', $paymentAccountId);
	    if(empty($paymentAccount) || $paymentAccount['status'] != 1 || $paymentAccount['channel'] != 1 || $paymentAccount['pay_type'] != 1) {
	        return $this->error('支付宝平台支付账户信息错误');
	    }
	
	    require_once 'framework/plugins/alipay/aop/request/AlipayTradeWapPayRequest.php';
	    $aop = new AopClient();
	    $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
	    $aop->appId = $paymentAccount['appid'];
	    $aop->rsaPrivateKey = $paymentAccount['rsa_private_key'];
	    $aop->alipayPublicKey = $paymentAccount['alipay_public_key'];
	    $aop->apiVersion = '1.0';
	    $aop->postCharset='UTF-8';
	    $aop->format='json';
	    $request = new AlipayTradeWapPayRequest ();
	    $outTradeNo = date("YmdHis",time()).'ox'.$oid.'x'.$paymentAccountId.'x'.$orderSell['order_sell_id'];
	    $subject = $order['venue_name'];
	    $totalAmount = $orderSell['price']/100;
	    $request->setBizContent("{" .
	        "    \"body\":\"订单转售\"," .
	        "    \"subject\":\"$subject\"," .
	        "    \"out_trade_no\":\"$outTradeNo\"," .
	        "    \"timeout_express\":\"90m\"," .
	        "    \"total_amount\":\"$totalAmount\"," .
	        "    \"product_code\":\"QUICK_WAP_PAY\"" .
	        "  }");
	
	    $request->setNotifyUrl(HOME_URL.'ali/asyncnotify/orderDealPayNotification');
	    $request->setReturnUrl(HOME_URL.'ali/syncnotify/orderDealPayNotification');
	    $res = $aop->pageExecute($request, $httpmethod);
	    if($httpmethod == 'POST') {
	        print_r($res);
	    } else {
	        return $this->output($res);
	    }
	}
	
	/**
	 * 订单退款
	 *
	 * @param int oid
	 * @param int payment_account_id
	 * @return json
	 * @author huwl
	 */
	public function actionOrderRefund() {
		$oid = isset($_REQUEST['oid']) ? $_REQUEST['oid'] : 0;
		$paymentAccountId = isset($_REQUEST['payment_account_id']) ? $_REQUEST['payment_account_id'] : 0;
		
		if($oid <= 0) {
			return $this->error('订单ID错误');
		}
		if($paymentAccountId <= 0) {
			return $this->error('支付账户ID错误');
		}
		
		//获取订单信息
		$order = Remote::instance()->get(ORDER_DOMIAN, 'order/detail', array('oid' => $oid));
		if($order['code'] != 200) {
			return $this->error($order['msg']);
		}
		$order = $order['data']['order'];
		
		//获取订单支付信息
		$orderPayments = WebApi_Ali_Order_Payment::instance()->getOrderPaymentsByParams(array('oid' => $oid), 1, -1);
		if(empty($orderPayments)) {
			return $this->error('订单支付信息错误');
		}
		$orderPayment = current($orderPayments);
		
		//获取支付账户信息
		$paymentAccount = WebApi_Ali_Account::instance()->row('*', $paymentAccountId);
		if(empty($paymentAccount)) {
			return $this->error('支付账户信息错误');
		}
		if($paymentAccount['status'] != 1) {
			return $this->error('支付账户未启用');
		}
		
		require_once 'framework/plugins/alipay/aop/request/AlipayTradeRefundRequest.php';
		$aop = new AopClient();
		$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
		$aop->appId = $paymentAccount['appid'];
		$aop->rsaPrivateKey = $paymentAccount['rsa_private_key'];
		$aop->alipayrsaPublicKey = $paymentAccount['rsa_public_key'];
		$aop->alipayPublicKey = $paymentAccount['alipay_public_key'];
		$aop->apiVersion = '1.0';
		$aop->postCharset='UTF-8';
		$aop->format='json';
		$request = new AlipayTradeRefundRequest();
		$outTradeNo = $orderPayment['out_trade_no'];
		$tradeNo = $orderPayment['trade_no'];
		$refundAmount = $order['pay_money']/100;
		$request->setBizContent("{" .
		"    \"out_trade_no\":\"$outTradeNo\"," .
		"    \"trade_no\":\"$tradeNo\"," .
		"    \"refund_amount\":$refundAmount," .
		"    \"refund_reason\":\"正常退款\"," .
		"    \"out_request_no\":\"HZ01RF001\"," .
		"    \"operator_id\":\"OP001\"," .
		"    \"store_id\":\"NJ_S_001\"," .
		"    \"terminal_id\":\"NJ_T_001\"" .
		"  }");
		
		$result = $aop->execute($request); 
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode) && $resultCode == 10000){
			$data = array(
					'buyer_logon_id' => $result->$responseNode->buyer_logon_id,
					'buyer_user_id' => $result->$responseNode->buyer_user_id,
					'fund_change' => $result->$responseNode->fund_change,
					'gmt_refund_pay' => $result->$responseNode->gmt_refund_pay,
					'open_id' => $result->$responseNode->open_id,
					'out_trade_no' => $result->$responseNode->out_trade_no,
					'refund_fee' => $result->$responseNode->refund_fee,
					'send_back_fee' => $result->$responseNode->send_back_fee,
					'trade_no' => $result->$responseNode->trade_no,
				);
			
		///////////////////////////////////////////商户的业务操作///////////////////////
			$msg = Msgtpl::msg('UNSUBSCRIB_SUCCESS', $order);
			
			//添加退款信息数据
			$refundData = array();
			$refundData['payment_account_id'] = $paymentAccountId;
			$refundData['uid'] = $order['uid'];
			$refundData['oid'] = $oid;
			$refundData['stadium_id'] = $order['stadium_id'];
			$refundData['venue_id'] = $order['venue_id'];
			$refundData['buyer_logon_id'] = $data['buyer_logon_id'];
			$refundData['refund_money'] = $data['refund_fee']*100;
			$refundData['fund_change'] = $data['fund_change'];
			$refundData['out_trade_no'] = $data['out_trade_no'];
			$refundData['trade_no'] = $data['trade_no'];
			$refundData['pay_type'] = $orderPayment['pay_type'];
			$refundData['channel'] = $orderPayment['channel'];
			WebApi_Ali_Order_Refund::instance()->add($refundData);
			return $this->output($data);
		} 
		
		return $this->error($result->$responseNode->msg);
	}
	/**
	 * 赛事支付
	 * 
	 */
	public function actionRegistration() {
	    $registrationId = isset($_REQUEST['contest_registration_id']) ? $_REQUEST['contest_registration_id'] : 0;
	    $paymentAccountId = isset($_REQUEST['payment_account_id']) ? $_REQUEST['payment_account_id'] : 0;
	    $httpmethod = isset($_REQUEST['httpmethod']) ? $_REQUEST['httpmethod'] : 'POST';
	    $uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : 0;
	    
	    if($registrationId <= 0) {
	        return $this->errorPay('订单ID错误');
	    }
	    if($paymentAccountId <= 0) {
	        return $this->errorPay('支付账户ID错误');
	    }
	     
	    //获取订单信息
        $registration = Remote::instance()->get(MATCH_DOMIAN, 'contest/registration/detail', array('registration_id' => $registrationId));
	    if($registration['code'] != 200) {
	        return $this->errorPay($registration['msg']);
	    }
	    $registration = $registration['data']['registration'];
	    if($registration['status'] != 1) {
	        return $this->error('报名状态错误');
	    }
	    
	    $contest = Remote::instance()->get(MATCH_DOMIAN, 'contest/detail', array('contest_id' => $registration['contest_id']));
	    if($contest['code'] != 200) {
	        return $this->errorPay($contest['msg']);
	    }
	    $contest = $contest['data']['contest'];
	    if($contest['status'] != 1) {
	        return $this->error('赛事状态错误');
	    }
	    
	    $paymentAccount = WebApi_Ali_Account::instance()->row('*', $paymentAccountId);
	    
	    if(empty($paymentAccount)) {
	        return $this->error('支付账户信息错误');
	    }
	    
	    if($paymentAccount['status'] != 1) {
	        return $this->error('支付账户未启用');
	    }
	
	    require_once 'framework/plugins/alipay/aop/request/AlipayTradeWapPayRequest.php';
	    $aop = new AopClient();
	    $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
	    $aop->appId = $paymentAccount['appid'];
	    $aop->rsaPrivateKey = $paymentAccount['rsa_private_key'];
	    $aop->alipayPublicKey = $paymentAccount['alipay_public_key'];
	    $aop->apiVersion = '1.0';
	    $aop->postCharset='UTF-8';
	    $aop->format='json';
	    $request = new AlipayTradeWapPayRequest ();
	    $outTradeNo = date("YmdHis",time()).'mx'.$registrationId.'x'.$paymentAccountId.'x'.$uid;
	    $subject = $contest['name'];
	    $totalAmount = 0.01;
	    $request->setBizContent("{" .
        "    \"body\":\"报名支付\"," .
        "    \"subject\":\"$subject\"," .
        "    \"out_trade_no\":\"$outTradeNo\"," .
        "    \"timeout_express\":\"90m\"," .
        "    \"total_amount\":\"$totalAmount\"," .
        "    \"product_code\":\"QUICK_WAP_PAY\"" .
        "  }");
		
	    $request->setNotifyUrl(HOME_URL.'ali/asyncnotify/registrationDealPayNotification');
	    $request->setReturnUrl(HOME_URL.'ali/syncnotify/registrationDealPayNotification');
	    $res = $aop->pageExecute($request, $httpmethod);
	    if($httpmethod == 'POST') {
	        print_r($res);
	    } else {
	        return $this->output($res);
	    }
	}
}
?>