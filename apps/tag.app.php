<?php 
!defined('DROP') && exit('REFUSED!');
class tag extends Core{
	public function index(){

		$this->template->out('tag.php');
	}
	public function admin_tag(){
	   		if(!$this->check_access())exit("You don't have permission to access!");	
		$mode="标签管理";

		$prefix_id="SELECT tag_id";
		$prefix_count="SELECT count(tag_id)";
		$prefix_result="SELECT *";
		$sql=" FROM tweet_tag WHERE 1=1";
		if(isset($_GET['keyword'])){
			$sql.=" AND user_name like '%".trim($_GET['keyword'])."%'";
		}
		if(isset($_GET['status'])){
			$sql.=" AND user_status=".intval($_GET['status']);
		}
		if(!empty($_GET['orderby'])){
			$orderby=trim($_GET['orderby']);
		}else{
			$orderby='tag_id';
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
				$ids2[]=$v['tag_id'];
			}
			$sql.=" AND tag_id IN (".implode(",",$ids2).")";
		}
		$array=array();
		if($count>0){
			// echo $prefix_result.$sql.$orderby.$limit;
			$result=$this->result($prefix_result.$sql.$orderby." LIMIT $page_size");
			foreach($result as $row){
				$array[$row['tag_id']]['id']=$row['tag_id'];
				$array[$row['tag_id']]['name']=$row['tag_name'];
				$array[$row['tag_id']]['count']=$row['tag_count'];
			}
			$parameter='admin_tag';
			$pager=$this->pager('?app=tag&action=admin_tag',$parameter,$page_current,$page_size,$count,2);
			$this->template->in('pager',$pager);
		}
		$this->template->in('mode',$mode);
		$this->template->in('tag',$array);
		$this->template->out('admin.tag.php');
	}
		public function delete_tag(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$tag_id=empty($_POST['tag_id'])?array():$_POST['tag_id'];
		if(!empty($tag_id)){
			foreach($tag_id as $id){
				if(!empty($id)){
					$this->del("tweet_tag","tag_id=".$id."");
				}
			}
		}
		alert('标签删除成功！');
	}
  public function install(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$this->query("insert into apps set app_name='tag',app_key=''");
	}
	public function uninstall(){
		if(!$this->check_access())exit("You don't have permission to access!");	
		$this->query("delete from apps where app_name='tag'");
	}

}
?> 