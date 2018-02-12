<?php
/**
 * 微信支付接口
 * @author huwl
 */
class Controller_Wxpayment extends Controller_Base {
	
	public $WX_NOTIFY_URL = 'wx/notify/receiptNotification';
	
	
	public function orderPay() {
	    $oid = isset($_REQUEST['oid']) ? $_REQUEST['oid'] : 0;
	    $openid = isset($_REQUEST['open_id']) ? $_REQUEST['open_id'] : 'o9A6d0i0howwE7XB1A8i6tOWE0fQ';
	    if($oid <= 0) {
	        return $this->error('订单ID错误');
	    }
	    
	    $order = WebApi_Order::instance()->row('*',$oid);
	    if($order['status'] != 1 || $order['pay_money'] <= 0) {
	        return $this->error('该订单不允许支付');
	    }
	    $paymentAccount['wx_appid'] = WEIXIN_APPID;
	    $paymentAccount['mchid'] = '1498010952';
	    $paymentAccount['mch_key'] = 'daming1211';
	    require_once(FW_PATH."/plugins/wxpay/WxPayPubHelper.php");
	    $jsApi = new JsApi_pub();
	    //=========步骤：使用统一支付接口，获取prepay_id============
	    //使用统一支付接口
	    $unifiedOrder = new UnifiedOrder_pub();
	    //设置统一支付接口参数
	    //设置必填参数
	    //appid已填,商户无需重复填写
	    //mch_id已填,商户无需重复填写
	    //noncestr已填,商户无需重复填写
	    //spbill_create_ip已填,商户无需重复填写
	    //sign已填,商户无需重复填写
	    
	    $unifiedOrder->setParameter("openid", "$openid");//商品描述
	    $unifiedOrder->setParameter("body", $order['brand_name']);//商品描述
	    $unifiedOrder->setParameter("out_trade_no",date("YmdHis",time()).'ox'.$oid.'x'.$paymentAccount['mchid']);//商户订单号
	    $unifiedOrder->setParameter("total_fee", $order['pay_money']);//总金额
	    $unifiedOrder->setParameter("notify_url",HOME_URL.$this->WX_NOTIFY_URL);//通知地址
	    $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
	    
	    
	    
	    
// 	    $unifiedOrder->setParameter("body", $order['brand_name']);//商品描述
// 	    $unifiedOrder->setParameter("out_trade_no",date("YmdHis",time()).'ox'.$oid.'x'.$paymentAccount['mchid']);//商户订单号
// 	    $unifiedOrder->setParameter("total_fee", $order['pay_money']);//总金额
// 	    $unifiedOrder->setParameter("notify_url",HOME_URL.$this->WX_NOTIFY_URL);//通知地址
// 	    $unifiedOrder->setParameter("trade_type","APP");//交易类型
// 	    //非必填参数，商户可根据实际情况选填
// 	    //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
// 	    //$unifiedOrder->setParameter("device_info","XXXX");//设备号
	    //$unifiedOrder->setParameter("attach","XXXX");//附加数据
	    //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
	    //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
	    //$unifiedOrder->setParameter("goods_tag","ORDER");//商品标记
	    //$unifiedOrder->setParameter("openid","XXXX");//用户标识
	    //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
	     $prepay_id = $unifiedOrder->getPrepayId($paymentAccount['wx_appid'], $paymentAccount['mchid'], $paymentAccount['mch_key']);
        //=========步骤2：使用jsapi调起支付============
        $jsApi->setPrepayId($prepay_id);
        
        $enviroment = isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : '';
        $wxUrl = $enviroment == 'test' ? WX_ONLINE_URL : WX_TEST_URL;
        
        $data = array();
        $data['wxpay_data'] = $jsApi->getParameters($paymentAccount['wx_appid'], $paymentAccount['mch_key']);
        $data['order'] = $order;
        $data['expire_time'] = strtotime($order['create_time']) + 900 - time();
        $data['wx_url'] = $wxUrl;
//         $data['paymentAccountAli'] = $paymentAccountAli;
        return $this->display('wxpayment/topay', $data);
	    
	    
	}
	
	
	/**
	 * 订单APP支付
	 *
	 * @param int oid 必传
	 * @param int payment_account_id 必传
	 *
	 * @author spring
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
	    if($order['status'] != 0 || $order['pay_money'] <= 0) {
	        return $this->error('该订单不允许支付');
	    }
	     
	    //获取wx支付账户信息
	    $paymentAccount = WebApi_Wx_Account::instance()->row('*', $paymentAccountId);
	    if(empty($paymentAccount)) {
	        return $this->error('wx支付账户信息错误');
	    }
	    if($paymentAccount['status'] != 1) {
	        return $this->error('wx支付账户未启用');
	    }
	     
	    require_once(FW_PATH."/plugins/wxpay/WxPayPubHelper.php");
	    //=========步骤：使用统一支付接口，获取prepay_id============
	    //使用统一支付接口
	    $unifiedOrder = new UnifiedOrder_pub();
	    //设置统一支付接口参数
	    //设置必填参数
	    //appid已填,商户无需重复填写
	    //mch_id已填,商户无需重复填写
	    //noncestr已填,商户无需重复填写
	    //spbill_create_ip已填,商户无需重复填写
	    //sign已填,商户无需重复填写
	    $unifiedOrder->setParameter("body", $order['venue_name']);//商品描述
	    $unifiedOrder->setParameter("out_trade_no",date("YmdHis",time()).'ox'.$oid.'x'.$paymentAccountId);//商户订单号
	    $unifiedOrder->setParameter("total_fee", $order['pay_money']);//总金额
	    $unifiedOrder->setParameter("notify_url",HOME_URL.$this->WX_NOTIFY_URL);//通知地址
	    $unifiedOrder->setParameter("trade_type","APP");//交易类型
	    //非必填参数，商户可根据实际情况选填
	    //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
	    //$unifiedOrder->setParameter("device_info","XXXX");//设备号
	    //$unifiedOrder->setParameter("attach","XXXX");//附加数据
	    //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
	    //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
	    //$unifiedOrder->setParameter("goods_tag","ORDER");//商品标记
	    //$unifiedOrder->setParameter("openid","XXXX");//用户标识
	    //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
	    $payData = $unifiedOrder->createXml($paymentAccount['wx_appid'], $paymentAccount['mchid'], $paymentAccount['mch_key']);
	    
	    $data = array();
	    $data['order'] = $order;
	    $data['pay_data'] = $payData;
	    $data['expire_time'] = strtotime($order['create_time']) + 900 - time();
	    return $this->output($data);
	}
	
	/**
	 * 订单JS支付
	 * 
	 * @param int oid 必传
	 * @param string open_id 必传
	 * @param int payment_account_id 必传
	 * 
	 * @author spring
	 * @return json
	 */
	public function actionOrderWebPay() {
	    $oid = isset($_REQUEST['oid']) ? $_REQUEST['oid'] : 0;
	    $openid = isset($_REQUEST['open_id']) ? $_REQUEST['open_id'] : '';
	    $paymentAccountId = isset($_REQUEST['payment_account_id']) ? $_REQUEST['payment_account_id'] : 0;
	    
	    if($oid <= 0) {
	        return $this->errorPay('订单ID错误');
	    }
	    if($openid == '') {
	        return $this->errorPay('OPENID错误');
	    }
	    if($paymentAccountId <= 0) {
	        return $this->errorPay('支付账户ID错误');
	    }
	    
	    //获取订单信息
	    $order = Remote::instance()->get(ORDER_DOMIAN, 'order/detail', array('oid' => $oid));
	    if($order['code'] != 200) {
	        return $this->errorPay($order['msg']);
	    }
	    $order = $order['data']['order'];
	    
	    //获取wx支付账户信息
	    $paymentAccount = WebApi_Wx_Account::instance()->row('*', $paymentAccountId);
	    if(empty($paymentAccount)) {
	        return $this->errorPay('wx支付账户信息错误');
	    }
	    if($paymentAccount['status'] != 1) {
	        return $this->errorPay('wx支付账户未启用');
	    }
	    
	    //获取ali支付账户信息
	    $paymentAccountAli = $this->getAliPaymentAccount($order['stadium_id']);
	    
        require_once(FW_PATH."/plugins/wxpay/WxPayPubHelper.php");
        //使用jsapi接口
        $jsApi = new JsApi_pub();
        //=========步骤：使用统一支付接口，获取prepay_id============
        //使用统一支付接口
        $unifiedOrder = new UnifiedOrder_pub();
        //设置统一支付接口参数
        //设置必填参数
        //appid已填,商户无需重复填写
        //mch_id已填,商户无需重复填写
        //noncestr已填,商户无需重复填写
        //spbill_create_ip已填,商户无需重复填写
        //sign已填,商户无需重复填写
        $unifiedOrder->setParameter("openid", "$openid");//商品描述
        $unifiedOrder->setParameter("body", $order['venue_name']);//商品描述
        $unifiedOrder->setParameter("out_trade_no",date("YmdHis",time()).'ox'.$oid.'x'.$paymentAccountId);//商户订单号
        $unifiedOrder->setParameter("total_fee", $order['pay_money']);//总金额
        $unifiedOrder->setParameter("notify_url",HOME_URL.$this->WX_NOTIFY_URL);//通知地址
        $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
        //非必填参数，商户可根据实际情况选填
        //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
        //$unifiedOrder->setParameter("device_info","XXXX");//设备号
        //$unifiedOrder->setParameter("attach","XXXX");//附加数据
        //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
        //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
        //$unifiedOrder->setParameter("goods_tag","ORDER");//商品标记
        //$unifiedOrder->setParameter("openid","XXXX");//用户标识
        //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
        $prepay_id = $unifiedOrder->getPrepayId($paymentAccount['wx_appid'], $paymentAccount['mchid'], $paymentAccount['mch_key']);
        //=========步骤2：使用jsapi调起支付============
        $jsApi->setPrepayId($prepay_id);
        
        $enviroment = isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : '';
        $wxUrl = $enviroment == 'online' ? WX_ONLINE_URL : WX_TEST_URL;
        
        $data = array();
        $data['wxpay_data'] = $jsApi->getParameters($paymentAccount['wx_appid'], $paymentAccount['mch_key']);
        $data['order'] = $order;
        $data['expire_time'] = strtotime($order['create_time']) + 900 - time();
        $data['wx_url'] = $wxUrl;
        $data['paymentAccountAli'] = $paymentAccountAli;
        return $this->display('wxpayment/topay', $data);
	}
	
