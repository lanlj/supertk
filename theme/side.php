{if $template.session.ins_Token}
<!--<a class="viewIns" href="{$path}instagram.php?login=load&do=myfeed" target="_blank">查看我的Instagram最新动态</a>-->
<a class="backup" href="{$path}?app=user&action=backup" target="_blank">备份Instagram照片到{$sitename}</a>
{/if}
<div class="side-box">
<h3 class="side-head">喜欢排行榜 ······</h3>

<ul class="list">
{foreach from=$hots item=tweet name=tweet}
	<li><span class="num {if $template.foreach.tweet.index<=2}hot{/if}">{$tweet.num}</span><a href="{$path}index.php?app=tweet&action=view&id={$tweet.id}" title="{$tweet.user_name}的动态:{$tweet.title}">{$tweet.title}</a></li>
{/foreach}
</ul>
</div>
<div class="side-box">
<h3 class="side-head">遇见他/她们 ······</h3>
<ul class="user-list">
{foreach from=$users item=user}
	<li class="user-item">
		<a href="{$path}index.php?app=tweet&action=user&id={$user.id}" title="{$user.name}">
		<img class="u_avatar" src="{if $user.avatar!=''}{$user.avatar}{else}{$path}theme/style/avatar.jpg{/if}" alt="{$user.name}" />
		<div class="u_name">
			<p>{$user.name}{if $user.is_new}<span>[最近在线]</span>{/if}</p>
			<p class="info">{if $user.city!=''}{$user.city}{else}火星{/if} {$user.sign}</p>
		</div>
		</a>
	</li>
	<div class="clear"></div>
{/foreach}
</ul>
</div>
<div class="side-box">
<h3 class="side-head">赞助{$sitename} ······</h3>
{$config.ad}
</div>