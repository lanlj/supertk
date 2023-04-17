<?php exit?><!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>通知中心 - {$sitename}</title>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><!--IE使用本身版本渲染 -->
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0" />
<meta name="renderer" content="webkit|ie-comp|ie-stand"><!--第三方webkit浏览器优先使用webkit -->
<meta name="apple-mobile-web-app-capable" content="yes"><!--全屏模式运行 -->
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="Web App"> <!-- WEBAPP标题 -->
<meta name="format-detection" content="telephone=no">
<link rel="stylesheet" type="text/css" href="{$path}theme/style/style.css" media="all"/>
<!--[if lt IE 9]>
<script src="{$path}theme/script/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="{$path}theme/script/jquery.js"></script>
<script type="text/javascript">
	var Path='{$path}';
</script>
<script type="text/javascript" src="{$path}theme/script/base.js"></script>
</head>
<body id="bind" data-menu="notice">
<!--{include file=header.php}-->

<section class="container offset-top">

	<aside class="sl">

	<!--{include file=sideLeft.php}-->

	</aside>

	<article class="sm">

	<h2 class="h2-title">通知中心 <i class="icon icon-xiangxia"></i></h2>


	{if $notification}

	<ul>

	{foreach from=$notification item=notice}

		<li id="n_{$notice.id}" class="tweet">

			<div class="tweet-inner">

				<span class="tweet-time">{$notice.lastdate}</span>

				<a class="tweet-user" href="{$path}index.php?app=tweet&action=user&id={$notice.user_id}">

					<img class="tweet-avatar" src="{if $notice.user_avatar!=''}{$notice.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}"/>

					<div class="tweet-name">{$notice.user_name}</div>

				</a>

			</div>

			<div id="tweet-{$notice.tid}" class="tweet-inner tweet-text clearleft">

			{if $notice.flag==0}

				<p>提到了你： {$notice.tweet_content}{if $notice.tweet_file!=''}[图片]{/if} <a href="{$path}index.php?app=tweet&action=view&id={$notice.tid}">查看详细</a></p>

			{else}

				<p>{$notice.content|truncate:300}</p>

				<h3 class="post-title">

				{if $notice.flag==1}

					<p class="post-desc">

						<span class="post-arrow"></span>

						<strong>评论了我的动态:</strong>
							{$notice.tweet_content|truncate:60|strip_tags}
							{if $notice.tweet_file!=''}[图片]{/if}
							<a href="{$path}index.php?app=tweet&action=view&id={$notice.tid}#reply-{$notice.rid}">查看详细</a>

					</p>

				{elseif $notice.flag==2}

					<p class="post-desc">

						<span class="post-arrow"></span>

						<strong>回复了我的评论：</strong>

						{if $notice.reply_content}

							{$notice.reply_content}
							<a href="{$path}index.php?app=tweet&action=view&id={$notice.tid}#reply-{$notice.prid}">查看详细</a>

						{else}

							原文评论已被删除

						{/if}

					</p>

				{/if}

				</h3>

			{/if}

			</div>
			<div class="tweet-data">

				{if $notice.flag==0}
				<div class="data-box data-box-line data-box-reply" data-id="{$notice.tid}" data-name="{$notice.user_name}" data-child="0" data-loc="0" data-nid="{$notice.id}"><i class="icon icon-comment"></i>评论</div>
				{elseif $notice.flag==1}
				<div class="data-box data-box-line data-box-reply" data-id="{$notice.tid}" data-name="{$notice.user_name}" data-child="{$notice.rid}" data-loc="0" data-nid="{$notice.id}"><i class="icon icon-comment"></i>回复</div>
				{elseif $notice.flag==2&&$notice.prid!=0}
				<div class="data-box data-box-line data-box-reply" data-id="{$notice.tid}" data-name="{$notice.user_name}" data-child="{$notice.prid}" data-loc="0" data-nid="{$notice.id}"><i class="icon icon-comment"></i>回复</div>
				{/if}

				{if $notice.atuid==$template.session.user_id||$isAdmin}
				<div class="data-box" onClick="NoticeDel({$notice.id})"><i class="icon icon-delete"></i>删除</div>
				{/if}

			</div>
			<div class="reply-hook">
			
				<ul class="reply-list">
				</ul>
			</div>
		</li>

	{/foreach}

	</ul>

	{$pager}

	{else}

		{if $template.get.isread==1}

			<div class="no-content">没有未读提醒，<a href="{$path}?app=tweet&action=notice&do=get&isread=0">查看已读提醒</a></div>

		{else $template.get.isread==0}

			<div class="no-content">没有提醒</div>

		{/if}
	{/if}
	</article>
</section>

<!--{include file=footer.php}-->

</body>
</html>