	/**
	 * 订单转售JS支付
	 *
	 * @param int oid 必传
	 * @param string open_id 必传
	 * @param int payment_account_id 必传
	 *
	 * @author spring
	 * @return json
	 */
	public function actionOrderDealWebPay() {
	    $oid = isset($_REQUEST['oid']) ? $_REQUEST['oid'] : 0;
	    $openid = isset($_REQUEST['open_id']) ? $_REQUEST['open_id'] : '';
	    $paymentAccountId = isset($_REQUEST['payment_account_id']) ? $_REQUEST['payment_account_id'] : 0;
	    
	    if($oid <= 0) {
	        return $this->errorPay('订单ID错误');
	    }
	    if($openid == '') {
	        return $this->errorPay('OPENID错误');
	    }
	    if($paymentAccountId <= 0) {
	        return $this->errorPay('支付账户ID错误');
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
	        return $this->errorPay($order['msg']);
	    }
	    $order = $order['data']['order'];
	    if($order['status'] != 1) {
            return $this->error('订单状态错误');
        }
	     
	    //获取wx平台js支付账户信息
	    $paymentAccount = WebApi_Wx_Account::instance()->row('*', $paymentAccountId);
	    if(empty($paymentAccount) || $paymentAccount['status'] != 1 || $paymentAccount['channel'] != 1 || $paymentAccount['pay_type'] != 1) {
	        return $this->errorPay('WX平台JS支付账户信息错误');
	    }
	    
	    //获取ali平台支付账户信息
	    $paymentAccounts = WebApi_Ali_Account::instance()->getAliAccountsByParams(array('status' => 1, 'channel' => 1, 'pay_type' => 1), 1, -1, 'Array');
	    if(empty($paymentAccounts)) {
	        return $this->errorPay('获取不到ali平台支付账户');
	    }
	    $paymentAccountAli = reset($paymentAccounts);
	     
	    require_once(FW_PATH."/plugins/wxpay/WxPayPubHelper.php");
	    //使用jsapi接口
	    $jsApi = new JsApi_pub();
	    //=========步骤：使用统一支付接口，获取prepay_id============
	    //使用统一支付接口
	    $unifiedOrder = new UnifiedOrder_pub();
	    //设置统一支付接口参数
	    //设置必填参数
	    //appid已填,商户无需重复填写
	    //mch_id已填,商户无需重复填写
	    //noncestr已填,商户无需重复填写
	    //spbill_create_ip已填,商户无需重复填写
	    //sign已填,商户无需重复填写
	    $unifiedOrder->setParameter("openid", "$openid");//商品描述
	    $unifiedOrder->setParameter("body", $order['venue_name']);//商品描述
	    $unifiedOrder->setParameter("out_trade_no",date("YmdHis",time()).'dx'.$oid.'x'.$paymentAccountId);//商户订单号
	    $unifiedOrder->setParameter("total_fee", $orderSell['price']);//售卖价格
	    $unifiedOrder->setParameter("notify_url",HOME_URL.$this->WX_NOTIFY_URL);//通知地址
	    $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
	    //非必填参数，商户可根据实际情况选填
	    //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
	    //$unifiedOrder->setParameter("device_info","XXXX");//设备号
	    //$unifiedOrder->setParameter("attach","XXXX");//附加数据
	    //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
	    //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
	    //$unifiedOrder->setParameter("goods_tag","ORDER");//商品标记
	    //$unifiedOrder->setParameter("openid","XXXX");//用户标识
	    //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
	    $prepay_id = $unifiedOrder->getPrepayId($paymentAccount['wx_appid'], $paymentAccount['mchid'], $paymentAccount['mch_key']);
	    //=========步骤2：使用jsapi调起支付============
	    $jsApi->setPrepayId($prepay_id);
	
	    $enviroment = isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : '';
	    $wxUrl = $enviroment == 'online' ? WX_ONLINE_URL : WX_TEST_URL;
	
	    $data = array();
	    $data['wxpay_data'] = $jsApi->getParameters($paymentAccount['wx_appid'], $paymentAccount['mch_key']);
	    $data['order'] = $order;
	    $data['orderSell'] = $orderSell;
	    $data['wx_url'] = $wxUrl;
	    $data['paymentAccountAli'] = $paymentAccountAli;
	    $data['expire_time'] = strtotime($orderSell['update_time']) + 600 - time();
	    return $this->display('wxpayment/deal_order_topay', $data);
	}
	
