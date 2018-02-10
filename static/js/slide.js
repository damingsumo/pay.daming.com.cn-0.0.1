;(function($){
	var stopWindowDefault = {
		windowdefaultEvent: function(windowflag){
			if(windowflag){
				$("body").removeAttr("style");
			}
			else{
				$("body").css("overflow","hidden");
			}
			window.ontouchmove = function(e){
				e.preventDefault && e.preventDefault();
				e.stopPropagation && e.stopPropagation();
				if(windowflag){
		            e.returnValue=true;
		            return true;	
				}
				else{
			        e.returnValue=false;
			        return false;
				}
			}
		}
	};
	$.fn.extend({
		//日期时间控件
		dateControl:function(options){
			document.onselectstart=function (){return false;};
			var defaults = {
				selectors:{
					slide:".ui-slide",
					slidelist:".ui-slide-list",
					scroll:".ui-slide-scroll",
					txt:".ui-stadus-txt",
					sure:".ui-sure",
					cancle:".ui-cancle",
					active:"ui-slide-active"
				},
				index:2,
				setData:"",
				endFunc:""
			};
			var ops = $.extend({},defaults,options);

			//return this.each(function () {	
			var obj = $(this);
			var id = $(this).attr("id");
			var sy,my,ey,st,ix,sh,ah,len,objh,olen;
			var flag = false;
			var touchEvents = {
			    touchstart: "touchstart",
			    touchmove: "touchmove",
			    touchend: "touchend",
			    initTouchEvents: function () {
					var browser={
						versions:function(){
							var u = navigator.userAgent, app = navigator.appVersion;
							return {//移动终端浏览器版本信息
								trident: u.indexOf('Trident') > -1, //IE内核
								presto: u.indexOf('Presto') > -1, //opera内核
								webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
								gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核
								mobile: !!u.match(/AppleWebKit.*Mobile.*/)||u.indexOf('iPad') > -1, //是否为移动终端
								ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
								android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, //android终端或者uc浏览器
								iPhone: u.indexOf('iPhone') > -1, //是否为iPhone或者QQHD浏览器
								iPad: u.indexOf('iPad') > -1, //是否iPad
								webApp: u.indexOf('Safari') == -1 //是否web应该程序，没有头部与底部
							};
						}(),
						language:(navigator.browserLanguage || navigator.language).toLowerCase()
					}
					if(!browser.versions.mobile){
			            this.touchstart = "mousedown";
			            this.touchmove = "mousemove";
			            this.touchend = "mouseup";
					}
			    },
			};
			var scrollEvents = {
				sy:0,
				my:0,
				ey:0,
				st:0,
				ix:0,
				sh:0,
				ah:$(ops.selectors.scroll).children().eq(0).height(),
				len:0,
				objh:0,
				olen:0,
				_self:"",
				_slide:function(e){
					e.stopPropagation();
					scrollEvents._self = $(this);
					if($(ops.selectors.slide).is(":hidden")){
						$(ops.selectors.slide).show();
						$(ops.selectors.slidelist).slideDown("fast");
						stopWindowDefault.windowdefaultEvent(false);
					}
					else{
						$(ops.selectors.slidelist).slideUp("fast",function(){
							$(ops.selectors.slide).hide();
							stopWindowDefault.windowdefaultEvent(true);
						});
					}
					$(ops.selectors.scroll).each(function(){
						$(this).children().eq(ops.index).addClass(ops.selectors.active).siblings().removeClass(ops.selectors.active);
						$(this).css("top",scrollEvents.ah*2-ops.index*scrollEvents.ah);
					})
					var txt = scrollEvents._self.text();
					var str = txt.split("-");
					$(ops.selectors.scroll).each(function(i){
						$(this).children().each(function(j){
							if(str[i]==$(this).text()){
								$(this).addClass(ops.selectors.active).siblings().removeClass(ops.selectors.active)
								$(this).parent().css("top",scrollEvents.ah*2-j*scrollEvents.ah);
							}
						})
					})
					return scrollEvents._self;
				},
				_slidedown:function(e){
					if(e.target == this)
						$(ops.selectors.slidelist).slideUp("fast",function(){
							$(ops.selectors.slide).hide();
							stopWindowDefault.windowdefaultEvent(true);
						});
				},
				_start:function(e){
					flag = true;
					scrollEvents.sy = e.pageY || e.originalEvent.targetTouches[0].pageY;
					scrollEvents.st = parseInt($(this).css("top"));
					scrollEvents.ah = $(this).children().eq(0).height();
					scrollEvents.len = $(this).children().length-1;
					scrollEvents.objh = $(this).height();
					return sy,st,ah,len,objh;
				},
			    _move: function(e){
					e.stopPropagation;
					if(flag){
						scrollEvents.my = e.pageY || e.originalEvent.targetTouches[0].pageY;
						scrollEvents.ey = scrollEvents.my-scrollEvents.sy;
						if(scrollEvents.ey+scrollEvents.st>=scrollEvents.ah*2){
							scrollEvents.ix = 0;
						}
						else{
							scrollEvents.ix = Math.floor(Math.abs((scrollEvents.ah*2-scrollEvents.ey-scrollEvents.st+scrollEvents.ah/2)/scrollEvents.ah));
						}
						$(this).children().eq(scrollEvents.ix).addClass(ops.selectors.active).siblings().removeClass(ops.selectors.active);
						$(this).css("top",scrollEvents.ey+scrollEvents.st);
						return my,ey,ix;
					}
			    },
			    _end: function(){
					flag = false;
					if(scrollEvents.my){
						scrollEvents.st = parseFloat($(this).css("top"));
						if(scrollEvents.st>=scrollEvents.ah*2){
							scrollEvents.st = scrollEvents.ah*2;
						}
						else if(scrollEvents.st<=scrollEvents.ah*2-scrollEvents.len*scrollEvents.ah){
							scrollEvents.ix = scrollEvents.len;
							scrollEvents.st = scrollEvents.ah*2-scrollEvents.len*scrollEvents.ah;
						}
						else{
							scrollEvents.st = scrollEvents.ah*2-scrollEvents.ix*scrollEvents.ah;
						}
						$(this).attr("data-on",scrollEvents.ix);
						$(this).stop().animate({"top":scrollEvents.st},100);
						$(this).children().eq(scrollEvents.ix).addClass(ops.selectors.active).siblings().removeClass(ops.selectors.active);
						//ops.index = scrollEvents.ix;
						if($(this).attr("data-city")=="1"){
							$(this).next().css("top",2*scrollEvents.ah);
							$(this).next().next().css("top",2*scrollEvents.ah);
						}
						else if($(this).attr("data-city")=="2"){
							$(this).next().css("top",2*scrollEvents.ah);
						}
						ops.endFunc($(this));
						scrollEvents.my = null;
					}
			    },
			    _sure:function(e){
			    	scrollEvents.olen = $(ops.selectors.scroll).length;
			    	var txt = "";
			    	for(i=0;i<scrollEvents.olen;i++){
			    		if(i<scrollEvents.olen){
			    			if(i==2){
			    				txt += " - "+$(ops.selectors.scroll+":eq("+i+") ."+ops.selectors.active).text()+" ";	
			    			}
			    			else{
			    				txt += $(ops.selectors.scroll+":eq("+i+") ."+ops.selectors.active).text()+" ";
			    			}
			    		}
			    		else{
			    			txt += $(ops.selectors.scroll+":eq("+i+") ."+ops.selectors.active).text();
			    		}
			    	}
			    	
			    	var venueId = $('#venueId').val();
			    	var bookDay = $('#chooseBookDay .ui-slide-active').text();
			    	var startHour = $('#chooseStartHour .ui-slide-active').text().substring(0, 2);
			    	if(startHour.substring(0, 1) == 0) {
			    		startHour = startHour.substring(1);
			    	}
			    	startHour = parseFloat(startHour);
			    	
			    	var endHour = $('#chooseEndHour .ui-slide-active').text().substring(0, 2);
			    	if(endHour.substring(0, 1) == 0) {
			    		endHour = endHour.substring(1);
			    	}
			    	endHour = parseFloat(endHour);
			    	
			    	if(startHour >= endHour) {
			    		xalert('开始时间必须小于结束时间');
			    		return false;
			    	}
			    	
			    	if(endHour - startHour > 2) {
			    		xalert('最多预定两小时');
			    		return false;
			    	}
			    	
					var hours = '';
					for(var i = startHour; i < endHour; i++) {
						hours += i+',';
					}
					
					var type = $('#chooseType').val();
			    	$.ajax({
			    		type: "GET",
			    		url: "/oc/price/getHoursPrices",
			    		data: {venue_id:venueId, hours:hours, book_day:bookDay, type:type},
			    		dataType: 'json',
			    		success: function(data) {
			    			if(data.status == 200) {
			    				txt += '￥'+data.data.total_price/100+'元';
			    				
			    				var orderStr = '';
			    				orderStr += '<input type="hidden" name="order['+type+'][book_day]" value="'+bookDay+'"/>';
			    				orderStr += '<input type="hidden" name="order['+type+'][order_money]" value="'+data.data.total_price+'"/>';
			    				orderStr += '<input type="hidden" name="order['+type+'][type]" value="'+type+'"/>';
			    				for(var i = 0; i < data.data.items.length; i++) {
			    					orderStr += '<input type="hidden" name="order['+type+'][items]['+i+'][start_hour]" value="'+data.data.items[i]['start_hour']+'"/>';
			    					orderStr += '<input type="hidden" name="order['+type+'][items]['+i+'][end_hour]" value="'+data.data.items[i]['end_hour']+'"/>';
			    					orderStr += '<input type="hidden" name="order['+type+'][items]['+i+'][price]" value="'+data.data.items[i]['price']+'"/>';
			    					orderStr += '<input type="hidden" name="order['+type+'][items]['+i+'][price_type]" value="'+data.data.type+'"/>';
			    					orderStr += '<input type="hidden" name="order['+type+'][items]['+i+'][play_time]" value="'+bookDay+'"/>';
								}
			    				
			    				if(type == 1) {
			    					$('#first').html('');
			    					$('#first').append(orderStr);
			    					
			    					$('#primeTimeType').val(data.data.primeTimeType);
			    					var tip = $('#tip').val();
			    					successRate(data.data.primeTimeType, tip);
			    				} else if(type == 2) {
			    					$('#second').html('');
			    					$('#second').append(orderStr);
			    				} else if(type == 3) {
			    					$('#third').html('');
			    					$('#third').append(orderStr);
			    				}
			    				
			    				scrollEvents._self.find(ops.selectors.txt).text(txt);
						    	scrollEvents._self.find(ops.selectors.txt).css("color",ops.txtcolor);
						    	scrollEvents._slide(e);
			    			} else {
			    				xalert(data.data);
								return false;
							}
			    		}
			    	});
			    	
			    }
			}
			obj.on("click",ops.setData);
			obj.on("click",scrollEvents._slide);
			touchEvents.initTouchEvents();
			$(ops.selectors.slidelist).parent().on("click",scrollEvents._slidedown)
			$(ops.selectors.slidelist).on(touchEvents.touchstart,ops.selectors.scroll,scrollEvents._start);
			$(ops.selectors.slidelist).on(touchEvents.touchmove,ops.selectors.scroll,scrollEvents._move);
			$(ops.selectors.slidelist).on(touchEvents.touchend,ops.selectors.scroll,scrollEvents._end);
			$(ops.selectors.sure).on("click",scrollEvents._sure);
			$(ops.selectors.cancle).on("click",scrollEvents._slide);
		//})
		}
	});
})(jQuery);