{if $template.session.user_id}
<div class="tabmu clear">
<ul class="tab-menu">
<li id="tab-home-m"><a href="{$path}"><i class="icon icon-friend"></i>全部动态</a></li>
<li id="tab-my-m"><a href="{$path}index.php?myfeed"><i class="icon icon-my"></i>好友圈</a></li>
</ul>
</div>
{/if}