	/**
	 * 会员卡充值JS支付
	 * 
	 * @param int card_id 必传
	 * @param int payment_account_id 必传
	 * @param int money 必传
	 * @param string open_id 必传
	 * @param int stadium_id 必传
	 * 
	 * @author spring
	 * @return json
	 */
	public function actionCardRechargeWebPay() {
	    $cardId = isset($_REQUEST['card_id']) ? $_REQUEST['card_id'] : 0;
	    $paymentAccountId = isset($_REQUEST['payment_account_id']) ? $_REQUEST['payment_account_id'] : 0;
	    $money = isset($_REQUEST['money']) ? $_REQUEST['money'] : 0;
	    $openid = isset($_REQUEST['open_id']) ? $_REQUEST['open_id'] : '';
	    $stadiumId = isset($_REQUEST['stadium_id']) ? $_REQUEST['stadium_id'] : 0;
	    
	    if($cardId <= 0) {
	        return $this->errorPay('会员卡ID错误');
	    }
	    if($paymentAccountId <= 0) {
	        return $this->errorPay('支付账户ID错误');
	    }
	    if($money <= 0) {
	        return $this->errorPay('充值金额必须大于零');
	    }
	    if($openid == '') {
	        return $this->errorPay('OPENID错误');
	    }
        if($stadiumId <= 0) {
	        return $this->errorPay('场馆ID错误');
	    }
	    
	    //获取会员卡信息
	    $card = Remote::instance()->get(CARD_DOMIAN, 'card/detail', array('card_id' => $cardId));
	    if($card['code'] != 200) {
	        return $this->errorPay($card['msg']);
	    }
	    $card = $card['data']['card'];
	    if($card['status'] != 1) {
	        return $this->errorPay('会员卡不可用,不允许支付');
	    }
	    
	    $paymentAccount = WebApi_Wx_Account::instance()->row('*', $paymentAccountId);
	    if(empty($paymentAccount)) {
	        return $this->errorPay('支付账户信息错误');
	    }
	    if($paymentAccount['status'] != 1) {
	        return $this->errorPay('支付账户未启用');
	    }
	    
	    //获取ali支付账户信息
	    $paymentAccountAli = $this->getAliPaymentAccount($stadiumId);
	    
	    require_once(FW_PATH."/plugins/wxpay/WxPayPubHelper.php");
	    //使用jsapi接口
	    $jsApi = new JsApi_pub();
	    //=========步骤：使用统一支付接口，获取prepay_id============
	    //使用统一支付接口
	    $unifiedOrder = new UnifiedOrder_pub();
	    //设置统一支付接口参数
	    //设置必填参数
	    //appid已填,商户无需重复填写
	    //mch_id已填,商户无需重复填写
	    //noncestr已填,商户无需重复填写
	    //spbill_create_ip已填,商户无需重复填写
	    //sign已填,商户无需重复填写
	    $unifiedOrder->setParameter("openid", "$openid");//商品描述
	    $unifiedOrder->setParameter("body", $card['cardTypeInfo']['name'].'-'.$card['number']);//商品描述
	    $unifiedOrder->setParameter("out_trade_no",date("YmdHis",time()).'cx'.$cardId.'x'.$paymentAccountId.'xr');//商户订单号
	    $unifiedOrder->setParameter("total_fee", $money);//总金额
	    $unifiedOrder->setParameter("notify_url",HOME_URL.$this->WX_NOTIFY_URL);//通知地址
	    $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
	    //非必填参数，商户可根据实际情况选填
	    //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
	    //$unifiedOrder->setParameter("device_info","XXXX");//设备号
	    //$unifiedOrder->setParameter("attach","XXXX");//附加数据
	    //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
	    //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
	    //$unifiedOrder->setParameter("goods_tag","CARD");//商品标记
	    //$unifiedOrder->setParameter("openid","XXXX");//用户标识
	    //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
	    //非必填参数，商户可根据实际情况选填
	    $prepay_id = $unifiedOrder->getPrepayId($paymentAccount['wx_appid'], $paymentAccount['mchid'], $paymentAccount['mch_key']);
	    //=========步骤2：使用jsapi调起支付============
	    $jsApi->setPrepayId($prepay_id);
	    
	    $enviroment = isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : '';
	    $wxUrl = $enviroment == 'online' ? WX_ONLINE_URL : WX_TEST_URL;
	    
	    $data = array();
	    $data['wxpay_data'] = $jsApi->getParameters($paymentAccount['wx_appid'], $paymentAccount['mch_key']);
	    $data['money'] = $money;
	    $data['card'] = $card;
	    $data['wx_url'] = $wxUrl;
	    $data['paymentAccountAli'] = $paymentAccountAli;
	    return $this->display('wxpayment/card_topay', $data);
	}
	
