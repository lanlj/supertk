{if $template.session.user_id}

<h2 class="h2-title"><span class="add-title"><i class="icon icon-add"></i>提问</span>来一句<i class="icon icon-xiangxia"></i></h2>

<form class="tweet-form">

	<input type="hidden" id="tweet-file" value="" />

	<input type="text" id="tweet-title" class="text-input" placeholder="在这里添加一个标题"/>
	<div class="edit-tool">
		<div id="quote-tool" class="edit-tool-box" data-tip="引用"><i class="icon icon-quoteright"></i></div>
		<div id="b-tool" class="edit-tool-box" data-tip="加粗"><i class="icon icon-jiacu"></i></div>
		<div id="format-tool" class="edit-tool-box" data-tip="自动排版"><i class="icon icon-wenjian"></i></div>
		<div class="clear"></div>
	</div>
	<textarea class="text-area" onkeydown="if(event.ctrlKey&&event.keyCode==13 || event.keyCode==10){document.getElementById('tweet-submit').click();return false};" id="tweet-content" placeholder="说点儿什么吧..."></textarea>

	<div class="tweet-tool">

		<div class="emoji"><i class="icon icon-biaoqing"></i><div class="emot-put"><div class="arrow"></div><div id="In_dex" class="emot-in"></div></div></div>

		<div class="upload-img" id="upload-img"><i class="icon icon-camerafill"></i><input type="file" name="files[]" accept="image/jpeg,image/gif,image/png" {if !$isMobile} multiple {/if}></div>

		<div class="btn btn-primary right" id="tweet-submit">发送</div>
		<div class="push-checkbox"><input type="checkbox" id="tb-status" value="1"> 私信</div>
		{if $template.session.weibo_token}
		<div class="push-checkbox"><input type="checkbox" id="tb-wb" value="1"> 同步到新浪微博</div>
		{/if}

	</div>

	<div class="clear"></div>

	<div id="fileList"></div>

</form>

{/if}