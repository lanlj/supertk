<div class="clear"></div>

{if !$isMobile}

<footer class="footer">

	© 2015-2017 SuperTK All Rights Reserved.<a href="http://www.miitbeian.gov.cn/" target="_blank"> {$config.icp}</a>{$config.statcode}

</footer>

{else}

<footer class="footer">© 2015 SuperTK | 触屏版</footer>

<div class="wap-set">
	<ul>
		<li><a href="{$path}?app=control&action=set&do=profile">个人资料</a></li>
		<li><a href="{$path}?app=control&action=set&do=nickname">修改昵称</a></li>
		<li><a href="{$path}?app=control&action=set&do=avatar">上传头像</a></li>
		<li><a href="{$path}?app=control&action=set&do=userbg">上传封面</a></li>
		<li><a href="{$path}?app=control&action=set&do=password">修改密码</a></li>
	</ul>
</div>

<div class="wap-box"></div>

{/if}

<div id="tip"></div>
<a id="totop" href="javascript:void(0);" title="回顶部"><i class="icon icon-xiangshang"></i></a>