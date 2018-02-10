$(document).ready(function(){
	//第一张会员卡
	var cardId = $('#card_id').val();
	getCard(cardId);
	//点击其他会员卡
	$(".cz-con li").click(function(){
		var cardId = $(this).attr("value");
		$(".cz-con li").removeClass("card-selected");
		$(this).addClass("card-selected");
		getCard(cardId);
	});
   
	$(".tishi-close").click(function(){
		$(".bg").hide();		
	});
	
	$(".zcgz").click(function(){
		$(".bg").show();		
	});
});


//获取会员卡的赠送金额和升级卡种信息
function getUpgradeCardTypeRule() {
	var money = $('#money').val();
	var cardId = $('#card_id').val();
	var re = /^[1-9][0-9]*$/;
	if (!re.test(money)){
		$('#money').val('');
		$('#gbalance').text($('#balance').text());
		return false;
	}
	
	$.get('/card/recharge/ajaxUpgradeCardTypeRule', {card_id:cardId,money:money}, function(jdata) {
		var data = eval("("+jdata+")");
		if(data.status == 200) {
			$("#allgivemoney").val(data.data.give_money);
			$("#money_after").val(data.data.money_after);
			$("#gmoney").text('赠￥'+data.data.give_money+'元');
			$("#gcard").text(data.data.upgrade_type_name);
			$("#card_type_id").val(data.data.card_type_id);
			$("#gbalance").text('￥'+data.data.money_after+'元');
		} else {
			xalert(data.data);
		}
	});
}

//获取会员卡信息
function getCard(cardId) {
	$.get('/card/recharge/ajaxDetail', {card_id:cardId}, function(jdata) {
		var data = eval("("+jdata+")");
		if(data.status != 200) {
			xalert(data.data);
			return false;
		}
	 
		$("#cardId").text(data.data.card.card_id);
		$("#card_id").val(data.data.card.card_id);
		$("#cardtypeName").text(data.data.card.cardTypeInfo.name);
		$("#number").text(data.data.card.number);
		$("#mobile").text(data.data.card.mobile);
		$("#balance").text('￥'+data.data.card.balance/100+'元');
		$("#minMoney").text('最低充值'+data.data.card.cardTypeInfo.min_recharge_money/100+'元');
		$("#gcard").text(data.data.card.cardTypeInfo.name);
		$("#gbalance").text('￥'+data.data.card.balance/100+'元');
		$('#min_recharge_money').val(data.data.card.cardTypeInfo.min_recharge_money);
		$('#money').val('');
		$('#gmoney').text('赠￥0元');
		
		$('#status').val(data.data.card.status);
		
		//充值规则
		var recharge = data.data.cardtypeRechargeRules;
		var html = '';
		$.each(recharge, function(i, n) {  
			var rechargeMoney = recharge[i].recharge_money/100;  
			var giveMoney = recharge[i].give_money/100;  
			html += '<p>满'+rechargeMoney+'元送'+giveMoney+'元</p>';
		}); 
		$("#rechargeRule").html(html);
		//升级规则
		var html = '';
		var upgrades = data.data.upgrades;
		$.each(upgrades, function(i, n) {  
			 var upgradeMoney = upgrades[i].upgrade_money/100;  
			 var cardTypeName = upgrades[i].card_type_name;  
			 html += '<p>单次充'+upgradeMoney+'元升级为'+cardTypeName+'</p>';
		}); 
		$("#upgradeRule").html(html);
	});
}



