<?php exit?><!doctype html>
<html>
<head>
<meta charset="utf-8" />
{if $page_current>1}
<title>{$sitename} - 第 {$page_current}页 - 全部动态</title>
{else}
<title>{$sitename} - {$config.sitesubname}</title>
{/if}
<meta name="keywords" content="{$keywords}">
<meta name="description" content="{$description}">
<!--{include file=static.php}-->
<script type="text/javascript" src="{$path}theme/script/base.js"></script>
{if !$isMobile}
<script type="text/javascript" src="{$path}theme/script/ad.js"></script>
{/if}
</head>
<body id="bind" data-menu="index" {if $page_current<2}data-autoload="true"{else}data-autoload="false"{/if}>
<!--{include file=header.php}-->

<section class="container offset-top">

	<aside class="sl">

	<!--{include file=sideLeft.php}-->

	{if $links}

	<div class="side">

		<h3 class="h3-title">邻居</h3>

		<ul class="link">

		{foreach from=$links item=link}

		<li><a href="{$link.url}" title="{$link.text}" target="_blank">{$link.name}</a></li>

		{/foreach}

		</ul>

		<div class="clear"></div>

	</div>

	{/if}

	</aside>

	<article class="get-data sm">

		<!--{include file=tweet.into.php}-->
		{if $isMobile}
		<!--{include file=class.php}-->
		{else}
		<h2 class="h2-title"><span class="tweet-counts">共{$tweet_counts}个话题</span>全部动态<i class="icon icon-xiangxia"></i></h2>
	    {/if}
		<ul id="tweet-list" {if $tweet} data-have="1" {else} data-have="0"{/if}>
	

		{if $tweet}

		{foreach from=$tweet item=tweet}
		
		<li id="tweet-{$tweet.id}" class="tweet" data-time="{$tweet.date}">

			<div class="tweet-inner">

				<span class="tweet-time">{$tweet.lastdate}</span>

				<a class="tweet-user" href="{$path}index.php?app=tweet&action=user&id={$tweet.user_id}">

					<img class="tweet-avatar" src="{if $tweet.user_avatar!=''}{$tweet.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}"/>
					<div class="tweet-name">{$tweet.user_name}</div>

				</a>

			</div>

			<div class="tweet-inner tweet-text clearleft">

				{if $tweet.title!=''}

				<h3 class="post-title">
				提问题说:
					<a class="a" href="{$path}index.php?app=tweet&action=view&id={$tweet.id}">{$tweet.title}</a>
				</h3>

				{else}
				
				<p>{$tweet.content}</p>

				{/if}
			</div>

			{if $tweet.title==''}

				{if $tweet.images}

				<div class="tweet-pic {if $tweet.images_count>=2}tweet-inner{/if}">

				{foreach from=$tweet.images item=image}

				{if $tweet.images_count==1}
				
				<a class="one-pic" href="{$path}upload/images/{$image.img}" rel="lightbox" data-name="{$tweet.user_name}" data-avatar="{if $tweet.user_avatar!=''}{$tweet.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}" data-title="{$tweet.description}" data-lightbox="tweet-pic-{$tweet.id}" style="background-image:url({$path}thumb.php&#63;src={$path}upload/images/{$image.img}&#38;w=550&#38;h=250&#38;zc=1&#38;q=100)">
				{if $image.ext=='gif'}<span class="img_intros"><em class="giftag"></em></span>{/if}</a>
				{else}
				<a class="mut-pic" href="{$path}upload/images/{$image.img}" rel="lightbox" data-name="{$tweet.user_name}" data-avatar="{if $tweet.user_avatar!=''}{$tweet.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}" data-title="{$tweet.description}" data-lightbox="tweet-pic-{$tweet.id}"><img src="{$path}thumb.php&#63;src={$path}upload/images/{$image.img}&#38;w=180&#38;h=180&#38;zc=1&#38;q=100"/>{if $image.ext=='gif'}<span class="img_intros"><em class="giftag"></em></span>{/if}</a>

				{/if}

				{/foreach}

				</div>

				{/if}

			{/if}

			<div class="tweet-inner tweet-oter clearleft">

				

				<span><i class="icon icon-locationfill"></i>{if $tweet.ip!=''}{$tweet.ip}{else}火星{/if}</span>

			

				{if $tweet.agent!=''}

				<span>{$tweet.agent}</span>

				{/if}

				<a href="{$path}index.php?app=tweet&action=view&id={$tweet.id}" title="{$tweet.description}" class="xs-hid">详细</a>

			</div>
				
			<div class="tweet-data {if $tweet.user_id==$template.session.user_id||$isAdmin}tweet-data-flex{/if}">

				<div class="data-box data-box-line data-box-reply" data-id="{$tweet.id}" data-name="{$tweet.user_name}" data-child="0" data-loc="0"><i class="icon icon-comment"></i>评论{if $tweet.reply_num>0}({$tweet.reply_num}){/if}{if $tweet.reply}<div class="arrow-top2"></div><div class="arrow-top"></div>{/if}</div>

				<div class="data-box like {if $tweet.like_status>0}liked{/if}" id="like-{$tweet.id}" data-id="{$tweet.id}">{if $tweet.like_status>0}<i class="icon icon-likefill"></i>喜欢{if $tweet.likes>0}({$tweet.likes}){/if}{else}<i class="icon icon-like"></i>喜欢{if $tweet.likes>0}({$tweet.likes}){/if}{/if}</div>

				{if $tweet.user_id==$template.session.user_id||$isAdmin}
				<div class="data-box data-box-line_l" onClick="TweetDel({$tweet.id})"><i class="icon icon-delete"></i>删除</div>
				{/if}

			</div>
			<div class="reply-hook">

				<ul id="reply-{$tweet.id}" class="reply-list">

					{if $tweet.zan}

					<li class="tweet-likes">

					{foreach from=$tweet.zan item=zan}
						
						<a href="{$path}index.php?app=tweet&action=user&id={$zan.user_id}" title="{$zan.user_name}" data-tip="{$zan.user_name}" class="liker"><img src="{if $zan.user_avatar!=''}{$zan.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}"></a>

					{/foreach}

					<span class="like_in">{if $tweet.hot>3}等{$tweet.hot}人{/if}喜欢过</span>

					<div class="clear"></div>

					</li>

					{/if}

					{if $tweet.reply}

						{if $tweet.reply_count}

						<li><a class="reply_more" href="{$path}index.php?app=tweet&action=view&id={$tweet.id}">阅读剩余{$tweet.reply_count}条评论</a></li>

						{/if}
						{foreach from=$tweet.reply item=reply}

						{if $reply.child !=0}
						
						<li id="reply-{$reply.id}">{if $template.session.user_id}<span class="data-box-reply" data-id="{$tweet.id}" data-name="{$reply.user_name}" data-child="{$reply.id}" data-loc="0">回复</span>{/if}{if $reply.user_id==$template.session.user_id||$tweet.user_id==$template.session.user_id||$isAdmin}<span id="delReply-{$reply.id}" onClick="ReplyDel({$reply.id})">删除</span>{/if}<a href="{$path}index.php?app=tweet&action=user&id={$reply.user_id}">{$reply.user_name}</a> 回复 <a href="{$path}index.php?app=tweet&action=user&id={$reply.reply_user_id}">{$reply.reply_user_name}</a>{$reply.content}</li>
						{else}
						
						<li id="reply-{$reply.id}">{if $template.session.user_id}<span class="data-box-reply" data-id="{$tweet.id}" data-name="{$reply.user_name}" data-child="{$reply.id}" data-loc="0">回复</span>{/if}{if $reply.user_id==$template.session.user_id||$tweet.user_id==$template.session.user_id||$isAdmin}<span id="delReply-{$reply.id}" onClick="ReplyDel({$reply.id})">删除</span>{/if}<a href="{$path}index.php?app=tweet&action=user&id={$reply.user_id}">{$reply.user_name}</a>{$reply.content}</li>

						{/if}

						{/foreach}

					{/if}
					
				</ul>
			</div>

		</li>

		{/foreach}

		{else}

		<li class="no-content">

		<p>无动态</p>

		</li>
		{/if}

		</ul>

	</article>

	<aside class="sr">

		<!--{include file=sideRight.php}-->

	</aside>

</section>

<!--{include file=footer.php}-->
</body>
</html>