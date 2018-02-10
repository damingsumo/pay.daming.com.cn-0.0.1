<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<!--忽略页面中的数字识别为电话号码-->  
	<meta name="format-detection" content="telephone=no" />
	<!--忽略页面中的邮箱格式为邮箱-->
	<meta name="format-detection" content="email=no"/>
	<title>{$pageTitle}</title>
	<link rel="stylesheet" type="text/css" href="{staticurl  action="style.css" type="css"}" />
	<script type="text/javascript" language="javascript" src="{staticurl action='jquery-2.0.3.min.js' type='js'}"></script>
	<script type="text/javascript" language="javascript" src="{staticurl action='project.js' type='js'}"></script>
</head>
<body>
<!--头部公用-->
<header class="ui-header">
	<div class="ui-fixed ui-header-fixed">
		<div class="ui-boxhor ui-header-box">
			{if isset($backUrl)}
				<a href="{$backUrl}" class="ui-icon ui-back"></a>
			{else}
				<a href="javascript:history.go(-1);" class="ui-icon ui-back"></a>
			{/if}
			<div class="ui-flex ui-header-center">
				<p class="ui-font16">{$pageTitle}</p>
			</div>
			  <a href="javascript:void(0)" class="ui-icon  "></a> 
		</div>
	</div>
</header>