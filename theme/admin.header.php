<?php exit?><!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>{$mode} - 控制台 - {$sitename}</title>
<meta name="keywords" content="{$user.name},{$keywords}">
<meta name="description" content="{$description}">
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><!--IE使用本身版本渲染 -->
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0" />
<meta name="format-detection" content="telephone=no">
<link rel="stylesheet" type="text/css" href="{$path}theme/style/admin.css" media="all"/>
<!--[if lt IE 9]>
<script src="{$path}theme/script/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="{$path}theme/script/jquery.js"></script>
<script type="text/javascript" src="{$path}theme/script/admin.js"></script>
<script type="text/javascript">
var Path='{$path}';
function clear(){
	if(confirm('这个不建议经常清理，推荐网站访问量少时清理，确定要清理吗?')){
		window.location.href=Path+"?app=control&action=clearfile";
		return true;
	}
	return false;
}
</script>
</head>
<body>
<header>
	<nav>
		您好,<a href="{$path}index.php?app=tweet&action=user&id={$template.session.user_id}" title="{$template.session.user_name}">{$template.session.user_name}</a>
	</nav>
	<a href="{$path}" title="控制台">返回{$sitename}首页</a>
</header>
<menu>
	<ul>
		<li><a class="admin-menu" href="javascript:void(0);"><div class="admin-menu-name"><i class="icon icon-shumiao fa"></i>我的{$sitename}</div></a>
			<ul>
			<div class="arrow"></div>
				<li><a href="{$path}?app=control&action=set&do=profile">个人资料</a></li>
				<li><a href="{$path}?app=control&action=set&do=password">修改密码</a></li>
				<li><a href="{$path}?app=control&action=set&do=nickname">修改昵称</a></li>
				<li><a href="{$path}?app=control&action=set&do=avatar">上传头像</a></li>
				<li><a href="{$path}?app=control&action=set&do=userbg">上传封面</a></li>
			</ul>
		</li>
		{if $isAdmin}
		<li><a class="admin-menu" href="{$path}?app=control&action=set&do=apps"><div class="admin-menu-name"><i class="icon icon-yingyongzhongxin fa"></i>应用中心</div></a></li>
		<li><a class="admin-menu" href="{$path}?app=control&action=set&do=setting"><div class="admin-menu-name"><i class="icon icon-shezhi fa"></i>通用设置</div></a></li>
		<li><a class="admin-menu" href="{$path}?app=control&action=set&do=link"><div class="admin-menu-name"><i class="icon icon-fujian fa"></i>链接管理</div></a></li>
		<li><a class="admin-menu" href="{$path}?app=control&action=clear"><div class="admin-menu-name"><i class="icon icon-qingchu fa"></i>清理缓存</div></a></li>
		<li><a class="admin-menu" href="javascript:void(clear());"><div class="admin-menu-name"><i class="icon icon-qingchu fa"></i>清理垃圾</div></a></li>
		{if $adminmenu}
		{foreach from=$adminmenu item=adminmenu}

		{if $adminmenu.children}
		
		<li><a href="" class="admin-menu"><div class="admin-menu-name"><i class="icon {if $adminmenu.icon!=''}{$adminmenu.icon}{else}icon-chajian{/if} fa"></i>{$adminmenu.name}</div></a>
			
			<ul>
			<div class="arrow"></div>
				{foreach from=$adminmenu.children item=child}
				
				<li><a href="{$path}{$child.url}" class="admin-menu-child">{$child.title}</a></li>
				{/foreach}
			
			</ul>		
		</li>
		{/if}
		{/foreach}
		{/if}
		<li><a class="admin-menu" href="{$path}?app=control&action=set&do=service"><div class="admin-menu-name"><i class="icon icon-yingpan fa"></i>服务器信息</div></a></li>
		{/if}
	</ul>
</menu>