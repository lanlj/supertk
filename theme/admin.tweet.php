<!--{include file=admin.header.php}-->
<section>
	<div class="body">
		<h2>{$mode}</h2>
		{if $template.get.do=='tweet_list'}
		<form method="post" id="form">
			<table class="table table-striped table-bordered">
				<thead>
				<tr>
				<th class="text-center" width="30">ID</th>
				<th class="text-center">动态内容</td>
				<th class="text-center">用户</td>
				<th class="text-center" width="110">时间</td>
				<th class="text-center" width="80">操作</td>
				</tr>
				</thead>
				<tbody>
				{foreach from=$tweet item=tweet}
				<tr id="tweet-{$tweet.id}">
				<td align="center"><input type="checkbox" class="checkbox" name="tid[]" value="{$tweet.id}" /></td>
				<td align="center">
					{$tweet.content}
				</td>
		  		<td align="center">
		  			{$tweet.user_name}
		  		</td>
				<td align="center">
					{$tweet.lastdate}
				</td>
		  		<td align="center">
		  			<div class="btn btn-warning btn-sm" id="delTweet-{$tweet.id}" onclick="TweetDel({$tweet.id});">删除</a>
		  		</td>
				</tr>
				{/foreach}
				</tbody>
				<tfoot>
				<tr>
				<th class="text-center"></th>
				<th class="text-center"></td>
				<th class="text-center"></td>
				<th class="text-center"></td>
				<th class="text-center"><a class="btn btn-warning" href="javascript:void(Del())">删除所选</a></td>
				</tr>
				</tfoot>
			</table>
		</form>
			{$pager}
		{/if}
	</div>
</section>
<script>
function TweetDel(id){
	var that = $("#delTweet-"+id);
	that.removeAttr("onclick");
	that.html(that.html().replace("删除","确认删除?"));
	that.click(function(){
		$.ajax({
			type: "GET",
			url: Path+"?app=tweet&action=delete&tid="+id,
			success: function(e) {
				$('#tweet-'+id).fadeOut();
			}
		});
	});
}
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
	$('#form').prop('action','/?app=tweet&action=delete');
	$('#form').submit();
}
</script>