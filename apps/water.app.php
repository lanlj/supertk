<?php 
class water extends Core{
public function setting(){
		$water='
		<form action="'.PATH.'?app=water&action=setting_update" method="post">
			<h5>水印</h5>
			<fieldset>
			<legend>水印</legend>
			<table cellspacing="10" class="form-table">
				<tr>
				<td class="input-name">水印图片：</td>
				<td><input type="text" size="50" name="waterimg" class="input" value='.@$this->config["waterimg"].' /></td>
				</tr>
			</table>
			</fieldset>
				<div class="form-submit">
			<input type="submit" value="保存更改" class="btn btn-primary btn-sm"/>
			</div>
		</form>';
		return $water;
	}

public function setting_update(){
		if(!$this->check_access())exit("You don't have permission to access!");
		$array=array();
		$array['waterimg']=empty($_POST['waterimg'])?'':addslashes(trim($_POST['waterimg']));
		
		$app_key=base64_encode(serialize($array));
		$this->update("apps",array('app_key'=>$app_key),"app_name='water'");
		alert('保存成功!');
		redirect(PATH.'?app=set&action=control&do=setting');
	}

  public function install(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$this->query("insert into apps set app_name='water',app_key='YToxOntzOjg6IndhdGVyaW1nIjtzOjg6ImxvZ28ucG5nIjt9'");
	}
	public function uninstall(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$this->query("delete from apps where app_name='water'");
	}

}
?> 