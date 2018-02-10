
{if !isset($type)}
{$type=''}
{/if}

<footer class="ui-footer">
	<div class="ui-fixed ui-footer-fixed">
		<div class="ui-boxhor ui-font12 ui-footer-box">
			<a href="{wxindex name='wechat_id'}" {if $type == 'index'}class="ui-flex ui-active"{else}class="ui-flex"{/if}>
				<span class="ui-icon ui-home"></span>首页
			</a>
			<a href="{url action='card/index'}" {if $type == 'card'}class="ui-flex ui-active"{else}class="ui-flex"{/if}>
				<span class="ui-icon ui-vip"></span>会员
			</a>
			<a href="{userindex name='wechat_id'}" {if $type == 'user'}class="ui-flex ui-active"{else}class="ui-flex"{/if}>
				<span class="ui-icon ui-center"></span>我的
			</a>
		</div>
	</div>
</footer>