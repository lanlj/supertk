<?php
!defined('DROP') && exit('REFUSED!');
class tweet extends Core{

	private $TweetCount;

	private $Timeline;

	public function index(){

		$page_size=isset($this->config['index_size'])?$this->config['index_size']:20;

		$page_current=intval(isset($_GET['p']) ? $_GET['p'] :1);

		$this->template->in('tweet',$this->getTweetList($page_size,$page_current));

		$this->template->in('pager',$this->pager('page','',$page_current,$page_size,$this->TweetCount));

		$this->template->in('page_current',$page_current);

		if(isset($_GET['more'])) :

			$this->template->out('more.php');

		else :

			$hots=$this->cache->read('hots',720);
			
			#近期喜欢查询数据结果写缓存12个小时
			
			if(empty($hots)){
				
				// get_hots() true:显示缩图 false:不显示缩图
				$hots=$this->get_hots(true);
			
				$this->cache->write('hots',$hots);
			}

			$tags=$this->cache->read('tags_list',720);
			
			#标签查询数据结果写缓存12个小时

			if(empty($tags)){
			
				$tags=$this->get_tags();
			
				$this->cache->write('tags_list',$tags);
			}
			$this->links();

			$this->userinfo();

			$this->template->in('tweet_counts',$this->TweetCount);

			$this->template->in('hots',$hots);

			$this->template->in('tags',$tags);

			$this->template->in('users',$this->get_users());

			$this->template->out('index.php');

		endif;
	}
	public function push(){

		$this->check();

		if(!is_login()){

			json(array('error'=>0));

		}
		if(empty($_POST['content'])){

			json(array('error'=>1));

		}
		if($this->check_user_access()){

			json(array('error'=>3));

		}

		$title=empty($_POST['title'])? '':trim(htmlspecialchars(addslashes($_POST['title'])));

		$content=$this->filter_html($_POST['content']);
	
		if(!empty($title)){

			$content=$this->ubb($content);

		}else{

			$content=$this->clear_text($content);
		}
		$time=$_SERVER['REQUEST_TIME'];
		$ip=getCityTaobao(getIp());
		$agent=addslashes($_SERVER['HTTP_USER_AGENT']);
		$file=empty($_POST['file'])?'':trim(addslashes($_POST['file']));
		$user_id=isset($_SESSION['user_id'])?$_SESSION['user_id']:'';

		$status=empty($_POST['status'])?0:intval($_POST['status']);

		$wb=empty($_POST['wb'])?0:intval($_POST['wb']);

		#检查是否包含垃圾关键字，包含则返回信息已屏蔽

		$unsafe=false;

		$unsafe_keywords=!empty($this->config['content_filter'])?explode(",",$this->config['content_filter']):array();

		if(count($unsafe_keywords)>1){

			foreach($unsafe_keywords as $keyword){

				if(strpos($content,$keyword)!==false){

					$unsafe=true;

					json(array('error'=>2));

					break;
				}
			}
		}
		if(!$unsafe){

			#更新TAG库

		    $pattern = "/#([^#^\\s^:^<br\/>^!]{1,})([\\s\\:\\,\\;]{0,1})/";

		    preg_match_all ( $pattern, $content, $matches );

		    $matches [1] = array_unique($matches [1]);

			if($matches[1]){

			    foreach ( $matches [1] as $tag ) {

			        if ($tag){

			        	$row=$this->row("SELECT tag_id FROM tweet_tag WHERE tag_name='$tag'");

			            if($row){

			            	$tag_id=$row['tag_id'];

			            	$this->update("tweet_tag",'tag_count=tag_count+1',"tag_id=".$tag_id);

			            }else{

			            	$this->insert("tweet_tag",array('tag_name'=>$tag,'tag_count'=>1));

			            }

			        }

			    }

			    unset($row['tag_id']);

			}

			# 附件转换

			if($file!=''){

				$files=substr($file,0,-1);

				$images=explode(",",$file);

				$IM=array();

				foreach ($images as $key => $value) {

					@copy(ROOT."upload/files/".$value,ROOT."upload/images/".$value);

					@unlink(ROOT."upload/files/".$value);

					$IM['img'][$key]=$value;

					$IM['ext'][$key]=get_ext($value);

				}

			}else{

				$files='';

				$IM='';

			}

			#微博同步

			if($wb==1){

				$token=$_SESSION['weibo_token'];

				if($token){

					$c = new SaeTClientV2($this->config['weibo_appid'],$this->config['weibo_appkey'],$token);

					$text=strip_tags($content);

					if($images!=''){

						$c->upload($text,$this->path()."upload/images/".$images[0]);

					}else{

						$c->update($text);

					}			
					
				}

			}
			$this->query("INSERT INTO `tweet` (`title`,`content`,`file`,`ip`,`agent`,`status`,`lastdate`,`user_id`) VALUES ('$title','$content','$files','$ip','$agent','$status','$time','$user_id')");

			$new=$this->id();

			$newcontent=$this->notification($content,$new,$time,0);

			$this->query("update tweet set content='$newcontent' where tid=$new");

			$this->cache->delete_cache('tags_list');

			$row=$this->row("SELECT a.*,b.user_avatar,b.user_name FROM tweet AS a LEFT JOIN user AS b ON a.user_id=b.user_id WHERE a.tid=$new");

			$array= array(
				'id' => $row['tid'],
				'user_name' => $row['user_name'],
				'title'=>$row['title'],
				'content'=> $row['content'],
				'description'=> truncate(strip_tags($row['content']),20),
				'user_id'=> $row['user_id'],
				'user_avatar' =>$row['user_avatar'],
				'ip'=> $row['ip'],
				'agent'=> getOS($row['agent']),
				'tweet_num'=>$this->getTweetCount($_SESSION['user_id']),
				'album_num'=>$this->getAlbumCount($_SESSION['user_id']),
				'status'=>$row['status'],
				'images'=>$IM
				);

			json($array,true);

			unset($new);

		}

	}
	public function like(){

		if(!is_login())echo json_encode(array("result"=>"0","count"=>'login'));

		$tid = intval($_GET['tid']);

		if (isset($_SESSION['user_id'])){

			$zanuser=$_SESSION['user_id'];

			$iszan=$this->value("liked","COUNT(`user_id`)","tid=$tid AND user_id=$zanuser");

			$iszan=intval($iszan);

			if($iszan && $iszan > 0){

				$this->query("update tweet set hot=hot-1 where tid=$tid");

				$this->query("delete from liked where tid=$tid and user_id=$zanuser");

				$zan=$this->value("tweet","hot","tid=$tid LIMIT 0,1");
				
				echo json_encode(array("result"=>"1","count"=>number_format($zan)));

			}else{

				$this->query("update tweet set hot=hot+1 where tid=$tid");

				$this->query("insert into liked set tid=$tid,user_id=$zanuser");

				$zan=$this->value("tweet","hot","tid=$tid LIMIT 0,1");

				echo json_encode(array("result"=>"2","count"=>number_format($zan)));

			}
		}
	}

