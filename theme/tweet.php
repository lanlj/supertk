<?php exit?><!doctype html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">

{if $tweet.title}

<title>{$tweet.title} - {$sitename}</title>

{else}

<title>{$tweet.user_name}的动态:{$tweet.description}</title>

{/if}

<meta name="keywords" content="{$tweet.user_name},{$keywords}">
<meta name="description" content="{$tweet.lastdate}:{$tweet.description}">
<!--{include file=static.php}-->
<script type="text/javascript" src="{$path}theme/script/base.js"></script>
</head>
<body >

<!--{include file=header.php}-->

<section class="container offset-top">

	<h2 class="h2-title">{if $tweet.title}{$tweet.title}{else}{$tweet.description}{/if}<i class="icon icon-xiangxia"></i></h2>

	<article class="tweet-view">

		<img src="{if $tweet.user_avatar!=''}{$tweet.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}" class="user-pro"/>

		<div class="user-db">
		
		
			<p class="user-info"><a href="{$path}index.php?app=tweet&action=user&id={$tweet.user_id}">{$tweet.user_name}</a> {$tweet.ip} <span>{$tweet.lastdate}</span><span class="data-box like {if $tweet.like_status>0}liked{/if}" id="like-{$tweet.id}" data-id="{$tweet.id}">{if $tweet.like_status>0}<i class="icon icon-likefill"></i>喜欢{if $tweet.likes>0}({$tweet.likes}){/if}{else}<i class="icon icon-like"></i>喜欢{if $tweet.likes>0}({$tweet.likes}){/if}{/if}</span></p>

		</div>

		<p class="tweet-content">{$tweet.content}</p>

		{if $tweet.images}

		<div class="tweet-pic-list">

		{foreach from=$tweet.images item=images}

		<img src="{$path}upload/images/{$images}"/>

		{/foreach}

		</div>

		{/if}

		{if $tweet.zan}
		
		<div class="tweet-likes">

		{foreach from=$tweet.zan item=zan}
			
			<a href="{$path}index.php?app=tweet&action=user&id={$zan.user_id}" title="{$zan.user_name}" data-tip="{$zan.user_name}" class="liker"><img src="{if $zan.user_avatar!=''}{$zan.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}"></a>

		{/foreach}

		<span class="like_in">{if $tweet.hot>3}等{$tweet.hot}人{/if}喜欢过</span>

		</div>

		{/if}

		<ul class="tweet-comment-list">

		{if $tweet.reply}

			{foreach from=$tweet.reply item=reply}
			
			<li id="reply-{$reply.id}" class="reply-view">
				
				<a class="reply-avatar" href="{$path}index.php?app=tweet&action=user&id={$reply.user_id}"><img src="{if $reply.user_avatar}{$reply.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}"/></a>

				<div class="reply-main">
					
					<div class="reply-name"><a href="{$path}index.php?app=tweet&action=user&id={$reply.user_id}">{$reply.user_name}</a></div>

					<p>{if $reply.child !=0} 回复 <a href="{$path}index.php?app=tweet&action=user&id={$reply.reply_user_id}">{$reply.reply_user_name}</a> : {/if}{$reply.content}</p>

					<div class="reply-data">

					<span>{$reply.lastdate}</span>

					{if $reply.user_id==$template.session.user_id||$tweet.user_id==$template.session.user_id||$isAdmin}<span id="delReply-{$reply.id}" onClick="ReplyDel({$reply.id})">删除</span>{/if}

					{if $template.session.user_id}

						<a href="javascript:;" class="reply-tip data-box-reply" data-id="{$tweet.id}" data-name="{$reply.user_name}" data-child="{$reply.id}" data-loc="1" data-tip="回复"><i class="icon icon-comment"></i></a>

					{/if}

					</div>

				</div>

			</li>

			{/foreach}


		{/if}
		</ul>

	</article>

	<h2 class="h2-title">跟帖 <i class="icon icon-xiangxia"></i></h2>

	<div class="reply-box">

	{if $template.session.user_id}

		<div class="reply-push">

			<textarea onkeydown="if(event.ctrlKey&&event.keyCode==13 || event.keyCode==10){document.getElementById('tweet-reply-this').click();return false};" id="reply-this-content" placeholder="我来说一句..."></textarea>

		</div>

		<div class="pr">

			<div class="emoji"><i class="icon icon-biaoqing"></i><div class="emot-put"><div id="In_te" class="emot-in"></div><div class="arrow"></div></div></div>
			<div id="tweet-reply-this" class="btn btn-primary right" data-id="{$tweet.id}">回应</div>
			<div class="clear"></div>

		</div>

	{else}

	<a href="{$path}index.php?app=user&action=signup" class="no-login">登录后即可发表评论</a>

	{/if}

</section>

<!--{include file=footer.php}-->

</body>
</html>