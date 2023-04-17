<!--{include file=admin.header.php}-->
<section>
	<div class="body">
		<h2>{$mode}</h2>
		{if $tag}
		<form method="post" id="form">
			<table class="table table-striped table-bordered">
				<thead>
				<tr>
				<th class="text-center">选择</th>
				<th class="text-center">标签</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$tag item=tag}
				<tr id="tag-{$tag.id}">
				<td align="center">
				<input type="checkbox" class="checkbox" name="tag_id[]" value="{$tag.id}" /></td>
				<td align="center">
				<a href="{$path}index.php?app=tweet&action=tag&keywords={$tag.name}">{$tag.name|escape:html}（{$tag.count|escape:html}）</a>
				</td>
				</tr>
				{/foreach}
				</tbody>
				<tfoot>
				<tr>
				<th class="text-center"><input type='checkbox'  onclick="checkAll(this.checked)">反选</th>
				<th class="text-center"><a class="btn btn-warning" href="javascript:void(Del())">删除所选标签</a>
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
	$('#form').prop('action','index.php?app=tag&action=delete_tag');
	$('#form').submit();
}
function checkAll(checked)
{
   var allCheckBoxs=document.getElementsByName("tag_id[]");
   for (var i=0;i<allCheckBoxs.length ;i++){
       if(allCheckBoxs[i].type=="checkbox"){
  allCheckBoxs[i].checked=checked;
       }
    }  
}
</script>