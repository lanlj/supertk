<?php
!defined('DROP') && exit('REFUSED!');
# ================================================================
# 用户||操作
# @core     Er8d.com
# @author   Drop
# @update   2015.3.4
# @notice   您只能在不用于商业目的的前提下对程序代码进行修改和使用
# ================================================================
class user extends Core{


	public function signup(){
		$this->template->out('signup.php');
	}
	public function foget(){

		$this->template->out('fogetpass.php');
	}
	public function findpass(){
	$array = explode('.',base64_decode(@$_GET['p']));
	$data = $this->value("user","user_pass","user_name='$array[0]'");
	$checkCode = md5($array['0'].'+').$data;
	if($array['1']==$checkCode){
	$this->template->in('pkey',$_GET['p']);
	$this->template->out('findpass.php');
	}else{alert('链接已失效','index.php');}
	}
    public function fogeter(){
	$foget_name=empty($_POST['user']) ? '':trim(addslashes($_POST['user']));
	$foget_email=empty($_POST['email']) ? '':trim(addslashes($_POST['email']));

	if(!preg_match('/^[\x{4e00}-\x{9fa5}_a-zA-Z0-9]+$/u', $foget_name)){//昵称不能包含特殊字符
		exit('ERROR:NAMEFORMAT');
		}
		if(empty($foget_name)){
			exit('ERROR:EMAIL');
		}
		if(!is_email($foget_email)){
			exit('ERROR:FEMAIL');
		}
		if($this->row("SELECT * FROM user WHERE user_email='".$foget_email."' and user_name='".$foget_name."'"))
		{
			$smtp = array('host'=>@$this->config["ssmtp"], 'port'=>@$this->config["sport"], 'user'=>@$this->config["suser"], 'pass'=>@$this->config["spass"], 'email'=>@$this->config["suser"]);
			$subject ='重置密码';
			$password = $this->value("user","user_pass","user_name='$foget_name'");
			$x = md5($foget_name.'+').$password;
			$string = base64_encode($foget_name.".".$x);
		    $url='http://'.$_SERVER['HTTP_HOST'].PATH.'?app=user&action=findpass&p='.$string;
			$message = '尊敬的用户'.$foget_name.':<br/>你使用了本站提供的密码找回功能，如果你确认此密码找回功能是你启用的，请点击下面的链接，按流程进行密码重设。<br/><a href="'.$url.'">'.$url.'</a><br/>如果不能打开链接，请复制链接到浏览器中。<br/>如果本次密码重设请求不是由你发起，你可以安全地忽略本邮件。';
		  if(new_mail::send($smtp,$this->config['sitename'],$foget_email,$subject,$message)=='ok')
		   {
		   
			exit('ERROR:scus');
			
			}else{
			
			exit('ERROR:ERROR');
			
			}
			
		}
		else{
		exit('ERROR:No');
		}
	}
	
