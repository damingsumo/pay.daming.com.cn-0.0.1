{include file="common/header.tpl" pageTitle='失败提示'}
<!--主体内容-->
<section class="ui-container">
	<div class="ui-result ui-result-fail">
		<div class="ui-icon-result"></div>
		<h3 class="ui-font20">{$msg}</h3>
	</div>
</section>
<div class="ui-bottom">
	<div class="ui-fixed ui-fixed-bottom">
		<div class="ui-boxhor ui-font17 ui-bottom-btn">
			{if isset($backUrl)}
				<a href="{$backUrl}" class="ui-flex ui-btn-2">返回</a>
			{else}
				<a href="javascript:history.go(-1);" class="ui-flex ui-btn-2">返回</a>
			{/if}
		</div>
	</div>
</div>
</body>
</html>