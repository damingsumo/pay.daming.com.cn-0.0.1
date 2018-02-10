
<div class="ui-font15 ui-layer" style="display:none;" id="tishi">
	<div class="ui-boxver ui-layer-cont">
		<div class="ui-layer-word"><p id="msg"></p></div>
	</div>
</div>


{literal}
<script type="text/javascript">
//类似alert
function xalert(str, time){
	$('#msg').text(str);
	$('#tishi').show();
	if(parseInt(time)>0) {
		time = time;
	} else {
		time = 3;
	}
	
	setInterval("$('#tishi').hide()",time*1000);
}

</script>
{/literal}

</body>
</html>