<?php exit?><!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>欢迎来到{$sitename}，登录或注册</title>
<meta name="keywords" content="{$keywords}">
<meta name="description" content="{$description}">
<!--{include file=static.php}-->
</head>
<script type="text/javascript" src="{$path}theme/script/register.js"></script>

<body class="sign-body">

	<div class="sign-cover">

		<div class="sign-bg"></div>
		
		<h1 class="logo"><a href="{$path}"><img src="{$path}theme/style/log-big.png" alt="{$sitename}" title="{$sitename}" /></a></h1>

	</div>

	<div class="sign-form">

		<div class="arrow"></div>

		<div class="tab">
			<div id="login-tab" class="nav">登录</div>
			<div id="reg-tab" class="nav">注册</div>
		</div>

		<div id="tip-warning"><i class="icon icon-info fa"></i><span id="tip-text"></span></div>

		<div id="login">

			<div class="form-group">
				<div class="form-row">
					<input type="email" value="{if $template.cookies.user_email}{$template.cookies.user_email|escape:html}{/if}" {if !$template.cookies.user_email} autofocus="true" {/if} id="form-email" class="input" placeholder="用户名/Email"/>
					<input type="password" value="" {if $template.cookies.user_email} autofocus="true" {/if}  id="form-password" class="input ipt-t" placeholder="密码"/>
				</div>
			</div>

			<button id="login-btn" class="button">普通登录</button>
			<a href="{$path}?app=user&action=foget" class="btn-box pass-word">忘记密码？</a>

			<div class="other"><div class="or">使用快捷登录</div></div>

			<a href="{$path}?app=user&action=qq" class="btn-box btn-qq-ins">腾讯帐号登录</a>
			{if $config.weibo}
			<a href="{$path}?app=user&action=weibo" class="btn-box sina-weibo">微博帐号登录</a>
			{/if}
			{if $config.ins}
			<a href="{$path}?app=user&action=instagram" class="btn-box btn-qq-ins">Instagram帐号登录</a>
			{/if}

		</div>

		<div id="reg">

			<div class="form-group">

				<div class="form-row">

					<input type="email" id="form-email-reg" class="input" placeholder="Email"/>

					<input type="password" id="form-password-reg" class="input ipt-t" placeholder="密码"/>

					<input type="password" id="form-password-com" class="input ipt-t" placeholder="确认密码"/>

					<input type="text" id="form-nickname" class="input ipt-t" placeholder="昵称"/>

				</div>

			</div>

			<button id="reg-btn" class="button">注册</button>

		</div>

	</div>

</body>
</html>