	/**
	 * 会员卡购买JS支付
	 * 
	 * @param int card_id 必传
	 * @param int payment_account_id 必传
	 * @param int money 必传
	 * @param string open_id 必传
	 * @param int stadium_id 必传
	 * 
	 * @author spring
	 * @return json
	 */
	public function actionCardBuyWebPay() {
	    $cardId = isset($_REQUEST['card_id']) ? $_REQUEST['card_id'] : 0;
	    $paymentAccountId = isset($_REQUEST['payment_account_id']) ? $_REQUEST['payment_account_id'] : 0;
	    $money = isset($_REQUEST['money']) ? $_REQUEST['money'] : 0;
	    $cardTypeSellRuleId = isset($_GET['card_type_sell_rule_id']) ? $_GET['card_type_sell_rule_id'] : 0;
	    $openid = isset($_REQUEST['open_id']) ? $_REQUEST['open_id'] : '';
	    $stadiumId = isset($_REQUEST['stadium_id']) ? $_REQUEST['stadium_id'] : 0;
	    $uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : 0;
	    
	    if($cardId <= 0) {
	        return $this->errorPay('会员卡ID错误');
	    }
	    if($paymentAccountId <= 0) {
	        return $this->errorPay('支付账户ID错误');
	    }
	    if($money <= 0) {
	        return $this->errorPay('充值金额必须大于零');
	    }
	    if($openid == '') {
	        return $this->errorPay('OPENID错误');
	    }
        if($stadiumId <= 0) {
	        return $this->errorPay('场馆ID错误');
	    }
		if($uid <= 0) {
	        return $this->errorPay('用户ID错误');
	    }
	    
	    //获取会员卡信息
	    $card = Remote::instance()->get(CARD_DOMIAN, 'card/detail', array('card_id' => $cardId));
	    if($card['code'] != 200) {
	        return $this->errorPay($card['msg']);
	    }
	    
	    $card = $card['data']['card'];
	    $paymentAccount = WebApi_Wx_Account::instance()->row('*', $paymentAccountId);
	    if(empty($paymentAccount)) {
	        return $this->errorPay('支付账户信息错误');
	    }
	    if($paymentAccount['status'] != 1) {
	        return $this->errorPay('支付账户未启用');
	    }
	    
	    //获取ali支付账户信息
	    $paymentAccountAli = $this->getAliPaymentAccount($stadiumId);
	    
	    require_once(FW_PATH."/plugins/wxpay/WxPayPubHelper.php");
	    //使用jsapi接口
	    $jsApi = new JsApi_pub();
	    //=========步骤：使用统一支付接口，获取prepay_id============
	    //使用统一支付接口
	    $unifiedOrder = new UnifiedOrder_pub();
	    //设置统一支付接口参数
	    //设置必填参数
	    //appid已填,商户无需重复填写
	    //mch_id已填,商户无需重复填写
	    //noncestr已填,商户无需重复填写
	    //spbill_create_ip已填,商户无需重复填写
	    //sign已填,商户无需重复填写
	    $unifiedOrder->setParameter("openid", "$openid");//商品描述
	    $unifiedOrder->setParameter("body", $card['cardTypeInfo']['name'].'-'.$card['number']);//商品描述
	    $unifiedOrder->setParameter("out_trade_no",date("YmdHis",time()).'cx'.$cardId.'x'.$paymentAccountId.'xbx'.$uid);//商户订单号
	    $unifiedOrder->setParameter("total_fee", $money);//总金额
	    $unifiedOrder->setParameter("notify_url",HOME_URL.$this->WX_NOTIFY_URL);//通知地址
	    $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
	    //非必填参数，商户可根据实际情况选填
	    //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
	    //$unifiedOrder->setParameter("device_info","XXXX");//设备号
	    //$unifiedOrder->setParameter("attach","XXXX");//附加数据
	    //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
	    //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
	    //$unifiedOrder->setParameter("goods_tag","CARD");//商品标记
	    //$unifiedOrder->setParameter("openid","XXXX");//用户标识
	    //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
	    //非必填参数，商户可根据实际情况选填
	    $prepay_id = $unifiedOrder->getPrepayId($paymentAccount['wx_appid'], $paymentAccount['mchid'], $paymentAccount['mch_key']);
	    //=========步骤2：使用jsapi调起支付============
	    $jsApi->setPrepayId($prepay_id);
	    
	    $enviroment = isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : '';
	    $wxUrl = $enviroment == 'online' ? WX_ONLINE_URL : WX_TEST_URL;
	    
	    $data = array();
	    $data['wxpay_data'] = $jsApi->getParameters($paymentAccount['wx_appid'], $paymentAccount['mch_key']);
	    $data['money'] = $money;
	    $data['card'] = $card;
	    $data['card_type_sell_rule_id'] = $cardTypeSellRuleId;
	    $data['wx_url'] = $wxUrl;
	    $data['uid'] = $uid;
	    $data['paymentAccountAli'] = $paymentAccountAli;
	    return $this->display('wxpayment/buy_card_topay', $data);
	}
	
