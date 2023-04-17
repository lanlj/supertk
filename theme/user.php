<?php exit?><!doctype html>
<html>
<head>
<meta charset="utf-8" />

{if $page_current>1}

<title>{$user.name} - {$sitename} - 第{$page_current}页</title>

{else}

<title>{$user.name} - {$sitename}</title>

{/if}

<meta name="keywords" content="{$user.name},{$keywords}">
<meta name="description" content="{$user.name}{$user.sign}">
<!--{include file=static.php}-->
<script type="text/javascript" src="{$path}theme/script/base.js"></script>
</head>
<body id="bind" data-menu="user" data-name="{$user.name}">

<!--{include file=header.php}-->

<section class="user-cover" {if $user.bg} style="background-image:url({$path}upload/images/{$user.bg})" {else}style="background-image:url({$path}theme/style/001.jpg)"{/if}></section>

<div class="user-box">

	<div class="user-info-box">

		<section class="container user-vri">

		<div class="user-avatar">
			<img src="{if $user.avatar!=''}{$user.avatar}{else}{$path}theme/style/avatar.jpg{/if}"/>
			{if $user.id!=$template.session.user_id&&$template.session.user_id>0}
				{if !$user.is_follow}
				<div class="btn btn-success follow follow-on" data-id="{$user.id}">关注</div>
				{else}
				<div class="btn btn-success follow follow-off" data-id="{$user.id}">取消关注</div>
				{/if}
			{else}
				<div class="btn btn-success follow" disabled>关注</div>
			{/if}
		</div>
		<a href="{$path}index.php?app=tweet&action=user&id={$user.id}" class="user-name">{$user.name}</a>

		<ul class="user-menu">

			<li><a><span>动态</span><strong>{$user.tweet}</strong></a></li>

			<li><a><span>照片</span><strong>{$user.album}</strong></a></li>

			<li><a><span>关注者</span><strong>{$user.follows}</strong></a></li>

			<li><a class="xs-hid"><span>UID</span><strong>{$user.id}</strong></a></li>

		</ul>
		</section>

		<div class="clear"></div>

	</div>

	<section class="container">

		<aside class="bsl">

	{if $albums}
	
		<div class="side">

				<h3 class="h3-title">
					照片
				</h3>

				<ul class="user-album">

		{foreach from=$albums item=image}
			
				<li><a href="{$path}upload/images/{$image}" rel="lightbox" data-name="{$user.name}" data-avatar="{if $user.avatar!=''}{$user.avatar}{else}{$path}theme/style/avatar.jpg{/if}" data-title="{$user.name} 的照片" data-lightbox="tweet-pic"><img src="{$path}thumb.php&#63;src={$path}upload/images/{$image}&#38;w=145&#38;h=140" alt="{$user.name}" title="{$user.name}"/></a></li>

				{/foreach}

				<div class="clear"></div>

				</ul>

		</div>

	{/if}

		<div class="side xs-hid">

			<h3 class="h3-title">
				个人资料
			</h3>

			<div class="user-profile">

				<p><strong>现居地:</strong>{if $user.city!=''}{$user.city}{else}火星{/if}</p>

				<p><strong>性别:</strong>{if $user.sex!=''}{$user.sex}{else}保密{/if}</p>

				<p><strong>年龄:</strong>{$user.old}</p>

				<p><strong>最后上线:</strong>{$user.logintime}</p>

				<p><strong>内心独白:</strong>{if $user.sign!=''}{$user.sign}{else}哈喽！大家好~这是我的另一个家，希望大家多多关注我的{$sitename}主页{/if}</p>

			</div>

		</div>

		{if $user_follow}
		<div class="side">
			<h3 class="h3-title">关注者</h3>
			<div class="user-list">
			{foreach from=$user_follow item=user}
			<a class="avatar" href="{$path}index.php?app=tweet&action=user&id={$user.id}" title="{$user.name}"><img src="{if $user.avatar!=''}{$user.avatar}{else}{$path}theme/style/avatar.jpg{/if}"/></a>
			{/foreach}
			</div>
		</div>
		{/if}

		</aside>

		<article class="sm">

		<h2 class="h2-title">全部动态</h2>
 <ul id="tweet-list" >
		{if $tweet}
      
		
			{foreach from=$tweet item=tweet}
			
			<li id="tweet-{$tweet.id}" class="tweet" data-archives="{$tweet.archives}" data-time="{$tweet.date}">

				<div class="tweet-inner">

					<span class="tweet-time">{$tweet.lastdate}{if $tweet.status==1} <i class="icon icon-suoding"></i>{/if}</span>

					<a class="tweet-user" href="{$path}index.php?app=tweet&action=user&id={$tweet.user_id}">
					
						<img class="tweet-avatar" src="{if $tweet.user_avatar!=''}{$tweet.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}"/>
						<div class="tweet-name">{$tweet.user_name}</div>

					</a>

				</div>

				<div class="tweet-inner tweet-text clearleft">

					{if $tweet.title!=''}

					<h3 class="post-title">
						提问题说:
						<a class="a" href="{$path}index.php?app=tweet&action=view&id={$tweet.id}">{$tweet.title}
						</a>
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
					
					<a class="one-pic" href="{$path}upload/images/{$image.img}" rel="lightbox" data-name="{$tweet.user_name}" data-avatar="{if $tweet.user_avatar!=''}{$tweet.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}" data-title="{$tweet.description}" data-lightbox="tweet-pic-{$tweet.id}" style="background-image:url({$path}thumb.php&#63;src={$path}upload/images/{$image.img}&#38;w=350&#38;h=250&#38;zc=1&#38;q=100)">
					{if $image.ext=='gif'}<i class="gif icon icon-play"></i>{/if}</a>

					{else}
					<a class="mut-pic" href="{$path}upload/images/{$image.img}" rel="lightbox" data-name="{$tweet.user_name}" data-avatar="{if $tweet.user_avatar!=''}{$tweet.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}" data-title="{$tweet.description}" data-lightbox="tweet-pic-{$tweet.id}"><img src="{$path}thumb.php&#63;src={$path}upload/images/{$image.img}&#38;w=180&#38;h=180&#38;zc=1&#38;q=100"/></a>

					{/if}

					{/foreach}

					</div>

					{/if}

				{/if}

				<div class="tweet-inner tweet-oter clearleft">

				

					<span><i class="icon icon-locationfill"></i>{if $tweet.ip!=''}{$tweet.ip}{else}火星{/if} </span>


					{if $tweet.agent!=''}

					<span>{$tweet.agent}</span>

					{/if}

				</div>
					
				<div class="tweet-data">

					<div class="data-box data-box-line data-box-reply" data-id="{$tweet.id}" data-name="{$tweet.user_name}" data-child="0" data-loc="0"><i class="icon icon-comment"></i>评论{if $tweet.reply_num>0}({$tweet.reply_num}){/if}{if $tweet.reply}<div class="arrow-top2"></div><div class="arrow-top"></div>{/if}</div>

					<div class="data-box like {if $tweet.like_status>0}liked{/if}" id="like-{$tweet.id}"data-id="{$tweet.id}">{if $tweet.like_status>0}<i class="icon icon-likefill"></i>喜欢{if $tweet.likes>0}({$tweet.likes}){/if}{else}<i class="icon icon-like"></i>喜欢{if $tweet.likes>0}({$tweet.likes}){/if}{/if}</div>

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
</ul>
		<div class="no-content">家里太空了，啥都没有~</div>
		{/if}

		{if $pager}

		{$pager}

		{/if}

		</article>

		{if $timeline}

		<aside class="timeline-box xs-hid">
			<ul class="timeline">

				<li class="year year-active">最近</li>

				{foreach from=$timeline item=timeline key=key}

				<li id="year-{$key}" class="year" data-year="{$key}">{$key}</li>
					{if $timeline}
					<ul class="month">
					{foreach from=$timeline item=time}

					<li id="mon-{$time.year}-{$time.mon}" class="mon">{$time.mon}月<span>({$time.count})</span></li>

					{/foreach}
					</ul>
					{/if}
				{/foreach}
			</ul>
		</aside>

		{/if}

		<div class="clear"></div>

		</section>
		
</div>

<!--{include file=footer.php}-->

</body>
</html>