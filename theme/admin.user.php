<!--{include file=admin.header.php}-->
<section>
	<div class="body">
		<h2>{$mode}</h2>
		{if $user}
		<form method="post" id="form">
			<table class="table table-striped table-bordered">
				<thead>
				<tr>
				<th class="text-center">选择</th>
				<th class="text-center">头像</th>
				<th class="text-center">昵称</th>
				<th class="text-center">邮箱</th>
				<th class="text-center">城市</th>
				<th class="text-center">最后登录时间</th>
				<th class="text-center">操作</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$user item=user}
				<tr id="user-{$user.id}">
				<td align="center"><input type="checkbox" class="checkbox" name="user_id[]" value="{$user.id}" /></td>
				<td align="center">
					<img src="{if $user.avatar!=''}{$user.avatar}{else}{$path}theme/style/avatar.jpg{/if}" width="30" height="30" />
				</td>
				<td align="center">
					<a href="{$path}index.php?app=tweet&action=user&id={$user.id}">{$user.name|escape:html}</a>
				</td>
		  		<td align="center">
		  			{$user.email|escape:html}
		  		</td>
				<td align="center">
					{$user.city|escape:html}
				</td>
				<td align="center">
					{$user.time}
				</td>
				<td align="center">
					{if $user.status==0}
					<a class="btn btn-warning" href="{$path}?app=user&action=update_user&user_id={$user.id}&status=2" title="禁止发言">禁言</a>
					{elseif $user.status==2}
					<a class="btn btn-warning" href="{$path}?app=user&action=update_user&user_id={$user.id}&status=0" title="解除禁言">解除禁言</a>
					{/if}
					{if $user.status==3}
					<a class="btn btn-warning" href="{$path}?app=user&action=update_user&user_id={$user.id}&status=0" title="降级为普通会员">设为普通会员</a>
					{elseif $user.status==0}
					<a class="btn btn-warning" href="{$path}?app=user&action=update_user&user_id={$user.id}&status=3" title="升级为管理员">升级</a>
					{/if}
				</td>
				</tr>
				{/foreach}
				</tbody>
				<tfoot>
				<tr>
				<th class="text-center"><input type='checkbox'  onclick="checkAll(this.checked)">反选</th>
				<th class="text-center"></th>
				<th class="text-center"></td>
				<th class="text-center"></td>
				<th class="text-center"></td>
				<th class="text-center"></td>
				<th class="text-center"><a class="btn btn-warning" href="javascript:void(Del())">删除所选会员</a></td>
				</tr>
				</tfoot>
			</table>
		</form>
		{$pager}
	{/if}
	</div>
</section>
<script>
function Del(){
	var status=false;
	$('.checkbox').each(function(){
		if($(this).prop('checked')){
			status=true;
		}
	});
	if(!status){
		alert('至少选择一项');
		return false;
	}
	$('#form').prop('action','index.php?app=user&action=delete_user');
	$('#form').submit();
}
function checkAll(checked)
{
   var allCheckBoxs=document.getElementsByName("user_id[]");
   for (var i=0;i<allCheckBoxs.length ;i++){
       if(allCheckBoxs[i].type=="checkbox"){
  allCheckBoxs[i].checked=checked;
       }
    }  
}
</script>