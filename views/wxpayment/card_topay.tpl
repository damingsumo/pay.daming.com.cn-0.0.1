{include file="common/header.tpl" pageTitle='支付'}

<div class="ui-container">
	<ul class="ui-item">
		<li class="ui-boxhor ui-font15 ui-cont">
			<div class="ui-flex ui-item-left">
				{$card.cardTypeInfo.name}-{$card.number}费用
			</div>
			<div class="ui-item-right ui-red">￥{$money/100}</div>
		</li>
	</ul>
	<h2 class="ui-font15 ui-title">选择支付方式</h2>
	<ul class="ui-item2">
		<li class="ui-boxhor ui-font15 ui-cont ui-pay-active" onclick="choosePayType('wx');">
			<div class="ui-icon-weixin"></div>
			<div class="ui-flex ui-item-left">微信支付</div>
			<div class="ui-icon ui-icon-radio" id="wxChoose"></div>
		</li>
		<div style="margin-top: 3px;"></div>
		<li class="ui-boxhor ui-font15 ui-cont ui-pay-active" onclick="choosePayType('ali');">
			<div class="ui-icon-zfb"></div>
			<div class="ui-flex ui-item-left">支付宝</div>
			<div class="ui-icon" id="aliChoose"></div>
		</li>
	</ul>
	<div style="margin-top: 8px;"></div>
	<div class="ui-font17 ui-btn" style="display:block;" id="wxPay">
		<a onclick="checkActivity();" data="1" id="pay">确认支付<span>{$money/100}</span>元</a>
	</div>
	<div class="ui-font17 ui-btn" style="display:none;" id="aliPay">
		<a href="{url action='alipayment/cardRechargeWebPay'}?card_id={$card['card_id']}&payment_account_id={$paymentAccountAli['payment_account_id']}&money={$money}" class="J-btn-submit btn mj-submit btn-strong btn-larger btn-block">确认支付<span>{$money/100}</span>元</a>
	</div>
</div>
<script type="text/javascript" language="javascript" src="{staticurl action='ap.js' type='js'}"></script>
<script>
var btn = document.querySelector(".J-btn-submit");
btn.addEventListener("click", function (e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    //ele.href 是GET到支付宝收银台的URL
    _AP.pay(e.target.href);
    return false;
}, false);
</script>

<script>
var wx_url = '{$wx_url}';
function checkTime(i) {    
	if (i < 10) {    
		i = "0" + i;    
	}    
	return i;    
}

//选择支付方式
function choosePayType(chooseType) {
	if(chooseType == 'wx') {
		$('#wxChoose').attr('class', 'ui-icon ui-icon-radio');
		$('#aliChoose').attr('class', 'ui-icon');
		$('#wxPay').attr('style', 'display:block');
		$('#aliPay').attr('style', 'display:none');
	} else if(chooseType == 'ali') {
		$('#aliChoose').attr('class', 'ui-icon ui-icon-radio');
		$('#wxChoose').attr('class', 'ui-icon');
		$('#aliPay').attr('style', 'display:block');
		$('#wxPay').attr('style', 'display:none');
	}
}

{literal}
//判断活动状态
function checkActivity() {
	payment.wxJsPay();
}

var payment = {
		wxJsPay:function() {
			if($('#pay').attr('data') == 0) {
				xalert('该订单已超时取消,不允许支付');
				return false;
			}
			if (typeof WeixinJSBridge == "undefined"){
				if( document.addEventListener ){
					document.addEventListener('WeixinJSBridgeReady', payment.wxtopay, false);
				}else if (document.attachEvent){
					document.attachEvent('WeixinJSBridgeReady', payment.wxtopay);
					document.attachEvent('onWeixinJSBridgeReady', payment.wxtopay);
				}
			}else{
				payment.wxtopay();
			}
		},
		{/literal}
	    wxtopay:function(){
		 WeixinJSBridge.invoke(
				 'getBrandWCPayRequest',
				 {$wxpay_data},
				 function(res){
					 WeixinJSBridge.log(res.err_msg);
					 if(res.err_msg == "get_brand_wcpay_request:ok") {
						 
						 url = wx_url+"/payment/cardPaysuccess?card_id="+{$card.card_id};
						 {literal}
						 window.location.href=url;
					 } else {
					 {/literal}
						 url = wx_url+"/payment/cardPayfail?card_id="+{$card.card_id}+"&money="+{$money};
						 {literal}
						 window.location.href=url;
					 }
				 }
		 );
	 }

}
</script>
{/literal}
{include file="common/footer.tpl"}