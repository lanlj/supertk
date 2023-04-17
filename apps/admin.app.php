<?php
!defined('DROP') && exit('REFUSED!');
class admin extends Core{

	public function install(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$this->query("insert into apps set app_name='admin',app_key=''");
	}
	public function uninstall(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$this->query("delete from apps where app_name='admin'");

	}
	public function setting(){
		return '<form action="'.PATH.'?app=admin&action=setting_update" method="post">
		<h4>系统设置</h4>
		<fieldset>
		<legend>基本信息设置</legend>
		<table width="100%" class="form-table">
			<tr>
			<td class="input-name">站点标题：</td>
			<td><input type="text" size="50" name="sitename" value="'.@$this->config['sitename'].'" class="input"/></td>
			</tr>
			<tr>
			<td class="input-name">站点副标题：</td>
			<td><input type="text" size="50" name="sitesubname" value="'.@$this->config['sitesubname'].'" class="input"/></td>
			</tr>
			<tr>
			<td class="input-name">站点关键字(SEO)：</td>
			<td><input type="text" size="60" name="sitekeywords" value="'.@$this->config['sitekeywords'].'" class="input"/> 英文逗号隔开</td>
			</tr>
			<tr>
			<td class="input-name  valign-top">网站描述：</td>
			<td><textarea class="input" name="sitedescription" rows="3" cols="60">'.@$this->config['sitedescription'].'</textarea></td>
			</tr>
			<tr>
			<td class="input-name  valign-top">网站公告：</td>
			<td><textarea class="input" name="notice" rows="3" cols="60">'.@$this->config['notice'].'</textarea></td>
			</tr>
			<tr>
			<td class="input-name  valign-top">网站统计代码：</td>
			<td><textarea class="input" name="statcode" rows="4" cols="60">'.@$this->config['statcode'].'</textarea></td>
			</tr>
			<tr>
			<td class="input-name">网站备案号：</td>
			<td><input type="text" size="50" name="icp" value="'.@$this->config['icp'].'" class="input"/></td>
			</tr>
			<tr>
			<td class="input-name  valign-top">网站广告代码：</td>
			<td><textarea class="input" name="ad" rows="4" cols="60">'.@$this->config['ad'].'</textarea></td>
			</tr>
		</table>
		</fieldset>
		<div class="form-submit">
		<input type="submit" value="保存更改" class="btn btn-primary btn-sm"/>
		</div>
		</form>';
	}
	public function setting_update(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$array=array();
		$array['sitename']=empty($_POST['sitename'])?'':trim($_POST['sitename']);
		$array['sitesubname']=empty($_POST['sitesubname'])?'':addslashes(trim($_POST['sitesubname']));
		$array['sitekeywords']=empty($_POST['sitekeywords'])?'':addslashes(trim($_POST['sitekeywords']));
		$array['sitedescription']=empty($_POST['sitedescription'])?'':addslashes(trim($_POST['sitedescription']));
		$array['statcode']=empty($_POST['statcode'])?'':trim($_POST['statcode']);
		$array['notice']=empty($_POST['notice'])?'':trim($_POST['notice']);
		$array['ad']=empty($_POST['ad'])?'':trim($_POST['ad']);
		$array['icp']=empty($_POST['icp'])?'':trim($_POST['icp']);
		$app_key=base64_encode(serialize($array));
		$this->update("apps",array('app_key'=>$app_key),"app_name='admin'");
		alert('网站设置成功!');
		redirect(PATH.'?app=set&action=control&do=setting');
	}
	
}
?>