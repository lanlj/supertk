<!--{include file=admin.header.php}-->
<section>
	<div class="body">
		<h2>{$mode}</h2>
	{if $template.get.do=='avatar'}
		<div class="set-tip"><i class="icon icon-info fa"></i>上传头像仅支持jpg格式，尺寸不能小于 200*200 或大于1M，建议上传正方形照</div>
		<div id="upload-avatar"><input type="file" name="files" accept="image/jpeg" title="选择一张jpg图片"></div>
		<div id="images-avatar" class="images"><img src="{if $template.session.user_avatar!=''}{$template.session.user_avatar}{else}{$path}theme/style/avatar.jpg{/if}"/></div>
		<div id="upload-speed"></div>
		<p id="success" style="line-height:50px;"></p>
	{elseif $template.get.do=='userbg'}
		<div class="set-tip"><i class="icon icon-info fa"></i>上传封面将会展示在你的主页上，支持JPG格式大图</div>
		<div id="upload-bg"><input type="file" name="files" accept="image/jpeg" title="选择一张jpg图片"></div>
		<div id="upload-speed"></div>
	{elseif $template.get.do=='password'}
	{if $template.session.user_email==''}
	<div class="set-tip"><i class="icon icon-info fa"></i>您还没有设置邮箱帐号哦~<a href="{$path}?app=control&action=set&do=profile"><strong>现在就去设置</strong></a></div>
	{else}
	<form action="{$path}?app=control&action=set&do=password&insert=1" method="post">
		<div class="set-tip"><i class="icon icon-info fa"></i>妈妈说密码最好6位以上，英文数字和符号组合在一起才是一家~</div>
		<table width="100%" class="form-table">
			<tr>
			<td class="input-name">新密码：</td>
			<td><input type="text" size="30" name="pass" class="input"/></td>
			</tr>
			<tr>
			<td class="input-name">确认密码：</td>
			<td><input type="text" name="pass_confirm" size="30" class="input"/></td>
			</tr>
			<tr>
			<td align="right" height="40">&nbsp;</td>
			<td>
			<input type="submit" value="保存新密码" class="btn btn-primary btn-sm"/>
			</td>
			</tr>
		</table>
	</form>	
	{/if}
	{elseif $template.get.do=='nickname'}
	<form action="{$path}?app=control&action=set&do=nickname&insert=1" method="post">
		<div class="set-tip"><i class="icon icon-info fa"></i>妈妈说昵称不要太长哦~爸爸还说昵称不要带符号不然会@不到~</div>
		<table width="100%" class="form-table">
			<tr>
			<td class="input-name">当前昵称：</td>
			<td><input type="text" size="30" value="{$template.session.user_name}" class="input" disabled/></td>
			</tr>
			<tr>
			<td class="input-name">新昵称：</td>
			<td><input type="text" name="user_name" size="30" value="" class="input"/></td>
			</tr>
			<tr>
			<td align="right" height="40">&nbsp;</td>
			<td>
			<input type="submit" value="保存更改" class="btn btn-primary btn-sm"/>
			</td>
			</tr>
		</table>
	</form>
	{elseif $template.get.do=='profile'}
	<form action="{$path}?app=control&action=set&do=profile&insert=1" method="post">
		<table width="100%" class="form-table">
			<tr>
			<td class="input-name">邮箱：</td>
			<td><input type="text" size="30" name="user_email" value="{$template.session.user_email}" class="input" {if $template.session.user_email!=''} disabled {/if}/></td>
			</tr>
			<tr>
			<td class="input-name">年龄：</td>
			<td><input type="text" size="2" name="user_old" value="{$user.old}" class="input"/></td>
			</tr>
			<tr>
			<td class="input-name">性别：</td>
			<td><input type="radio" name="user_sex" id="user_sex" value="男"{if $user.sex=='男'}checked{/if}/> 男
			<input type="radio" name="user_sex" id="user_sex" value="女" {if $user.sex=='女'}checked{/if}/> 女</td>
			</tr>
			<tr>
			<td class="input-name valign-top">内心独白：</td>
			<td><textarea tabindex="2" class="textarea" name="user_sign"><!--{$user.sign|escape:html}--></textarea></td>
			</tr>
			<tr>
			<td align="right" height="40">&nbsp;</td>
			<td>
			<input type="submit" value="保存更改" class="btn btn-primary btn-sm"/>
			</td>
			</tr>
		</table>
	</form>
	{elseif $template.get.do=='linkadd'}
	<form action="{$path}?app=control&action=set&do=linkadd&insert=1" method="post">
		<table width="100%" class="form-table">
			<tr>
			<td class="input-name">链接名称：</td>
			<td><input type="text" size="50" name="link_name" value="" class="input"/></td>
			</tr>
			<tr>
			<td class="input-name">链接描述：</td>
			<td><input type="text" size="50" name="link_text" value="" class="input"/></td>
			</tr>
			<tr>
			<td class="input-name">链接地址：</td>
			<td><input type="text" size="50" name="link_url" value="" class="input"/> 注意加http://</td>
			</tr>
			<tr>
			<td align="right" height="40">&nbsp;</td>
			<td>
			<input type="submit" value="保存更改" class="btn btn-primary btn-sm"/>
			</td>
			</tr>
		</table>
	</form>
	{elseif $template.get.do=='linkedit'}
	<form action="{$path}?app=control&action=set&do=linkedit&insert=1" method="post">
		<table width="100%" class="form-table">
			<tr>
			<td><input type="hidden" size="50" name="link_id" value="{$link.id}" class="input"/></td>
			</tr>
			<tr>
			<td class="input-name">链接名称：</td>
			<td><input type="text" size="50" name="link_name" value="{$link.name}" class="input"/></td>
			</tr>
			<tr>
			<td class="input-name">链接描述：</td>
			<td><input type="text" size="50" name="link_text" value="{$link.text}" class="input"/></td>
			</tr>
			<tr>
			<td class="input-name">链接地址：</td>
			<td><input type="text" size="50" name="link_url" value="{$link.url}" class="input"/> 注意加http://</td>
			</tr>
			<tr>
			<td align="right" height="40">&nbsp;</td>
			<td>
			<input type="submit" value="保存更改" class="btn btn-primary btn-sm"/>
			</td>
			</tr>
		</table>
	</form>
	{elseif $template.get.do=='link'}
	<table class="table table-striped table-bordered">
		<thead>
		<tr>
		<th class="text-center" width="30">ID</th>
		<th class="text-center">链接名称</td>
		<th class="text-center">链接描述</td>
		<th class="text-center">链接地址</td>
		<th class="text-center" width="80">操作</td>
		</tr>
		</thead>
		<tbody>
		{foreach from=$links item=link}
		<tr>
		<td align="center">{$link.id}</td>
		<td align="center">
			{$link.name}
		</td>
  		<td align="center">
  			{$link.text}
  		</td>
		<td align="center">
			{$link.url}
		</td>
  		<td align="center">
  			<a href="{$path}?app=control&action=set&do=linkedit&linkid={$link.id}" title="">编辑</a>
  			<a href="{$path}?app=control&action=set&do=linkdel&linkid={$link.id}" title="">删除</a>
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
		<th class="text-center"><a class="btn btn-primary" href="{$path}?app=control&action=set&do=linkadd" title="">添加链接</a></td>
		</tr>
		</tfoot>
	</table>
	{elseif $template.get.do=='apps'}
	<table class="table table-striped table-bordered">
		<thead>
		<tr>
		<th class="text-center">应用名称</td>
		<th class="text-center">应用描述</td>
		<th class="text-center">版本</td>
		<th class="text-center">作者</td>
		<th class="text-center" width="80">操作</td>
		</tr>
		</thead>
		<tbody>
		{foreach from=$apps item=app}
		<tr>
		<td align="center">
			<strong>{$app.name}</strong>
		</td>
  		<td align="center">
  			{$app.description}
  		</td>
		<td align="center">
			{$app.version}
		</td>
		<td align="center">
			{$app.author}
		</td>
  		<td align="center">
  			{if !$app.install}
  			<a href="{$path}?app=control&action=app_install&app_name={$app.app}" class="btn btn-success btn-sm">安装</a>
  			{else}
  			<a href="{$path}?app=control&action=app_uninstall&app_name={$app.app}" class="btn btn-warning btn-sm">卸载</a>
  			{/if}
  		</td>
		</tr>
		{/foreach}
		</tbody>
	</table>
	{elseif $template.get.do=='setting'}

		{foreach from=$setting item=set}
		{$set}
		{/foreach}
	{elseif $template.get.do=='service'}
	<div class="info">
		<ul>
			<li>服务器引擎：{$server.software}</li>
			<li>服务器端口：{$server.port}</li>
			<li>网站域名：{$server.name}</li>
			<li>服务器版本：{$server.os}</li>
			<li>数据库版本：MYSQL {$server.db_version}</li>
			<li>PHP版本：{$server.version}</li>
			<li>网站根目录：{$server.root}</li>
			<li>最大上传值：{$server.upload}</li>
			<li>会话超时：{$server.timeout} 分</li>
			<li>占用内存：{$server.memory_usage}</li>
			{if $server.disable_functions}
			<li>禁用函数：{$server.disable_functions}</li>
			{/if}
		</ul>
	</div>
	{/if}
	</div>
	<footer>Power by Er8d.com</footer>
</section>