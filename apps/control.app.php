<?php 
!defined('DROP') && exit('REFUSED!');
class control extends Core{

	public function set(){
		if(!is_login())http_404();

		$user_id=isset($_SESSION['user_id'])?$_SESSION['user_id']:exit("LOGIN");
		$row=$this->row("SELECT * FROM user WHERE user_id='$user_id'");
		$array=array();
		$array['sex']=$row['user_sex'];
		$array['sign']=$row['user_sign'];
		$array['old']=$row['user_old'];
		$this->template->in('user',$array);

		if($this->do=='service'){

			if(!$this->check_access())exit("You don't have permission to access!");
			$mode="服务器信息";

			$server=array();
			$server['port']=$_SERVER['SERVER_PORT'];
			$server['os']=@PHP_OS;
			$server['version']=@PHP_VERSION;
			$server['root']=$_SERVER['DOCUMENT_ROOT'];
			$server['name']=$_SERVER['SERVER_NAME'];
			$server['upload']=@ini_get('upload_max_filesize');
			$session_timeout=@ini_get('session.gc_maxlifetime');
			$server['timeout']=$session_timeout?$session_timeout/60:'未知';
			$server['memory_usage']=format_size(memory_get_usage());
			$server['disable_functions']=@ini_get('disable_functions');
			if(strpos(strtolower($_SERVER['SERVER_SOFTWARE']),'apache')!==false){
				$server['software']='Apache';
			}elseif(strpos(strtolower($_SERVER['SERVER_SOFTWARE']),'nginx')!==false){
				$server['software']='Nginx';	
			}else{
				$server['software']='Other';
			}
		
			$this->template->in('server',$server);
		
		}elseif($this->do=='avatar'){
			$mode='上传头像';
		
		}elseif($this->do=='userbg'){
			$mode='上传封面';
		
		}elseif($this->do=='password'){
		
			$mode='修改密码';

			if(isset($_GET['insert'])&&$_GET['insert']=='1'){
		
				$user_pass=empty($_POST['pass'])?'':compile_password($_POST['pass'],KEY);
				$user_pass_confirm=empty($_POST['pass_confirm'])?'':compile_password($_POST['pass_confirm'],KEY);
				if(empty($user_pass)||strlen($user_pass)<6){
					alert("密码太短了~");
				}
				if($user_pass!==$user_pass_confirm){
					alert("两次输入的密码不一致！");
				}
				$this->update("user","user_pass='$user_pass'","user_id=".$user_id);
				alert('修改成功！',PATH.'?app=control&action=set&do=password');
			}
		
		}elseif($this->do=='profile'){
		
			$mode='个人资料';
		
			if(isset($_GET['insert'])&&$_GET['insert']=='1'){
		
				$user_sex=empty($_POST['user_sex'])?'':trim(addslashes($_POST['user_sex']));
				$user_sign=empty($_POST['user_sign'])?'':trim(addslashes(htmlspecialchars($_POST['user_sign'])));
				$user_old=empty($_POST['user_old'])?0:trim(addslashes(htmlspecialchars($_POST['user_old'])));
				if($user_sex=='')alert('性别不能为空哦~');
				if(empty($_SESSION['user_email'])){
					$user_email=empty($_POST['user_email'])?'':trim(addslashes($_POST['user_email']));
					if($user_email=='')alert('邮箱不能为空哦~');
					if(!is_email($user_email))alert('邮箱格式不正确哦~');
					if($this->repeat("user",'user_email',$user_email))alert('邮箱已存在哦~');
				}else{
					$user_email=$_SESSION['user_email'];
				}
				$this->query("update user set user_email='$user_email',user_sex='$user_sex',user_sign='$user_sign',user_old='$user_old' where user_id='$user_id'");
				$_SESSION['user_email']=$user_email;
				alert('修改成功！',PATH.'?app=control&action=set&do=profile');
			}
		
		}elseif($this->do=='nickname'){
		
			$mode='修改昵称';
		
			if(isset($_GET['insert'])&&$_GET['insert']=='1'){
				$user_name=empty($_POST['user_name'])?'':trim(addslashes($_POST['user_name']));
				if($user_name=='')alert('昵称不能为空哦~');
				if($this->repeat("user",'user_name',$user_name))alert('昵称被占用了哦~');
				if(!preg_match('/^(?!_|\s\')(?!.*?_$)[A-Za-z0-9_\x{4e00}-\x{9fa5}\s\']+$/u', $user_name))alert('昵称不能含有特殊符号哦~');
				$this->query("update user set user_name='$user_name' where user_id='$user_id'");
				$_SESSION['user_name']=$user_name;
				alert('修改成功！',PATH.'?app=control&action=set&do=nickname');
			}
		
		}elseif($this->do=='linkadd'){
		
			if(!$this->check_access())exit("You don't have permission to access!");
			$mode='添加链接';
			if(isset($_GET['insert'])&&$_GET['insert']=='1'){
				$link_id=empty($_POST['link_id'])?0:intval($_POST['link_id']);
				$link_name=empty($_POST['link_name'])?'':trim(addslashes($_POST['link_name']));
				$link_url=empty($_POST['link_url'])?'':trim(addslashes($_POST['link_url']));
				$link_text=empty($_POST['link_text'])?'':trim(addslashes($_POST['link_text']));
				if($link_name=='')alert('链接名称不能为空');
				if($link_url=='')alert('链接地址不能为空');
				$this->query("insert into link set link_name='$link_name',link_url='$link_url',link_text='$link_text'");
				alert('链接添加成功！',PATH.'?app=control&action=set&do=link');
			}
		
		}elseif($this->do=='linkedit'){
			
			if(!$this->check_access())exit("You don't have permission to access!");

			$mode='编辑链接';
			$link_id=empty($_GET['linkid'])?0:intval($_GET['linkid']);
			$row=$this->row("SELECT * FROM link WHERE link_id =$link_id");
			$array=array();
			$array['id']=$row['link_id'];
			$array['name']=$row['link_name'];
			$array['url']=$row['link_url'];
			$array['text']=$row['link_text'];
			if(isset($_GET['insert'])&&$_GET['insert']=='1'){
				$link_id=intval($_POST['link_id']);
				$link_name=empty($_POST['link_name'])?'':trim(addslashes($_POST['link_name']));
				$link_url=empty($_POST['link_url'])?'':trim(addslashes($_POST['link_url']));
				$link_text=empty($_POST['link_text'])?'':trim(addslashes($_POST['link_text']));
				if($link_name=='')$this->alert('链接名称不能为空');
				if($link_url=='')$this->alert('链接地址不能为空');
				$this->query("update link set link_name='$link_name',link_url='$link_url',link_text='$link_text' where link_id='$link_id'");
				alert('链接编辑成功！',PATH.'?app=control&action=set&do=link');
			}
			$this->template->in('link',$array);
		
		}elseif($this->do=='link'){
			if(!$this->check_access())exit("You don't have permission to access!");
			$mode="链接列表";
			$this->links();
		
		}elseif($this->do=='linkdel'){
			if(!$this->check_access())http_404();
			$link_id = intval($_GET['linkid']); 
			$this->query("delete from link where link_id=".$link_id);
			alert('删除成功！');			
		}elseif($this->do=='apps'){

			if(!$this->check_access())exit("You don't have permission to access!");
			$mode="应用列表";
			$array=array();
			if($handle=opendir("apps/")){
				while(false!==($dir=readdir($handle))){
					if (strpos($dir,'.info.php')!==false){
						$info=@include(ROOT.'apps/'.$dir);
						if(!empty($info)){
							$info['info']['install']=in_array($info['app'],$this->getApps());
							$info['info']['app']=$info['app'];
							$array[]=$info['info'];
						}
					}
				}
				closedir($handle);
			}
			$this->template->in("apps",$array);

		}elseif($this->do=='setting'){

			if(!$this->check_access())exit("You don't have permission to access!");
			$mode="通用设置";
			$array=array();
			$apps=$this->getApps();
			if($apps){
				foreach ($apps as $app) {
					$file=APPS_DIR.$app.'.app.php';
					if(is_file($file))include($file);
					if(class_exists($app)){
						$r=new $app();
						if(method_exists($r,'setting'))$array[]=$r->setting();
					}
				}
				$this->template->in("setting",$array);
			}
		}
		$this->template->in('mode',$mode);
		$this->template->out('control.php');
	}
	public function app_install(){
		if(!$this->check_access())exit("You don't have permission to access!");
		$app=empty($_GET['app_name'])?'':trim($_GET['app_name']);
		$apps=$this->getApps();
		$apps[]=$app;
		$apps=implode("|",$apps);
		$file=APPS_DIR.$app.'.app.php';
	 	if(is_file($file)){include($file);}else{alert('核心文件不存在！');}
		$this->update("apps","app_key='".$apps."'","app_name='apps'");
		$this->cache->delete_cache('apps');
		$run=new $app();
		if(method_exists($run,'install'))$run->install();
		alert('安装成功！');
	}
	public function app_uninstall(){
		if(!$this->check_access())exit("You don't have permission to access!");		
		$app=empty($_GET['app_name'])?'':trim($_GET['app_name']);
	 	$apps=$this->getApps();
	 	$apps=array_diff($apps,array($app));
	 	$apps=implode("|",$apps);
	 	$this->update("apps","app_key='".$apps."'","app_name='apps'");
		$file=APPS_DIR.$app.'.app.php';
		$this->cache->delete_cache('apps');
		if(is_file($file))include($file);
		$run=new $app();
		if(method_exists($run,'uninstall'))$run->uninstall();
		alert('应用已卸载！');
	}
	public function clear(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$this->cache->clear();
		alert('缓存已清空！');
	}
	public function clearfile(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		clear_files();
		alert('垃圾已清空！');
	}
}
?>