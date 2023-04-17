{if $template.session.user_id}

	<div class="user-item">

		<a class="user-item-bg" href="{$path}index.php?app=tweet&action=user&id={$template.session.user_id}">

			<img class="user-bg" src="{if $user_bg!=''}{$path}thumb.php&#63;src={$path}upload/images/{$user_bg}&#38;w=290&#38;h=112&#38;zc=1{else}{$path}thumb.php&#63;src={$path}theme/style/001.jpg&#38;w=290&#38;h=112&#38;zc=1{/if}"/>

			<img src="{if $template.session.user_avatar!=''}{$template.session.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}" class="user-avatar"/>

			<p class="user-name">{$template.session.user_name}</p>
		</a>

		<ul class="user-item-line">

			<li class="user-atten"><a href="{$path}index.php?app=tweet&action=user&id={$template.session.user_id}"><strong id="update_tweet_num">{$tweet_count}</strong><span>动态</span></a></li>

			<li class="user-atten"><a href="{$path}index.php?app=tweet&action=user&id={$template.session.user_id}"><strong id="update_album_num">{$album_count}</strong><span>照片</span></a></li>

			<li class="user-atten"><a href="{$path}?app=tweet&action=notice&do=get&isread=1&read"><strong>{$notice}</strong><span>通知</span></a></li>
			<div class="clear"></div>

		</ul>

	</div>

	<div class="side">
		<ul class="tab-menu">
			<li id="tab-home"><a href="{$path}"><i class="icon icon-friend"></i>全部动态</a></li>
			<li id="tab-my"><a href="{$path}index.php?myfeed"><i class="icon icon-my"></i>好友圈</a></li>
		</ul>
	</div>

{/if}

{if $hots}
	<div class="side">

		<h3 class="h3-title">趋势</h3>

		<ul>

		{foreach from=$hots item=tweet name=tweet}

			<li class="list">
				<a class="list-thumb" href="{$path}index.php?app=tweet&action=view&id={$tweet.id}" title="{$tweet.user_name}的动态:{$tweet.title}">
					{if $tweet.images.0!=''}
					<img src="{$path}thumb.php&#63;src={$path}upload/images/{$tweet.images.0}&#38;w=60&#38;h=60&#38;zc=1"/>
					{else}
					<i class="icon icon-tuwenxiangqing"></i>
					{/if}
					<span class="num {if $template.foreach.tweet.index<=2}hot{/if}">{$tweet.num}</span>
					<span class="list-title">{$tweet.title}</span>
					<span class="list-likes">{$tweet.likes}个喜欢</span>
				</a>
			</li>

		{/foreach}
		</ul>
		
	</div>
{/if}