	/**
	 * 获取Ali账户信息
	 *
	 * @param int $stadiumId 必传
	 *
	 * @author spring
	 * @return array
	 */
	public function getAliPaymentAccount($stadiumId) {
	    $aliAcountMappings = WebApi_Mapping::instance()->getMappingByParams(array('stadium_id' => $stadiumId, 'type' => array(2)));
	    $paymentAccounts = array();
	    if(!empty($aliAcountMappings)) {
	        $paymentAccountIds = array();
	        foreach($aliAcountMappings as $value) {
	            $paymentAccountIds[] = $value['payment_account_id'];
	        }
	    
	        $paymentAccounts = WebApi_Ali_Account::instance()->getAliAccountsByParams(array('status' => 1, 'payment_account_ids' => $paymentAccountIds, 'pay_type' => 1), 1, -1, 'Array');
	    }
	    
	    if(empty($paymentAccounts)) {
	        $paymentAccounts = WebApi_Ali_Account::instance()->getAliAccountsByParams(array('status' => 1, 'channel' => 1, 'pay_type' => 1), 1, -1, 'Array');
            if(empty($paymentAccounts)) {
                return $this->errorPay('获取不到ali支付账户');
            }
	    }
	    
	    return reset($paymentAccounts);
	}
	
	/**
	 * 订单退款
	 *
	 * @param int oid 必传
	 * @param int payment_account_id 必传
	 *
	 * @author spring
	 * @return json
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
	    
	    //获取账户信息
	    $paymentAccount = WebApi_Wx_Account::instance()->row('*', $paymentAccountId);
	    if(empty($paymentAccount)) {
	        return $this->error('支付账户信息错误');
	    }
	    if($paymentAccount['status'] != 1) {
	        return $this->error('支付账户未启用');
	    }
	    
	    //获取订单信息
	    $order = Remote::instance()->get(ORDER_DOMIAN, 'order/detail', array('oid' => $oid));
	    if($order['code'] != 200) {
	        return $this->error($order['msg']);
	    }
	    $order = $order['data']['order'];
	    
	    //获取订单支付信息
	    $orderPayments = WebApi_Wx_Order_Payment::instance()->getOrderPaymentsByParams(array('oid' => $oid), 1, -1);
	    if(empty($orderPayments)) {
	        return $this->error('订单支付信息错误');
	    }
	    $orderPayment = current($orderPayments);
	    
		//微信退款
		require_once(FW_PATH."/plugins/wxpay/WxPayPubHelper.php");
		$refund = new Refund_pub();
		$refund->parameters["out_trade_no"] = $orderPayment['out_trade_no'];
		$refund->parameters["out_refund_no"] = $paymentAccount['mchid'].date("YmdHis");
		$refund->parameters["transaction_id"] = $orderPayment['trade_no'];
		$refund->parameters["total_fee"] = $order['pay_money'];
		$refund->parameters["refund_fee"] = $order['pay_money'];
		$refund->parameters["op_user_id"] = $order['uid'];
		$res = $refund->getResult($paymentAccount['wx_appid'], $paymentAccount['mchid'], $paymentAccount['mch_key'], $paymentAccount['sslcert_path'], $paymentAccount['sslkey_path']);
		if($res == false || !isset($res['return_code']) || $res['return_code'] != 'SUCCESS' || !isset($res['result_code']) || $res['result_code'] != 'SUCCESS') {
		    return $this->error('微信退款失败');
		}
		
		$msg = Msgtpl::msg('UNSUBSCRIB_SUCCESS', $order);
		
	    //添加订单退款信息
	    $orderRefund = array();
	    $orderRefund['payment_account_id'] = $paymentAccountId;
	    $orderRefund['uid'] = $order['uid'];
	    $orderRefund['oid'] = $oid;
	    $orderRefund['stadium_id'] = $order['stadium_id'];
	    $orderRefund['venue_id'] = $order['venue_id'];
	    $orderRefund['refund_money'] = $order['pay_money'];
	    $orderRefund['out_trade_no'] = $orderPayment['out_trade_no'];
	    $orderRefund['trade_no'] = $orderPayment['trade_no'];
	    $orderRefund['pay_type'] = $orderPayment['pay_type'];
	    $orderRefund['channel'] = $orderPayment['channel'];
		$refundRes = WebApi_Wx_Order_Refund::instance()->add($orderRefund);
		if($refundRes == false) {
		    return $this->error('添加订单退款信息失败');
		}
		
		return $this->output(array('退订成功'));
	}
	/**
	 * 赛事支付
	 * 
	 */
	public function actionRegistration() {
	    $registrationId = isset($_REQUEST['registration_id']) ? $_REQUEST['registration_id'] : 0;
	    $openid = isset($_REQUEST['open_id']) ? $_REQUEST['open_id'] : '';
	    $paymentAccountId = isset($_REQUEST['payment_account_id']) ? $_REQUEST['payment_account_id'] : 0;
	     
	    if($registrationId <= 0) {
	        return $this->errorPay('报名ID错误');
	    }
	    if($openid == '') {
	        return $this->errorPay('OPENID错误');
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
	        return $this->error('订单状态错误');
	    }
	    
	    $contest = Remote::instance()->get(MATCH_DOMIAN, 'contest/detail', array('contest_id' => $registration['contest_id']));
	    if($contest['code'] != 200) {
	        return $this->errorPay($contest['msg']);
	    }
	    $contest = $contest['data']['contest'];
	    if($contest['status'] != 1) {
	        return $this->error('订单状态错误');
	    }
	    
	    //获取wx平台js支付账户信息
	    $paymentAccount = WebApi_Wx_Account::instance()->row('*', $paymentAccountId);
	    if(empty($paymentAccount) || $paymentAccount['status'] != 1 || $paymentAccount['channel'] != 1 || $paymentAccount['pay_type'] != 1) {
	        return $this->errorPay('WX平台JS支付账户信息错误');
	    }
	     
	    //获取ali平台支付账户信息
	    $paymentAccounts = WebApi_Ali_Account::instance()->getAliAccountsByParams(array('status' => 1, 'channel' => 1, 'pay_type' => 1), 1, -1, 'Array');
	    if(empty($paymentAccounts)) {
	        return $this->errorPay('获取不到ali平台支付账户');
	    }
	    $paymentAccountAli = reset($paymentAccounts);
	    
	    require_once(FW_PATH."/plugins/wxpay/WxPayPubHelper.php");
	    //使用jsapi接口
	    $jsApi = new JsApi_pub();
	    //=========步骤：使用统一支付接口，获取prepay_id============
	    //使用统一支付接口
	    $unifiedOrder = new UnifiedOrder_pub();
	    //设置统一支付接口参数
	    //设置必填参数
	    //appid已填,商户无需重复填写
	    //mch_id已填,商户无需重复填写
	    //noncestr已填,商户无需重复填写
	    //spbill_create_ip已填,商户无需重复填写
	    //sign已填,商户无需重复填写
	    $unifiedOrder->setParameter("openid", "$openid");//商品描述
	    $unifiedOrder->setParameter("body", $contest['name']);//商品描述
	    $unifiedOrder->setParameter("out_trade_no",date("YmdHis",time()).'mx'.$registrationId.'x'.$paymentAccountId);//商户订单号
	    $unifiedOrder->setParameter("total_fee", intval($contest['money']));//售卖价格
	    file_put_contents('total_fee.txt', var_export(intval($contest['money']), TRUE));
	    $unifiedOrder->setParameter("notify_url",HOME_URL.$this->WX_NOTIFY_URL);//通知地址
	    $unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
	    //非必填参数，商户可根据实际情况选填
	    //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
	    //$unifiedOrder->setParameter("device_info","XXXX");//设备号
	    //$unifiedOrder->setParameter("attach","XXXX");//附加数据
	    //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
	    //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
	    //$unifiedOrder->setParameter("goods_tag","ORDER");//商品标记
	    //$unifiedOrder->setParameter("openid","XXXX");//用户标识
	    //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
	    $prepay_id = $unifiedOrder->getPrepayId($paymentAccount['wx_appid'], $paymentAccount['mchid'], $paymentAccount['mch_key']);
	    //=========步骤2：使用jsapi调起支付============
	    $jsApi->setPrepayId($prepay_id);
	    
	    $enviroment = isset($_SERVER['RUNTIME_ENVIROMENT']) ? $_SERVER['RUNTIME_ENVIROMENT'] : '';
	    $wxUrl = $enviroment == 'online' ? WX_ONLINE_URL : WX_MATCH_URL;
	    
	    $data = array();
	    $data['wxpay_data'] = $jsApi->getParameters($paymentAccount['wx_appid'], $paymentAccount['mch_key']);
	    $data['contest'] = $contest;
	    $data['registration'] = $registration;
	    $data['wx_url'] = $wxUrl;
	    $data['paymentAccountAli'] = $paymentAccountAli;
	    return $this->display('wxpayment/contest_topay', $data);
	}
}
?>