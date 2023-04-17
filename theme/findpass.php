<?php exit?><!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>重置密码 - {$sitename}</title>
<meta name="keywords" content="{$keywords}">
<meta name="description" content="{$description}">
<!--{include file=static.php}-->
</head>
<script type="text/javascript" src="{$path}theme/script/foget.js"></script>
<body class="sign-body">

	<div class="sign-cover">

		<div class="sign-bg"></div>
		
		<h1 class="logo"><a href="{$path}"><img src="{$path}theme/style/log-big.png" alt="{$sitename}" title="{$sitename}" /></a></h1>

	</div>

	<div class="sign-form">
				<div id="tip-warning"><i class="icon icon-info fa"></i><span id="tip-text"></span></div>

		<div id="login">
			<div class="form-group">
				<div class="form-row">
					<input type="password" id="find-key" style="display: none;" value="{$pkey}"/>
					<input type="password" id="find-pass" class="input" placeholder="新密码"/>
					<input type="password" id="find-passagan" class="input ipt-t" placeholder="重复一次"/>
				</div>
			</div>
			<button id="find-bth" class="button">确定</button>
		</div>
	</div>
</body>
</html>