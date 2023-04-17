{if $template.session.ins_Token}
<a class="backup" href="{$path}?app=user&action=view&do=myfeed" target="_blank">查看我的Instagram动态</a>
<a class="backup" href="{$path}?app=user&action=backup" target="_blank">备份Instagram照片到{$sitename}</a>
{/if}

<div class="side">

	<h3 class="h3-title">小广播</h3>

	<div class="side-content">

	{$config.notice}
	
	</div>

</div>

{if $tags}
<div class="side">

	<h3 class="h3-title">热门标签</h3>

	<div class="tags">

	{foreach from=$tags item=tag}

		<a href="{$path}index.php?app=tweet&action=tag&keywords={$tag.name|escape:urlencode}" title="{$tag.count}条相关动态">
		
		{$tag.name}({$tag.count})</a>

	{/foreach}
	</div>

</div>

{/if}
<div class="side">

	<h3 class="h3-title">推荐用户</h3>

	<ul>
	{foreach from=$users item=user}
		<li class="hot-user">
		<a class="hot-ava" href="{$path}index.php?app=tweet&action=user&id={$user.id}" title="{$user.name}">
			<img src="{if $user.avatar!=''}{$user.avatar}{else}{$path}theme/style/avatar.jpg{/if}" alt="{$user.name}" />
			<span class="hot-name">{$user.name}</span>
			<div class="hot-pt">
				{if $user.city!=''}{$user.city}{else}火星{/if} {$user.sign}
			</div>
			<span class="at-user" data-name="{$user.name}">@TA</span>
		</a>
		</li>
	{/foreach}
	</ul>

</div>

{if !$isMobile}
<div class="side">
<div id="navigator2">
<div class="side">
	<div class="ad-box">
     {$config.ad}
    </div>
</div>
</div>
</div>
{/if}
</div>