	public function delete(){
		if(!is_login())http_404();

		if(isPost()):

			$tid = empty($_POST['tid'])?array():$_POST['tid'];

			foreach ($tid as $id) {

				$row=$this->row("SELECT user_id,file,content FROM tweet WHERE tid=$id");

				$user_id=$row['user_id'];

				$file=$row['file'];

				$content=$row['content'];

				if ($_SESSION['user_id']==$user_id||$this->check_access()){

					if($file!=''){

						$images=explode(",",$file);

						foreach ($images as $key => $value) {

							@unlink(ROOT."upload/images/".$value);

						}				
					}

					#更新TAG库

				    $pattern = "/#([^#^\\s^:^<br\/>]{1,})([\\s\\:\\,\\;]{0,1})/";

				    preg_match_all ( $pattern, $content, $matches );

				    $matches [1] = array_unique($matches [1]);

					if($matches[1]){

					    foreach ( $matches [1] as $tag ) {

					        if ($tag){

					        	$row2=$this->row("SELECT tag_id,tag_count FROM tweet_tag WHERE tag_name='$tag'");

					            if($row){

					            	$tag_id=$row2['tag_id'];

					            	$tag_count=$row2['tag_count'];

					            	if($tag_count>1){

					            		$this->update("tweet_tag",'tag_count=tag_count-1',"tag_id=".$tag_id);

					            	}else{

					            		$this->query("delete from tweet_tag where tag_id=$tag_id");
					            	}
					            }

					            $this->cache->delete_cache('tags_list');
					        }
					    }

					    unset($row2['tag_id'],$row2['tag_count']);

					}

					$this->query("delete from tweet where tid=$id");

					$this->query("delete from reply where tid=$id");

					$this->query("delete from notification where tid=$id");

					alert('删除成功！');
				}
			}
		else :

			$tid = intval($_GET['tid']);

			$row=$this->row("SELECT user_id,file,content FROM tweet WHERE tid=$tid");

			$user_id=$row['user_id'];

			$file=$row['file'];

			$content=$row['content'];

			if ($_SESSION['user_id']==$user_id||$this->check_access()){

				if($file!=''){

					$images=explode(",",$file);

					foreach ($images as $key => $value) {

						@unlink(ROOT."upload/images/".$value);
					}				
				}

				#更新TAG库

			    $pattern = "/#([^#^\\s^:^<br\/>]{1,})([\\s\\:\\,\\;]{0,1})/";

			    preg_match_all ( $pattern, $content, $matches );

			    $matches [1] = array_unique($matches [1]);

				if($matches[1]){

				    foreach ( $matches [1] as $tag ) {

				        if ($tag){

				        	$row2=$this->row("SELECT tag_id,tag_count FROM tweet_tag WHERE tag_name='$tag'");

				            if($row){

				            	$tag_id=$row2['tag_id'];

				            	$tag_count=$row2['tag_count'];

				            	if($tag_count>1){

				            		$this->update("tweet_tag",'tag_count=tag_count-1',"tag_id=".$tag_id);

				            	}else{

				            		$this->query("delete from tweet_tag where tag_id=$tag_id");

				            	}

				            }

				            $this->cache->delete_cache('tags_list');

				        }

				    }

				    unset($row2['tag_id'],$row2['tag_count']);

				}

				$this->query("delete from tweet where tid=$tid");

				$this->query("delete from reply where tid=$tid");

				$this->query("delete from notification where tid=$tid");

			}

		endif;
	}

	public function delete_reply(){

		if(!is_login())http_404();

		$rid = intval($_GET['rid']);

		if (isset($_SESSION['user_id'])){

			$tid=$this->value("reply","tid","rid=$rid");

			$adminID=$this->value("tweet","user_id","tid=$tid");

			$user_id=$this->value("reply","user_id","rid=$rid");

			if ($_SESSION['user_id']==$user_id||$this->check_access()||$_SESSION['user_id']==$adminID){

				$this->cache->delete_cache('tweet'.$tid);

				$this->query("delete from reply where rid=$rid");

				$this->query("delete from notification where rid=$rid");

			}else{

				http_404();

			}

		}

	}

