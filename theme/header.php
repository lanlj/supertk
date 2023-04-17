{if $isMobile}
<header class="wap-menu">
	<div class="wap-menu-close"><i class="icon icon-liebiao"></i></div>
	<ul>
		<li class="avatar"><img src="{if $template.session.user_avatar!=''}{$template.session.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}" alt="{$template.session.user_name}" title="{$template.session.user_name}"/>{if $template.session.user_name}{$template.session.user_name}{else}游客你好！{/if}</li>
   {if $template.session.user_id}
		<li><a href="{$path}"><i class="icon icon-homefill"></i><span>首页</span></a></li>
		<li><a href="{$path}index.php?myfeed"><i class="icon icon-myfill"></i><span>好友圈</span></a></li>
		<li class="pr"><a href="{$path}index.php?app=tweet&action=notice&do=get&isread=1&read" >{if $notice>0}<span class="notifi">{$notice}</span>{/if}<i class="icon icon-tixing"></i><span>通知</span></a></li>
		<li><a href="{$path}index.php?app=tweet&action=user&id={$template.session.user_id}"><i class="icon icon-yonghu1"></i><span>主页</span></a></li>
		<li><a href="{$path}index.php?app=user&action=logout"><i class="icon icon-tuichu logout"></i><span>退出</span></a></li>
	{else}
		<li><a href="{$path}index.php?app=user&action=signup" title="登录"><i class="icon icon-yonghu"></i><span>登录</span></a></li>
		<li><a href="{$path}index.php?app=user&action=signup#register" title="注册"><i class="icon icon-shumiao"></i><span>注册</span></a></li>
	{/if}
	</ul>
</header>
{/if}
<header class="header">
	<nav class="container pr">
	<div class="row">
		<div class="wap-menu-btn wap-menu-list"><i class="icon icon-liebiao"></i></div>
		<h1 id="wap-head" class="logo"><a href="{$path}" title="{$sitename}"><img src="{$path}theme/style/logo.png" alt="{$sitesubname}" title="{$sitename}" /></a></h1>
		<menu class="menu xs-hid">
			<ul>
				<li><a id="menu-index" href="{$path}" title="{$sitename}">首页</a></li>
			</ul>
		</menu>
		<div class="search xs-hid">
			<span class="search-input">
			<input type="text" id="search-text" placeholder="输入关键字回车"/>
			</span>
			<div id="search" class="search-btn"><i class="icon icon-sousuo"></i></div>
			<div class="clear"></div>
		</div>
		{if $template.session.user_id}
		<div class="user_info" data-user="{$template.session.user_id}" data-not="{$notice}">
			<div class="static_num load_info">
				<a href="{$path}index.php?app=tweet&action=user&id={$template.session.user_id}" title="我的主页" rel="nofollow" class="dl user xs-hid">
					<p class="dt"><img class="avatar" src="{if $template.session.user_avatar!=''}{$template.session.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}"/><span class="user_name">{$template.session.user_name}</span></p>
				</a>
				<div class="dl set">
					<p class="dt"><i class="icon icon-shezhi"></i></p>
					{if !$isMobile}
					<div class="set-more">
					<ul>
					<li><a href="{$path}index.php?app=control&action=set&do=profile">个人资料</a></li>
					<li><a href="{$path}index.php?app=control&action=set&do=password">修改密码</a></li>
					<li><a href="{$path}index.php?app=control&action=set&do=nickname">修改昵称</a></li>
					<li><a href="{$path}index.php?app=control&action=set&do=avatar">上传头像</a></li>
					<li><a href="{$path}index.php?app=control&action=set&do=userbg">上传封面</a></li>
					{if $isAdmin}
					<li><a href="{$path}index.php?app=control&action=set&do=apps">应用管理</a></li>
					<li><a href="{$path}index.php?app=control&action=set&do=setting">后台设置</a></li>
					<li><a href="{$path}index.php?app=control&action=set&do=link">链接管理</a></li>
					<li><a href="{$path}index.php?app=control&action=clear">清理缓存</a></li>
					{/if}
					</ul>
					</div>
					{/if}
				</div>
				<a id="GetNotice" href="{$path}index.php?app=tweet&action=notice&do=get&isread=1&read" rel="nofollow" class="dl pr xs-hid">
				<p class="dt"><i class="icon icon-tixing"></i></p>
					{if $notice>0}<div class="notification" title="{$notice}条未读提醒">{$notice}</div>{/if}
				</a>
				<a href="{$path}index.php?app=user&action=logout" title="退出登录" class="dl xs-hid">
					<p class="dt"><i class="icon icon-tuichu"></i></p>
				</a>
				<div class="clear"></div>
			</div>
			<div class="notice-more">
				<div class="arrow"></div>
				<div class="arrow1"></div>
				<ul class="no">
				</ul>
			</div>
		</div>
		{else}
		<div class="user_info">
			<div class="static_num">
				<a class="dl" href="{$path}index.php?app=user&action=signup" title="登录">
					<p class="dt"><i class="icon icon-yonghu salt"></i></p>
				</a>
			<a class="dl xs-hid" href="{$path}index.php?app=user&action=signup#register" title="注册">
				<p class="dt"><i class="icon icon-shumiao"></i></p>
			</a>
			</div>
		</div>
		{/if}
	</div>
	</nav>
	<div class="clear"></div>
</header>