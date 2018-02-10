;(function ($) {
    $.fn.extend({
        tabEve: function(options) {
            var defaluts = {
                selectors:{
                    active:".ui-active", 
                },
                index:0,
                setData:""
            };
            var opts = $.extend({}, defaluts, options);
            var obj = $(this); 
            obj.on("click",opts.setData);
            obj.on("click",function(){
                var cls = opts.selectors.active.split(".");
                var ix = obj.index($(this));
                var typing = opts.typing;
                //选项卡、单选按钮
                if(typing=="tab"){
                    $(this).addClass(cls[1]).siblings().removeClass(cls[1]);
                    $(opts.selectors.tabcont).eq(ix).show().siblings(opts.selectors.tabcont).hide();
                }
                //下拉菜单
                else if(typing=="slide"){
                    if($(opts.selectors.tabcont).is(":hidden")){
                        $(opts.selectors.tabcont).slideDown();
                    }
                    else{
                        $(opts.selectors.tabcont).slideUp();
                    }
                }
                //排序
                else if(typing=="checkbox"){
                    if($(this).hasClass(cls[1])){
                        $(this).removeClass(cls[1])
                    }
                    else{
                        $(this).addClass(cls[1])
                    }
                }
            })
        },
        //输入框清除
        inputClear:(function(){
            $("input").keyup(function(){
                var str = $(this).val();
                if(str.trim()!=""){
                    $(this).siblings(".ui-icon-clear").show();
                }
                else{
                    $(this).siblings(".ui-icon-clear").hide();
                }
            })
            $(".ui-icon-clear").on("click",function(){
                $(this).siblings("input").val("");
                $(this).siblings("input").focus();
                $(this).hide();
            })
        })()
    });

})(jQuery);