	public function upload(){

		$this->check();

		if(!is_login())http_404();

		if($this->do=='put'){

			$file=upload($_FILES['file'],'upload/files/','jpg,gif,png',5); // 大小限制5M以内

			if(isImage($file)&&!is_notsafe_file($file)){ //检查上传图片是否安全
			$images=new image();
			//gif,jpg,png
            if(is_file($this->config["waterimg"])){
			if(get_ext($file)=='jpg' || get_ext($file)=='png')
			{$images->water(ROOT.'upload/files/'.$file,$this->config["waterimg"]);}
			}
                if(get_ext($file)=='jpg'){
					#照片压缩
					$images->thumb(ROOT.'upload/files/'.$file,ROOT.'upload/files/'.$file,800,800);
				}
				echo json_encode(array("result"=>"success","message"=>$file));

			}

		}
		if($this->do=='rotate'){

			$filename=trim($_POST['image']);

			// 旋转角度 iphone照片默认需要旋转3个90度

			$degrees=270;

			$data = @getimagesize($filename);

			if($data==false)return false;

			switch ($data[2]) {

				case 1:
				$src = imagecreatefromgif($filename);
				break;

				case 2:
				$src = imagecreatefromjpeg($filename);
				break;

				case 3:
				$src = imagecreatefrompng($filename);
				break;

			}

			$rotate = @imagerotate($src,$degrees,0);

			//输出图片

			@imagejpeg($rotate,$filename,100);

			//回收资源
			@imagedestroy($rotate);

			echo json_encode(array("result"=>"success","message"=>$filename));

		}
	}
	public function tag(){

		$keywords=trim(replace_chars($_GET['keywords']));

		$page_size=20;

		$page_current=isset($_GET['page'])&&is_numeric($_GET['page'])?intval($_GET['page']):1;

		#查询结果缓存380分钟
		$result=$this->cache->read('tag'.urlencode($keywords).$page_current,380);

		$pager='';

		if(empty($result)){

			$array=$this->getTweetList($page_size,$page_current,'',$keywords);

			$pager=$this->pager('category',urlencode($keywords)."/",$page_current,$page_size,$this->TweetCount);

			$this->cache->write('tag'.urlencode($keywords).$page_current,$array);

			$this->template->in('tweet',$array);

		}else{

			$this->template->in('tweet',$result);

		}

		$this->userinfo();

		$hots=$this->cache->read('hots',720);

		$tags=$this->cache->read('tags_list',720);

		if(empty($tags)){
		
			$tags=$this->get_tags();
		
			$this->cache->write('tags_list',$tags);
		}
		$this->template->in('pager',$pager);

		$this->template->in('page_current',$page_current);

		$this->template->in('tag',$keywords);

		$this->template->in('users',$this->get_users());

		$this->template->in('hots',$hots);

		$this->template->out('tag.php');
	}

	public function reply(){

		$this->check();

		if(!is_login()){

			json(array('error'=>0));

		}
		if(empty($_POST['content'])){

			json(array('error'=>1));

		}
		//限制发帖间隔

		$now=$_SERVER['REQUEST_TIME'];

		if($this->config['comment_interval_time']>0){

			$last_time=$this->value("reply","lastdate","user_id=".$_SESSION['user_id']." ORDER BY lastdate DESC limit 1");

			if(!empty($last_time)){

				$time=$now-$last_time;

				if($time<=(int)$this->config['comment_interval_time']){

					json(array(
						'error'=>3
					));

				}

			}

		}
		$tid=empty($_POST['tid'])?0:intval($_POST['tid']);

		$content=$this->filter_html(trim(addslashes($_POST['content'])));

		$lastdate=$_SERVER['REQUEST_TIME'];

		$parent_id=empty($_POST['parent_id'])?0:intval($_POST['parent_id']);

		$user_id=isset($_SESSION['user_id'])?$_SESSION['user_id']:'';

		#检查是否包含垃圾关键字，包含则返回信息已屏蔽

		$unsafe=false;

		$unsafe_keywords=!empty($this->config['content_filter'])?explode(",",$this->config['content_filter']):array();

		if(count($unsafe_keywords)>1){

			foreach($unsafe_keywords as $keyword){

				if(strpos($content,$keyword)!==false){

					$unsafe=true;

					json(array('error'=>2));

					break;

				}

			}

		}
		if(!$unsafe){

			$this->query("insert into reply set tid='$tid',content='$content',lastdate='$lastdate',parent_id='$parent_id',user_id='$user_id'");

			$newid=$this->id();

			$newcontent=$this->notification($content,$tid,$lastdate,$newid);

			$this->query("update reply set content='$newcontent' where rid=$newid");

			$this->cache->delete_cache('tweet'.$tid);

			$row=$this->row("SELECT a.*,b.user_avatar,b.user_name FROM reply AS a LEFT JOIN user AS b ON a.user_id=b.user_id WHERE a.rid=$newid");
			if($row['user_avatar']==''){
			$row['user_avatar'] = PATH.'theme/style/avatar.jpg"';
			
			}

			if($row['parent_id']!=0){

				$parent_id=$row['parent_id'];

				$replyAt=$this->row("SELECT a.user_id,b.user_name FROM reply AS a LEFT JOIN user AS b ON a.user_id=b.user_id WHERE a.rid=$parent_id");

				$reply_user_id=$replyAt['user_id'];

				$reply_user_name=$replyAt['user_name'];

				if(isset($_SESSION['user_id'])&&$_SESSION['user_id']!=$reply_user_id){

					$user_id=$_SESSION['user_id'];

		            $this->query("insert into notification set content='$content',tid='$tid',atuid='$reply_user_id',user_id='$user_id',lastdate='$lastdate',rid='$parent_id',prid='$newid',isread=isread+1,flag=2");

				}

			}else{

				$reply_user_id='';

				$reply_user_name='';

				$tweet_user_id=$this->value("tweet","user_id","tid=$tid");

				if(isset($_SESSION['user_id'])&&$_SESSION['user_id']!=$tweet_user_id){

					$user_id=$_SESSION['user_id'];

		            $this->query("insert into notification set content='$content',tid='$tid',atuid='$tweet_user_id',user_id='$user_id',lastdate='$lastdate',rid='$newid',isread=isread+1,flag=1");

				}

			}

			$array= array(
				'rid' => $row['rid'],
				'tid' => $row['tid'],
				'user_name' => $row['user_name'],
				'lastdate'=> " 刚刚",
				'content'=> stripslashes($row['content']),
				'user_id'=> $row['user_id'],
				'user_avatar' =>$row['user_avatar'],
				'child'=>$row['parent_id'],
				'reply_user_name'=>$reply_user_name,
				'reply_user_id'=>$reply_user_id
				);

			json($array,true);

		}

	}

