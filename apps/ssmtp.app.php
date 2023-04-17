<?php 
class ssmtp extends Core{
public function setting(){
		$ssmtp='
		<form action="'.PATH.'?app=ssmtp&action=setting_update" method="post">
			<h5>邮件</h5>
			<fieldset>
			<legend>配置</legend>
			<table cellspacing="10" class="form-table">
				<tr>
				<td class="input-name">smtp服务器：</td>
				<td><input type="text" size="50" name="ssmtp" class="input" value='.@$this->config["ssmtp"].' /></td>
				</tr>	
				<tr>
				<td class="input-name">端口：</td>
				<td><input type="text" size="50" name="sport" class="input" value='.@$this->config["sport"].' /></td>
				</tr>				
				<tr>
				<td class="input-name">账号：</td>
				<td><input type="text" size="50" name="suser" class="input" value='.@$this->config["suser"].' /></td>
				</tr>				<tr>
				<td class="input-name">密码：</td>
				<td><input type="text" size="50" name="spass" class="input" value='.@$this->config["spass"].' /></td>
				</tr>
			</table>
			</fieldset>
				<div class="form-submit">
			<input type="submit" value="保存更改" class="btn btn-primary btn-sm"/>
			</div>
		</form>';
		return $ssmtp;
	}

public function setting_update(){
		if(!$this->check_access())exit("You don't have permission to access!");
		$array=array();
		$array['ssmtp']=empty($_POST['ssmtp'])?'':addslashes(trim($_POST['ssmtp']));
		$array['sport']=empty($_POST['sport'])?'':addslashes(trim($_POST['sport']));
		$array['suser']=empty($_POST['suser'])?'':addslashes(trim($_POST['suser']));
		$array['spass']=empty($_POST['spass'])?'':addslashes(trim($_POST['spass']));
		$app_key=base64_encode(serialize($array));
		$this->update("apps",array('app_key'=>$app_key),"app_name='ssmtp'");
		alert('保存成功!');
		redirect(PATH.'?app=set&action=control&do=setting');
	}
  public function install(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$this->query("insert into apps set app_name='ssmtp',app_key='YTo0OntzOjU6InNzbXRwIjtzOjEzOiJzbXRwLmRlbW8uY29tIjtzOjU6InNwb3J0IjtzOjI6IjI1IjtzOjU6InN1c2VyIjtzOjEyOiJkZW1vQDE2My5jb20iO3M6NToic3Bhc3MiO3M6NDoiZGVtbyI7fQ=='");
	}
	public function uninstall(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$this->query("delete from apps where app_name='ssmtp'");
	}

}
?> 