	public function find(){
	$user_p = explode('.',base64_decode($_POST['pkey']));
	$foget_pass=empty($_POST['newpass'])?'':compile_password($_POST['newpass'],KEY);
	$foget_pass_confirm=empty($_POST['newpassagan'])?'':compile_password($_POST['newpassagan'],KEY);
	if(empty($foget_pass)||strlen($_POST['newpassagan'])<6){
		exit('ERROR:PASS');
	}
	if($foget_pass!==$foget_pass_confirm){
		exit('ERROR:UNPASS');
	}
	if($this->update("user","user_pass='$foget_pass'","user_name IN('$user_p[0]')")){
	exit('ERROR:scus');
	}else{exit('ERROR:ERROR');}
	}

	
	public function register(){

		$user_name=empty($_POST['name'])?'':trim(addslashes($_POST['name']));
		$user_pass=empty($_POST['pass'])?'':compile_password($_POST['pass'],KEY);
		$user_pass_confirm=empty($_POST['pass_confirm'])?'':compile_password($_POST['pass_confirm'],KEY);
		$user_email=empty($_POST['email'])?'':trim(addslashes($_POST['email']));
		$user_login_time=$_SERVER['REQUEST_TIME'];
		$user_city=addslashes(getCityTaobao(getIp()));
		if(empty($user_email)){
			exit('ERROR:EMAIL');
		}
		if(!is_email($user_email)){//Email格式不对
			exit('ERROR:EMAILX');
		}
		if($this->repeat("user",'user_email',$user_email)){//email已存在
			exit('ERROR:EMAILED');
		}
		if(empty($user_name)){//昵称不能为空
			exit('ERROR:NAME');
		}
		if(!preg_match('/^[\x{4e00}-\x{9fa5}_a-zA-Z0-9]+$/u', $user_name)){//昵称不能包含特殊字符
			exit('ERROR:NAMEFORMAT');
		}
		if($this->repeat("user",'user_name',$user_name)){//昵称已存在
			exit('ERROR:NAMEED');
		}
		if(empty($user_pass)||strlen($_POST['pass'])<6){
			exit('ERROR:PASS');
		}
		if($user_pass!==$user_pass_confirm){
			exit('ERROR:UNPASS');
		}
		$this->query("insert into user set user_name='$user_name',user_pass='$user_pass',user_email='$user_email',user_login_time='$user_login_time',user_city='$user_city'"); 
		$user_id=$this->id();#获取新注册会员的id
		@setcookie("token",$this->authstr($user_id,'ENCODE'),time()+3600*24*365,PATH);
	}
	public function login(){

		$user_email=empty($_POST['email'])?'':trim(addslashes($_POST['email']));
		$user_pass=empty($_POST['pass'])?'':compile_password($_POST['pass'],KEY);
		if(empty($user_email))exit('ERROR:EMAIL');
		if(empty($user_pass))exit('ERROR:PASS');
		if(is_email($user_email)){
			$sql="SELECT * FROM user WHERE user_email='".$user_email."' and user_pass='".$user_pass."'";
		}else{
			$sql="SELECT * FROM user WHERE user_name='".$user_email."' and user_pass='".$user_pass."'";
		}
		$row=$this->row($sql);
		if($row){	
			@setcookie("user_email",$row['user_email'],time()+3600*24*365);
			@setcookie("token",$this->authstr($row['user_id'],'ENCODE'),time()+3600*24*365,PATH);
			$user_login_time=$_SERVER['REQUEST_TIME'];
			if(is_email($user_email)){
			$this->update("user","user_login_time='$user_login_time'","user_email IN('$user_email')");

		    }else{
		    $this->update("user","user_login_time='$user_login_time'","user_name IN('$user_email')");
		    }
		        }
			else{
			exit('ERROR:EMAILERROR');
		}
	}
	public function qq(){

		$_SESSION['qq_appid']=$this->config['qq_appid']; 
		$_SESSION['qq_appkey']=$this->config['qq_appkey']; 
		$_SESSION['qq_callback']="http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?app=user&action=qqcallback"; 
		$_SESSION['qq_scope']= "get_user_info,add_t,del_t,add_pic_t,get_repost_list";
	    $_SESSION['qq_state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
	    $url="https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=".$_SESSION['qq_appid']."&redirect_uri=".urlencode($_SESSION['qq_callback'])."&state=".$_SESSION['qq_state']."&scope=".$_SESSION['qq_scope'];
	    header("Location:$url");
	    exit;
	}
	public function qqcallback(){

		#验证state防止CSRF攻击
	    if($_GET['state']!= $_SESSION['qq_state']){
	    	exit('Access denied!');
	    }
	    #请求访问
	    $data = file_get_contents("https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=".$_SESSION['qq_appid']."&redirect_uri=".urlencode($_SESSION["qq_callback"])."&client_secret=".$_SESSION["qq_appkey"]."&code=".$_REQUEST["code"]);

	    if (strpos($data, "callback") !== false){
	        $lpos = strpos($data, "(");
	        $rpos = strrpos($data, ")");
	        $data  = substr($data, $lpos + 1, $rpos - $lpos -1);
	        $msg = json_decode($data);
	        if (isset($msg->error)){
	            echo "<h3>error:</h3>" . $msg->error;
	            echo "<h3>msg  :</h3>" . $msg->error_description;
	            exit;
	        }
	    }
		$params = array();
		parse_str($data, $params);
	   	if(isset($params["access_token"])){#获取OPENID
		    $data  = file_get_contents("https://graph.qq.com/oauth2.0/me?access_token=".$params["access_token"]);
		    if (strpos($data, "callback") !== false){
		        $lpos = strpos($data, "(");
		        $rpos = strrpos($data, ")");
		        $data  = substr($data, $lpos + 1, $rpos - $lpos -1);
		    }
		    $user = json_decode($data);
		    if (isset($user->error)){
		        echo "<h3>error:</h3>" . $user->error;
		        echo "<h3>msg  :</h3>" . $user->error_description;
		        exit;
		    }
	    	$_SESSION["qq_token"] = $user->openid;
	    	if(isset($user->openid)){#获取QQ信息
			    $data = file_get_contents("https://graph.qq.com/user/get_user_info?access_token=".$params["access_token"]."&oauth_consumer_key=" . $_SESSION["qq_appid"]. "&openid=" .$user->openid."&format=json");
			    $json = json_decode($data, true);
			    $_SESSION["qq_nickname"] = $json['nickname'];
			    $_SESSION["qq_avatar"] = $json['figureurl_qq_2'];
			    $_SESSION["qq_sex"] = $json['gender'];
	    	}
		}

	    if(!empty($_SESSION['qq_token'])){
	    	$user_login_time=$_SERVER['REQUEST_TIME'];
	    	$user_login_city=addslashes(getCityTaobao(getIp()));
	    	$row=$this->row("SELECT * FROM user WHERE qq_token='".$_SESSION['qq_token']."' LIMIT 1");
	    	if($row){
				@setcookie("token",$this->authstr($row['user_id'],'ENCODE'),time()+3600*24*365,PATH);
				redirect(PATH);
			}else{
				$user_avatar=$_SESSION["qq_avatar"];
				$user_sex=$_SESSION["qq_sex"];
				$user_name=$_SESSION["qq_nickname"];
				$qq_token=$_SESSION['qq_token'];
				$this->query("insert into user set user_name='$user_name',user_sex='$user_sex',user_login_time='$user_login_time',user_city='$user_login_city',qq_token='$qq_token'"); 
				$user_id=$this->id();#获取新注册会员的id
				@setcookie("token",$this->authstr($user_id,'ENCODE'),time()+3600*24*365,PATH);
				@file_put_contents("upload/user/".$user_id.".jpg",@file_get_contents($user_avatar));
				$newavatar="/upload/user/".$user_id.".jpg";
				$this->query("update user set user_avatar='$newavatar' where user_id='$user_id'");
				redirect(PATH);
			}
	    }else{
	    	exit('Access denied!');
	    }  
	    exit;
	}
	public function weibo(){
		$weibo=new SaeTOAuthV2($this->config['weibo_appid'],$this->config['weibo_appkey']);
		$_SESSION['REFERER']=$_SERVER['HTTP_REFERER'];
		$url = $weibo->getAuthorizeURL($this->config['weibo_callback']);
	    header("Location:$url");
	    exit;
	}
	public function weibocallback(){

		if (isset($_REQUEST['code'])) {
			$weibo=new SaeTOAuthV2($this->config['weibo_appid'],$this->config['weibo_appkey']);
			$keys = array();
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] =$this->config['weibo_callback'];
			$token = $weibo->getAccessToken('code',$keys);
			if(!empty($token['access_token'])){
				$_SESSION['weibo_token']=$token['access_token'];
				$c = new SaeTClientV2($this->config['weibo_appid'],$this->config['weibo_appkey'],$token['access_token']);
				$ms  = $c->home_timeline();
				$uid_get = $c->get_uid();
				$uid = $uid_get['uid'];
				$info=$c->show_user_by_id($uid);
				$user_login_time=$_SERVER['REQUEST_TIME'];
				$user_login_city=addslashes(getCityTaobao(getIp()));
	            $row=$this->row("SELECT * FROM user WHERE weibo_token='".$token['access_token']."' LIMIT 1");
		    	if($row){
					$_SESSION['user_id']=$row['user_id'];
					$_SESSION['user_name']=$row['user_name'];
					$_SESSION['user_email']=$row['user_email'];
					$_SESSION['user_avatar']=$row['user_avatar'];
					redirect(PATH);
				}else{
					$user_avatar=$info['avatar_large'];
					$user_sex=$info['gender'];
					if($user_sex=='m'){
						$user_sex="男";
					}elseif($user_sex=='f'){
						$user_sex="女";
					}else{
						$user_sex="";
					}
					$user_name=$info['name'];
					$user_sign=$info['description'];
					$weibo_token=$token['access_token'];
					$this->query("insert into user set user_name='$user_name',user_avatar='$user_avatar',user_sex='$user_sex',user_sign='$user_sign',user_login_time='$user_login_time',user_city='$user_login_city',weibo_token='$weibo_token'"); 
					$ins_id=$this->id();#获取新注册会员的id
					$_SESSION['user_id']=$ins_id;
					$_SESSION['user_name']=$user_name;
					$_SESSION['user_avatar']=$user_avatar;
					$_SESSION['user_email']='';
					@file_put_contents("upload/user/".$_SESSION['user_id'].".jpg",@file_get_contents($user_avatar));
					$newavatar="/upload/user/".$_SESSION['user_id'].".jpg";
					$this->query("update user set user_avatar='$newavatar' where user_id='$ins_id'");
					redirect(PATH);
				}
			}
		}
		exit;
	}
	public function instagram(){
		$auth='https://api.instagram.com/oauth/authorize/?client_id=%s&redirect_uri=%s&response_type=code';
		header("Location: ".sprintf($auth,$this->config['ins_appid'],$this->config['ins_callback']));
		exit;
	}
	public function inscallback(){

		$appconfig = array(		
		'client_id' 	=> $this->config['ins_appid'],
		'client_secret' => $this->config['ins_appkey'],
		'redirect_url' 	=> $this->config['ins_callback'],
		'scope'         => 'comments+relationships+likes',
		);

		$instagram = new Instagram($appconfig);

		if(isset($_GET['error'])&&$_GET['error']!="") {			
			echo "You Need to grant access to the application in order to continue!";
			exit;	
		}

		if(isset($_GET['code'])&&$_GET['code']!="") {
			$accesstoken = $instagram->getAccessTokenFromCode($_GET['code']);

			if($accesstoken!="") {
				$_SESSION['insId'] = $instagram->getUserId();
				$_SESSION['ins_Token'] = $accesstoken;
				$user_login_time=$_SERVER['REQUEST_TIME'];
				$row=$this->row("SELECT * FROM user WHERE ins_token='".$accesstoken."' LIMIT 1");
				if($row){
					$_SESSION['user_id']=$row['user_id'];
					$_SESSION['user_name']=$row['user_name'];
					$_SESSION['user_avatar']=$row['user_avatar'];
					$this->query("update user set user_login_time='$user_login_time' where user_id='".$row['user_id']."'");
					redirect(PATH);
				}else{
					$user_avatar=$instagram->getUserThumb();
					$user_name=$instagram->getUserFullName();
					$ins_token=$accesstoken;
					$this->query("insert into user set user_name='$user_name',user_avatar='$user_avatar',user_login_time='$user_login_time',ins_token='$ins_token'"); 
					$ins_id=$this->id();
					$_SESSION['user_id']=$ins_id;
					$_SESSION['user_name']=$user_name;
					$_SESSION['user_avatar']=$user_avatar;
					@file_put_contents("upload/user/".$_SESSION['user_id'].".jpg",@file_get_contents($_SESSION['user_avatar']));
					$newavatar="upload/user/".$_SESSION['user_id'].".jpg";
					$this->query("update user set user_avatar='$newavatar' where user_id='$ins_id'");
					redirect(PATH);
				}
				exit;
			}			
		}
	}
	public function avatar(){
		if(!is_login())exit('ERROR:LOGIN');
		
		$file=upload($_FILES['file'],'upload/files/','jpg',1);
		if(strpos($file,"jpg")===false&&strpos($file,"php")!==false){
			echo("ERROR:FILE");
			exit;
		}
		ImgReduce(ROOT."upload/files/".$file,200,200,2);
		if(copy(ROOT."upload/files/".$file,ROOT."upload/user/".$_SESSION['user_id'].".jpg")){
			@unlink(ROOT."upload/files/".$file);
		}
		$newavatar="upload/user/".$_SESSION['user_id'].".jpg";
		$user_id=$_SESSION['user_id'];
		$this->query("update user set user_avatar='$newavatar' where user_id='$user_id'");
		$_SESSION['user_avatar']=$newavatar;
		echo json_encode(array("result"=>"success","message"=>$newavatar));
	}
	public function setbg(){
		if(!is_login())exit('ERROR:LOGIN');
		$user_id=$_SESSION['user_id'];
		$user_bg=$this->value("user","user_bg","user_id=$user_id");
		if($user_bg!=''){
			@unlink(ROOT."upload/images/".$user_bg);
			$file=upload($_FILES['file'],'upload/images/','jpg',2);
			if(strpos($file,"jpg")===false&&strpos($file,"php")!==false){
				echo("ERROR:FILE");
				exit;
			}
			$this->query("update user set user_bg='$file' where user_id='$user_id'");
			echo json_encode(array("result"=>"success","message"=>$file));
		}else{
			$file=upload($_FILES['file'],'upload/images/','jpg',2);
			if(strpos($file,"jpg")===false&&strpos($file,"php")!==false){
				echo("ERROR:FILE");
				exit;
			}
			$this->query("update user set user_bg='$file' where user_id='$user_id'");
			echo json_encode(array("result"=>"success","message"=>$file));
		}
	}
	public function logout(){
		unset($_SESSION['user_id'],$_SESSION['user_email'],$_SESSION['user_name'],$_SESSION["user_avatar"],$_SESSION['weibo_token'],$_SESSION['ins_Token']);
		@setcookie('token',"",time()-3600,PATH);
		redirect(PATH);
	}
	public function admin_user(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$mode="会员列表";

		$prefix_id="SELECT user_id";
		$prefix_count="SELECT count(user_id)";
		$prefix_result="SELECT *";
		$sql=" FROM user WHERE 1=1";
		if(isset($_GET['keyword'])){
			$sql.=" AND user_name like '%".trim($_GET['keyword'])."%'";
		}
		if(isset($_GET['status'])){
			$sql.=" AND user_status=".intval($_GET['status']);
		}
		if(!empty($_GET['orderby'])){
			$orderby=trim($_GET['orderby']);
		}else{
			$orderby='user_id';
		}
		if(!empty($_GET['sort'])){
			$sort=trim($_GET['sort']);
		}else{
			$sort='DESC';
		}
		$orderby=" ORDER BY $orderby $sort";
		$page_size=25;
		$page_current=isset($_GET['page'])&&is_numeric($_GET['page'])?intval($_GET['page']):1;
		$limit=" limit ".(($page_current-1)*$page_size).",".$page_size;
		$count=$this->count($prefix_count.$sql,false);
		#获取ID
		$ids=$this->result($prefix_id.$sql.$orderby.$limit);
		if(!empty($ids)){
			$ids2=array();
			foreach($ids as $v){
				$ids2[]=$v['user_id'];
			}
			$sql.=" AND user_id IN (".implode(",",$ids2).")";
		}
		$array=array();
		if($count>0){
			// echo $prefix_result.$sql.$orderby.$limit;
			$result=$this->result($prefix_result.$sql.$orderby." LIMIT $page_size");
			foreach($result as $row){
				$array[$row['user_id']]['id']=$row['user_id'];
				$array[$row['user_id']]['name']=$row['user_name'];
				$array[$row['user_id']]['avatar']=$row['user_avatar'];
				$array[$row['user_id']]['city']=$row['user_city'];
				$array[$row['user_id']]['email']=$row['user_email'];
				$array[$row['user_id']]['time']=format_time($row['user_login_time']);
				$array[$row['user_id']]['status']=$row['user_status'];
			}
			$parameter='admin_user';
			$pager=$this->pager('?app=user&action=admin_user',$parameter,$page_current,$page_size,$count,2);
			$this->template->in('pager',$pager);
		}
		$this->template->in('mode',$mode);
		$this->template->in('user',$array);
		$this->template->out('admin.user.php');
	}
	public function update_user(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$user_id=intval($_GET['user_id']);
		if($user_id == 1){alert("无法对初始用户操作");}else{
		if(intval($_GET['status'])==3){
			$this->update("user","user_status=3","user_id IN($user_id)");
			alert('已升级为管理员');
		}elseif(intval($_GET['status'])==2){
			$this->update("user","user_status=2","user_id IN($user_id)");
			alert('禁言成功！');
		}elseif(intval($_GET['status'])==0){
			$this->update("user","user_status=0","user_id IN($user_id)");
			alert('已为普通会员');
		}
	}}
	public function delete_user(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$user_id=empty($_POST['user_id'])?array():$_POST['user_id'];
		if(!empty($user_id)){
			foreach($user_id as $id){
				if(!empty($id)){
				if($id != 1){
					$row=$this->row("SELECT user_avatar,user_bg FROM user WHERE user_id =$id");
					if($row['user_avatar']!='')unlink("upload/user/".$id.".jpg");
					if($row['user_bg']!='')unlink("upload/images/".$row['user_bg']);
					$this->del("user","user_id=".$id."");
					$this->del("tweet","user_id=".$id."");
				}}
			}
		}
		alert('会员删除完毕！');
	}
	public function setting(){
		$userSetting='
		<form action="'.PATH.'?app=user&action=setting_update" method="post">
			<h4>会员设置</h4>
			<fieldset>
			<legend>QQ登陆设置</legend>
			<table cellspacing="10" class="form-table">
				<tr>
				<td class="input-name">APP ID：</td>
				<td><input type="text" size="50" name="qq_appid" value="'.@$this->config['qq_appid'].'" class="input"/></td>
				</tr>
				<tr>
				<td class="input-name">APP KEY：</td>
				<td><input type="text" size="50" name="qq_appkey" value="'.@$this->config['qq_appkey'].'" class="input"/></td>
				</tr>
			</table>
			</fieldset>
			<fieldset>
			<legend>微博登陆设置</legend>
			<table cellspacing="10" class="form-table">
				<tr>
				<td class="input-name">启用：</td>
				<td><label><input type="checkbox" name="weibo" size="15" value="1"';
			if(@$this->config['weibo']==1)$userSetting.='checked="checked"';$userSetting.='/> 启用</lable>
				</td>
				</tr>
				<tr>
				<td class="input-name">WEIBO ID：</td>
				<td><input type="text" size="50" name="weibo_appid" value="'.@$this->config['weibo_appid'].'" class="input"/></td>
				</tr>
				<tr>
				<td class="input-name">WEIBO KEY：</td>
				<td><input type="text" size="50" name="weibo_appkey" value="'.@$this->config['weibo_appkey'].'" class="input"/></td>
				</tr>
				<tr>
				<td class="input-name">回调地址：</td>
				<td><input type="text" size="50" name="weibo_callback" value="'.@$this->config['weibo_callback'].'" class="input"/></td>
				</tr>
			</table>
			</fieldset>
			<fieldset>
			<legend>Instagram登陆设置</legend>
			<table cellspacing="10" class="form-table">
				<tr>
				<td class="input-name">启用：</td>
				<td><label><input type="checkbox" name="ins" size="15" value="1"';
			if(@$this->config['ins']==1)$userSetting.='checked="checked"';$userSetting.='/> 启用</lable>
				</td>
				</tr>
				<tr>
				<td class="input-name">INS ID：</td>
				<td><input type="text" size="50" name="ins_appid" value="'.@$this->config['ins_appid'].'" class="input"/></td>
				</tr>
				<tr>
				<td class="input-name">INS KEY：</td>
				<td><input type="text" size="50" name="ins_appkey" value="'.@$this->config['ins_appkey'].'" class="input"/></td>
				</tr>
				<tr>
				<td class="input-name">回调地址：</td>
				<td><input type="text" size="50" name="ins_callback" value="'.@$this->config['ins_callback'].'" class="input"/></td>
				</tr>
			</table>
			</fieldset>
			<div class="form-submit">
			<input type="submit" value="保存更改" class="btn btn-primary btn-sm"/>
			</div>
		</form>';
		return $userSetting;
	}
	public function setting_update(){
		if(!$this->check_access())exit("You don't have permission to access!");
		$array=array();
		$array['qq_appid']=empty($_POST['qq_appid'])?'':addslashes(trim($_POST['qq_appid']));
		$array['qq_appkey']=empty($_POST['qq_appkey'])?'':addslashes(trim($_POST['qq_appkey']));
		$array['weibo']=empty($_POST['weibo'])?0:intval($_POST['weibo']);
		$array['weibo_appid']=empty($_POST['weibo_appid'])?'':addslashes(trim($_POST['weibo_appid']));
		$array['weibo_appkey']=empty($_POST['weibo_appkey'])?'':addslashes(trim($_POST['weibo_appkey']));
		$array['weibo_callback']=empty($_POST['weibo_callback'])?'':addslashes(trim($_POST['weibo_callback']));
		$array['ins']=empty($_POST['ins'])?0:intval($_POST['ins']);
		$array['ins_appid']=empty($_POST['ins_appid'])?'':addslashes(trim($_POST['ins_appid']));
		$array['ins_appkey']=empty($_POST['ins_appkey'])?'':addslashes(trim($_POST['ins_appkey']));
		$array['ins_callback']=empty($_POST['ins_callback'])?'':addslashes(trim($_POST['ins_callback']));
		$app_key=base64_encode(serialize($array));
		$this->update("apps",array('app_key'=>$app_key),"app_name='user'");
		alert('保存成功!');
		redirect(PATH.'?app=set&action=control&do=setting');
	}
	public function install(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$this->query("insert into apps set app_name='user',app_key=''");
	}
	public function uninstall(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$this->query("delete from apps where app_name='user'");
	}
	private function autoTAG($str){
		return preg_replace("/#([^#^\\s^:]{1,})([\\s\\:\\,\\;]{0,1})/",'<a href="/tag/\\1\\3/">#\\1\\3</a>'." ", $str." ");
	}
}
?>