	public function view(){

		$id=intval($_GET['id']);

		$array=$this->cache->read('tweet'.$id,1800);

		if(empty($array)){

			$array=$this->getTweetView($id);

			!empty($array)?$this->cache->write('tweet'.$id,$array):http_404();

		}

		!empty($array)?$this->template->in('tweet',$array):http_404();

		$this->userinfo();

		$this->template->out('tweet.php');
	}

	public function user(){

		$userid=intval(addslashes($_GET['id']));

		$row=$this->row("SELECT * FROM user WHERE user_id =$userid");

		if(!$row)http_404();

		$user_info=array();

		$user_info['id']=$userid;

		$user_info['avatar']=$row['user_avatar'];

		$user_info['name']=$row['user_name'];

		$user_info['sex']=$row['user_sex'];

		$user_info['sign']=$row['user_sign'];

		$user_info['city']=$row['user_city'];

		$user_info['bg']=$row['user_bg'];

		$user_info['old']=$row['user_old'];

		$user_info['follows']=$this->getFollower($userid);

		$user_info['tweet']=$this->getTweetCount($userid);

		$user_info['album']=$this->getAlbumCount($userid);

		$user_info['logintime']=format_time($row['user_login_time']);

		$user_info['is_follow']=$this->is_follow($userid);

		$page_size=10;

		$page_current=isset($_GET['page'])&&is_numeric($_GET['page'])?intval($_GET['page']):1;

		$array=$this->getTweetList($page_size,$page_current,$userid);

		if($array){

			$pager=$this->pager('?app=tweet&action=user',$userid,$page_current,$page_size,$this->TweetCount);

		}else{

			$pager='';

		}

		$album=$this->result("select file,content from tweet where user_id=$userid and file!='' order by lastdate desc limit 6");

		if($album){

			$arr=array();

			foreach($album as $key=>$row){
				$images=explode(",",$row['file']);
            foreach($images as $img){
			    $arr[]=$img;
			}
			}
			
			$this->template->in('albums',$arr);

		}

		$this->userinfo();

		$this->template->in('tweet',$array);

		$this->template->in('pager',$pager);

		$this->template->in('page_current',$page_current);

		$this->template->in('timeline',$this->Timeline);

		$this->template->in('user',$user_info);

		$this->template->in('user_follow',$this->follow_user($userid));

		$this->template->out('user.php');
	}
	public function notice(){

		if(!is_login())http_404();

		if($this->do=='get'){

			$isread=isset($_GET['isread'])&&is_numeric($_GET['isread'])?intval($_GET['isread']):0;

			$user_id=isset($_SESSION['user_id'])?$_SESSION['user_id']:'';

			$page_size=10;

			$page_current=isset($_GET['page'])&&is_numeric($_GET['page'])?intval($_GET['page']):1;

			$sql="SELECT a.*,b.user_avatar,b.user_name FROM notification AS a LEFT JOIN user AS b ON a.user_id=b.user_id WHERE a.isread=$isread AND a.atuid=$user_id ORDER BY a.lastdate DESC";

			$count =mysql_num_rows($this->query($sql));

			if($count>0){

				$array=array();

				$result=$this->result($sql." LIMIT ".(($page_current-1)*$page_size).",".$page_size);

				foreach ($result as $row) {

					// 暂缺评论回复子id字段
					$array[$row['nid']]['id']=$row['nid'];

					$array[$row['nid']]['tid']=$row['tid'];

					$array[$row['nid']]['rid']=$row['rid'];

					$array[$row['nid']]['prid']=$row['prid'];

					$array[$row['nid']]['user_id']=$row['user_id'];

					$array[$row['nid']]['atuid']=$row['atuid'];

					$array[$row['nid']]['user_name']=$row['user_name'];

					$array[$row['nid']]['user_avatar']=$row['user_avatar'];

					if(isset($_GET['isread'])==1&&isset($_GET['json'])){

						$array[$row['nid']]['content']=preg_replace('/<img.+src=\"?(.+\.(jpg|gif))\"?.+>/i','[表情]',$row['content']);

					}else{
						
						$array[$row['nid']]['content']=$row['content'];
					
					}

					$array[$row['nid']]['lastdate']=format_time($row['lastdate']);

					$array[$row['nid']]['flag']=$row['flag'];

					$array[$row['nid']]['isread']=$row['isread'];

					if($row['flag']==1||$row['flag']==0){

						$array[$row['nid']]['tweet_content']=$this->value('tweet','content','tid='.$row['tid']);

						$array[$row['nid']]['tweet_file']=$this->value('tweet','file','tid='.$row['tid']);

					}elseif ($row['flag']==2) {

						$array[$row['nid']]['reply_content']=$this->value('reply','content','rid='.$row['rid']);

					}

					if(isset($_GET['read'])){

						if($row['isread']==1)$this->query("update notification set isread=0 where nid='".$row['nid']."'");
					}				

				}

				if(isset($_GET['isread'])==1&&isset($_GET['json'])){

					json($array,true);

				}

				$pager=$this->pager('notification','',$page_current,$page_size,$count);

				$this->template->in('pager',$pager);

				$this->template->in('notification',$array);

			}

			$this->userinfo();

			$this->template->out('notification.php');

		}elseif($this->do=='delete'){

			$nid = intval($_GET['nid']);

			$row=$this->row("SELECT atuid FROM notification WHERE nid=$nid");

			if($_SESSION['user_id']==$row['atuid']){

				$this->query("delete from notification where nid=$nid");
			}
		}
	}
	public function follow_on(){

		$this->check();

		if(!is_login())echo json_encode(array("result"=>"0"));

		$follow_id=isset($_GET['user_id'])?intval($_GET['user_id']):0;

		if(isset($_SESSION['user_id'])&&!$this->is_follow($follow_id)&&$follow_id!=$_SESSION['user_id']){
			$array=array();
			$array['follow_time']=$_SERVER['REQUEST_TIME'];
			$array['follow_id']=$follow_id;
			$array['user_id']=$_SESSION['user_id'];
			$this->insert("user_follow",$array);
			echo json_encode(array("result"=>"1"));
		}

	}
	public function follow_off(){

		$this->check();

		if(!is_login())echo json_encode(array("result"=>"0"));

		$follow_id=isset($_GET['user_id'])?intval($_GET['user_id']):0;

		if(isset($_SESSION['user_id'])&&!empty($follow_id)){

			$this->del("user_follow","user_id=".$_SESSION['user_id']." AND follow_id=$follow_id");

			echo json_encode(array("result"=>"1"));
		}

	}
	public function my_friend(){
		if(!is_login())echo json_encode(array("result"=>"0"));

		if(isset($_SESSION['user_id'])){

			$array=array();

			$result=$this->result("SELECT user_id,user_name FROM user WHERE user_id IN (SELECT follow_id FROM user_follow WHERE user_id=".$_SESSION['user_id'].") ORDER BY user_id DESC");
			if($result){
				foreach ($result as $row) {
					$array[$row['user_id']]['name']=$row['user_name'];
					$array[$row['user_id']]['id']=$row['user_id'];
				}
			}

			json($array,true);
		}
	}
	private function is_follow($user_id){
		if(isset($_SESSION['user_id'])){
			if($this->row("SELECT * FROM user_follow WHERE user_id=".$_SESSION['user_id']." AND follow_id=".$user_id)){
				return true;
			}else{
				return false;
			}
		}
	}
	private function follow_user($user_id){
		$array=array();
		$result=$this->result("SELECT user_id,user_name,user_avatar FROM user WHERE user_id IN (SELECT user_id FROM user_follow WHERE follow_id=".$user_id.") ORDER BY user_id DESC LIMIT 12");

		if($result){
			foreach ($result as $row) {
				$array[$row['user_id']]['id']=$row['user_id'];
				$array[$row['user_id']]['name']=$row['user_name'];
				$array[$row['user_id']]['avatar']=$row['user_avatar'];
			}
		}
		return $array;
	}
	public function admin_tweet(){

		if(!$this->check_access())exit("You don't have permission to access!");	

		$mode='';

		if ($this->do=='tweet_list') :
			$mode="动态列表";
			$page_current=isset($_GET['p'])&&is_numeric($_GET['p'])?intval($_GET['p']):1;
			$this->template->in('tweet',$this->getTweetList(20,$page_current));
			$this->template->in('pager',$this->pager('tweet','admin_tweet&do=tweet_list&p=',$page_current,20,$this->TweetCount,2));
		elseif($this->do='tweet_reply') :
			$mode="评论列表";

		endif;
		$this->template->in('mode',$mode);
		$this->template->out('admin.tweet.php');
	}
	public function setting(){
		return '<form action="'.PATH.'?app=tweet&action=setting_update" method="post">
		<h4>动态设置</h4>
		<fieldset>
		<legend>数量设置</legend>
		<table width="100%" class="form-table">
			<tr>
			<td class="input-name">首页显示条数：</td>
			<td><input type="text" size="50" name="index_size" value="'.$this->config['index_size'].'" class="input"/></td>
			</tr>
			<tr>
			<td class="input-name">评论发布间隔秒钟</td>
			<td><input type="text" size="50" name="comment_interval_time" value="'.@$this->config['comment_interval_time'].'" class="input"/></td>
			</tr>
			<tr>
			<td class="input-name  valign-top">关键词过滤：</td>
			<td><textarea class="input" name="content_filter" rows="5" cols="60">'.@$this->config['content_filter'].'</textarea> <br>多个关键词请使用英文逗号隔开</td>
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
		$array['index_size']=empty($_POST['index_size'])?'':addslashes(trim($_POST['index_size']));
		$array['content_filter']=empty($_POST['content_filter'])?'':trim($_POST['content_filter']);
		$array['comment_interval_time']=empty($_POST['comment_interval_time'])?'':trim($_POST['comment_interval_time']);
		$app_key=base64_encode(serialize($array));
		$this->update("apps",array('app_key'=>$app_key),"app_name='tweet'");
		alert('动态设置成功!');
		redirect(PATH.'?app=set&action=control&do=setting');
	}
	public function install(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$this->query("insert into apps set app_name='tweet',app_key=''");
	}

	public function uninstall(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$this->query("delete from apps where app_name='tweet'");
	}
	private function userinfo(){
		if(isset($_SESSION['user_id'])){
			$this->template->in('album_count',$this->getAlbumCount($_SESSION['user_id']));
			$this->template->in('tweet_count',$this->getTweetCount($_SESSION['user_id']));
			$this->template->in('notice',$this->getNotifiCount($_SESSION['user_id']));
			$this->template->in('user_bg',$this->value("user","user_bg","user_id=".$_SESSION['user_id']));
		}
	}
	private function getTweetList($page_size,$page_current,$userid='',$keywords=''){

		$prefix_count="SELECT count(tid)";

		$prefix_result_time="SELECT a.lastdate";

		$prefix_result="SELECT a.tid AS tid,a.title AS title,a.content AS content,a.lastdate AS lastdate,a.hot AS hot,a.agent AS agent,a.ip AS ip,b.user_name AS user_name,a.file AS file,a.status AS status,b.user_avatar AS user_avatar,b.user_id AS user_id";

		$sql=" FROM tweet AS a LEFT JOIN user AS b ON a.user_id=b.user_id ";

		if($userid!=''){
			$sql.=" WHERE a.user_id=$userid ";
			if(!is_login()||@$_SESSION['user_id']!=$userid){
				$sql.=" AND a.status=0 ";
			}
		}elseif($keywords!=''){
			$sql.=" WHERE a.status=0 AND a.content like '%".$keywords."%' ";
		}else{
			$sql.=" WHERE a.status=0 ";
		}
		if(isset($_SESSION['user_id'])&&isset($_GET['myfeed'])){
			$friends=$this->result('SELECT follow_id FROM user_follow WHERE user_id='.$_SESSION['user_id']);
			$ids=array();
			if(!empty($friends)){
				foreach($friends as $v){
					$ids[]=$v['follow_id'];
				}
			}
			
			$sql.=" AND a.user_id IN (".implode(",",$ids).") ";
		}

		$sql.="ORDER BY a.lastdate DESC";

		$limit=" LIMIT ".(($page_current-1)*$page_size).",".$page_size;

		$count=$this->count($prefix_count.$sql,false);

		$this->TweetCount=$count;
		$array=array();
		$datetime=array();
		$archivedb=array();
		$archives=$this->result($prefix_result_time.$sql);
if(is_array($archives)){ 
		foreach ($archives as $row) {
			$datetime[] = gmdate("Y-m", $row['lastdate']);
		}
		
 }
		
		$timeline = array_count_values($datetime);

		foreach($timeline as $key => $value) {
			list($y, $m) = explode('-', $key);
			$archivedb[$y][$m]['year']= $y;
			$archivedb[$y][$m]['mon']= $m;
			$archivedb[$y][$m]['count']= $value;
		}

		$this->Timeline=$archivedb;

		$tweet=$this->result($prefix_result.$sql.$limit);

		$year=0; $mon=0;
if(is_array($tweet)){    //add

		foreach ($tweet as $row) {

			list($year_tmp,$mon_tmp) = explode('-',date('Y-m',$row['lastdate']));

			if ($year != $year_tmp) {
				$year = $year_tmp;
			}
			if ($mon != $mon_tmp) {
				$mon = $mon_tmp;
				$array[$row['tid']]['archives']=$year."-".$mon;
			}

			$array[$row['tid']]['date']=gmdate("Y-m", $row['lastdate']);

			$array[$row['tid']]['id']=$row['tid'];

			$array[$row['tid']]['user_id']=$row['user_id'];

			$array[$row['tid']]['user_name']=$row['user_name'];

			$array[$row['tid']]['user_avatar']=$row['user_avatar'];

			$array[$row['tid']]['title']=$row['title'];

			if($keywords!=''){

				$array[$row['tid']]['content']=searchred($row['content'],$keywords);

			}else{
				
				$array[$row['tid']]['content']=$row['content'];
			}

			$array[$row['tid']]['description']=truncate(strip_tags($row['content']),100);

			$array[$row['tid']]['lastdate']=format_time($row['lastdate']);

			$array[$row['tid']]['likes']=$row['hot'];

			$array[$row['tid']]['agent']=getOS($row['agent']);

			$array[$row['tid']]['reply_num']=$this->getReplycount($row['tid']);

			$array[$row['tid']]['ip']=$row['ip'];

			$array[$row['tid']]['status']=$row['status'];

			$tid=$row['tid'];
			if(isset($_SESSION['user_id'])){
			$userid=$_SESSION['user_id'];
			
			$array[$row['tid']]['like_status']=intval($this->value("liked","COUNT(`user_id`)","tid=$tid AND user_id=$userid"));}
			# 附件转换
			if($row['file']!=""){

				$_images=explode(",",$row['file']);
				$array[$row['tid']]['images_count']=count($_images);
				$IM=array();
				foreach ($_images as $key => $value) {
					$IM[$key]['img']=$value;
					$IM[$key]['ext']=get_ext($value);
				}
				$array[$row['tid']]['images']=$IM;
			}
			# 点赞用户
			if($row['hot']>0){
				$array2=array();
				$zan=$this->result("SELECT a.user_id,b.user_name,b.user_avatar FROM liked AS a LEFT JOIN user AS b ON a.user_id=b.user_id WHERE a.tid =$tid order by a.id desc limit 4");
				foreach ($zan as $key=>$row2) {
					$array2[$key]['user_id']=$row2['user_id'];
					$array2[$key]['user_name']=$row2['user_name'];
					$array2[$key]['user_avatar']=$row2['user_avatar'];
				}
				$array[$row['tid']]['zan']=$array2;
			}
			# 回应列表
			$replys=array();

			$reply_sql="SELECT a.*,b.user_avatar,b.user_name FROM reply AS a LEFT JOIN user AS b ON a.user_id=b.user_id WHERE a.tid=$tid order by a.lastdate asc";

			$reply_count =mysql_num_rows($this->query($reply_sql));

			if($reply_count>0){

				$reply=$this->result($reply_sql." LIMIT 5");

				foreach ($reply as $row3) {

					$replys[$row3['rid']]['id']=$row3['rid'];

					$replys[$row3['rid']]['content']=$row3['content'];

					$replys[$row3['rid']]['lastdate']=format_time($row3['lastdate']);

					$replys[$row3['rid']]['user_id']=$row3['user_id'];

					$replys[$row3['rid']]['user_name']=$row3['user_name'];

					$replys[$row3['rid']]['user_avatar']=$row3['user_avatar'];

					$replys[$row3['rid']]['child']=$row3['parent_id'];

					if($row3['parent_id']!=0) :

					$parent_id=$row3['parent_id'];

					$reply_par_sql="SELECT a.user_id,b.user_name FROM reply AS a LEFT JOIN user AS b ON a.user_id=b.user_id WHERE a.rid=$parent_id";

					$replyAt=$this->row($reply_par_sql);

					$replys[$row3['rid']]['reply_user_id']=$replyAt['user_id'];

					$replys[$row3['rid']]['reply_user_name']=$replyAt['user_name'];

					endif;
				}

				$array[$row['tid']]['reply']=$replys;

				if($reply_count>5)$array[$row['tid']]['reply_count']=$reply_count-5;
			}
		}
		}
		return $array;
	}
	private function getTweetView($id){

		$row=$this->row("SELECT a.*,b.user_avatar,b.user_name FROM tweet AS a LEFT JOIN user AS b ON a.user_id=b.user_id WHERE a.tid=$id");
	    $like_status=intval($this->value("liked","COUNT(`user_id`)","tid=$id AND user_id={$row['user_id']}"));

		if($row){
		
			$array=array();
		
			$array['id']=$row['tid'];
		
			$array['title']=$row['title'];
		
			$array['description']=truncate(strip_tags($row['content']),20);
		
			$array['user_id']=$row['user_id'];
		
			$array['user_name']=$row['user_name'];
			
			$array['like_status']=$like_status;
		
			$array['user_avatar']=$row['user_avatar'];
		
			$array['content']=$row['content'];
		
			$array['lastdate']=format_time($row['lastdate']);
		
			$array['likes']=$row['hot'];
		
			$array['ip']=$row['ip'];
		
			$tid=$row['tid'];
			# 附件转换
		
			if($row['file']!=""){
		
				$_images=explode(",",$row['file']);
		
				$_count=count($_images);
		
				$array['images_count']=$_count;
		
				$array['images']=$_images;
			}
		
			# 点赞用户
		
			if($row['hot']>0){
		
				$array2=array();
		
				$zan=$this->result("SELECT a.user_id,b.user_name,b.user_avatar FROM liked AS a LEFT JOIN user AS b ON a.user_id=b.user_id WHERE a.tid =$tid order by a.id desc limit 10");
		
				foreach ($zan as $key=>$row2) {
		
					$array2[$key]['user_id']=$row2['user_id'];
		
					$array2[$key]['user_name']=$row2['user_name'];
		
					$array2[$key]['user_avatar']=$row2['user_avatar'];
		
				}
		
				$array['zan']=$array2;
		
			}
		
			# 回应列表
		
			$replys=array();
		
			$reply_sql="SELECT a.*,b.user_avatar,b.user_name FROM reply AS a LEFT JOIN user AS b ON a.user_id=b.user_id WHERE a.tid=$tid order by a.lastdate asc";
		
			$reply_count =mysql_num_rows($this->query($reply_sql));
		
			if($reply_count>0){
		
				$reply=$this->result($reply_sql);
		
				foreach ($reply as $row3) {
		
					$replys[$row3['rid']]['id']=$row3['rid'];
		
					$replys[$row3['rid']]['content']=stripslashes($row3['content']);
		
					$replys[$row3['rid']]['lastdate']=format_time($row3['lastdate']);
		
					$replys[$row3['rid']]['user_id']=$row3['user_id'];
		
					$replys[$row3['rid']]['user_name']=$row3['user_name'];
		
					$replys[$row3['rid']]['user_avatar']=$row3['user_avatar'];
		
					$replys[$row3['rid']]['child']=$row3['parent_id'];
		
					if($row3['parent_id']!=0){
		
						$parent_id=$row3['parent_id'];
			
						$reply_par_sql="SELECT a.user_id,b.user_name FROM reply AS a LEFT JOIN user AS b ON a.user_id=b.user_id WHERE a.rid=$parent_id";
			
						$replyAt=$this->row($reply_par_sql);
			
						$replys[$row3['rid']]['reply_user_id']=$replyAt['user_id'];
			
						$replys[$row3['rid']]['reply_user_name']=$replyAt['user_name'];
		
					}
		
				}
		
				$array['reply']=$replys;
		
			}
		
		}else{
		
			$array='';
		
		}
		
		return $array;
	}
	private function getFollower($userid){
		return $this->value("user_follow","COUNT(`user_id`)","follow_id='".$userid."'");
	}
	private function getAlbumCount($userid,$count=0){
	    $album=$this->result("select file from tweet where user_id=$userid and file!='' ");
	    if($album){
		    foreach ($album as $row) {
		        $images=explode(",",$row['file']);
		        $count+=count($images);
		    }
	    }
	    return $count;
	}
	private function getTweetCount($userid){
	    return $this->value("tweet","COUNT(`tid`)","user_id='".$userid."'");
	}
	private function getNotifiCount($atuid){
	    return $this->value("notification","COUNT(`nid`)","atuid='".$atuid."' AND isread=1");
	}
	private function notification($content,$tid,$time,$rid){
	    $pattern = "/@([^@^\\s^:]{1,})([\\s\\:\\,\\;]{0,1})/";
	    preg_match_all ( $pattern, $content, $matches );
	    $matches [1] = array_unique($matches [1]);
	    foreach ( $matches [1] as $u ) {
	        if ($u){
	            $row=$this->row("SELECT user_id FROM `user` WHERE `user_name`='".strtolower($u)."'");
	            if ($row['user_id']) {
	                $user_id=$_SESSION['user_id'];
	                $search [] = '@'.$u;
	                $replace [] = '<a href="index.php?app=tweet&action=user&id='.$row['user_id'].'">@'.$u.'</a>';
	                if($user_id!=$row['user_id']){
	                    $atuid=$row['user_id'];
	                    $this->query("insert into notification set content='$content',tid='$tid',rid='$rid',atuid='$atuid',user_id='$user_id',lastdate='$time',isread=isread+1");
	                }
	            }
	        }
	    }
	    $str = str_replace( @$search, @$replace, $content);

	    return $str;
	}
	private function get_hots($thumb=false){
		$sql="select a.*,b.user_avatar,b.user_name from tweet as a left join user as b on a.user_id=b.user_id ";
		if($thumb==true)
			$sql.="where a.file!='' ";
		$sql.="order by a.hot desc limit 10";
		$array=array();
		$result=$this->result($sql);
		$i=1;
		if($result){
			foreach($result as $row){
				$array[$row['tid']]['id']=$row['tid'];
				$array[$row['tid']]['user_id']=$row['user_id'];
				$array[$row['tid']]['user_name']=$row['user_name'];
				$array[$row['tid']]['title']=truncate(strip_tags($row['content']),26);
				$array[$row['tid']]['images']=explode(",",$row['file']);
				$array[$row['tid']]['likes']=$row['hot'];
				$array[$row['tid']]['num']=$i;
				$i++;
			}
		}
		return $array;
	}
	private function get_users(){
		$array=array();
		$result=$this->result("SELECT * FROM user ORDER BY user_login_time DESC LIMIT 8");
		if($result){
			foreach($result as $row){
				$array[$row['user_id']]['id']=$row['user_id'];
				$array[$row['user_id']]['name']=$row['user_name'];
				$array[$row['user_id']]['avatar']=$row['user_avatar'];
				$array[$row['user_id']]['bg']=$row['user_bg'];
				$array[$row['user_id']]['city']=$row['user_city'];
				$array[$row['user_id']]['sign']=$row['user_sign'];
				$array[$row['user_id']]['is_new']=$_SERVER['REQUEST_TIME']-$row['user_login_time']<3600*3?true:false;
			}
		}
		return $array;		
	}
	private function get_tags(){
		$array=array();
		$result=$this->result("SELECT * FROM tweet_tag ORDER BY tag_count DESC LIMIT 12");
		if($result){
			foreach($result as $row){
				$array[$row['tag_id']]['name']=$row['tag_name'];
				$array[$row['tag_id']]['count']=$row['tag_count'];
			}
		}
		return $array;
	}
	# 自动链接
	private function autoUrl($str){
	    return preg_replace("/(?<=[^\]a-z0-9-=\"'\\/])((https?|ftp):\/\/)([a-z0-9\/\-_+=.~!%@?#%&;:$\\│]+)/i",'<a href="\\1\\3" target="_blank" rel="nofollow">\\1\\3</a>'," ".$str);
	}
	# 匹配标签
	private function autoTAG($str){
		return preg_replace("/#([^#^\\s^:^<br\/>^!]{1,})([\\s\\:\\,\\;]{0,1})/",'<a href="index.php?app=tweet&action=tag&keywords=\\1\\3">#\\1\\3</a>'." ", $str." ");
	}
	# 文本过滤
	private function filter_html($str){
	    $str = $this->xss_clean(addslashes($str));
		$str1 = preg_replace("/<a[^>]*>/","", $str);
		$str = preg_replace("/<\/a>/","", $str1);
	    $str = preg_replace("/&nbsp;/","",$str);
	    $str = htmlspecialchars($str);
	    for($i=0;$i<36;$i++){
	        $str=str_replace("[e:".$i."]","<img src=\"".$this->path()."theme/emot/".$i.".gif\" alt=\"emot\" align=\"absmiddle\"/>",$str);
	    }
	    $str = $this->autoTAG($str);
	    $str = $this->autoUrl($str);
	    return $str;
	}
	# 获取评论个数
	private function getReplycount($tid){
	    return $this->value("reply","COUNT(`rid`)","tid='".$tid."'");
	}
	private function ubb($content) {
		$content=preg_replace("/\[quote\](.+?)\[\/quote\]/is","<blockquote>\\1</blockquote>",$content);
		$content=preg_replace("/\[b\](.+?)\[\/b\]/is","<strong>\\1</strong>",$content);
		$content=nl2br($content);
		return $content;
	}
	private function clear_text($str){
		$str = str_replace(chr(13),"",$str);
	    $str = preg_replace("/[\n]+/is","\n",$str);
	    $str = nl2br($str);
	    return trim($str);
	}
}
?>