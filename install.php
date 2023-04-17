<?php
define('DROP', TRUE);
if (!defined('ROOT')) {
	define('ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);
}
if (!defined('SYSTEM_DIR')) {
	define('SYSTEM_DIR', ROOT . 'system' . DIRECTORY_SEPARATOR);	
}
if (!defined('APPS_DIR')) {
	define('APPS_DIR', ROOT . 'apps' . DIRECTORY_SEPARATOR);	
}
require_once SYSTEM_DIR.'core.php';
require_once SYSTEM_DIR.'function.php';
header('Content-Type: text/html; charset=utf-8');
if (is_file("install.lock")){
alert('已安装!如需重装请删除【install.lock】',"");
}
class install extends Core{


	function __construct(){

		if($_POST){

			$safe_key		=trim($_POST['safe_key']);
			$admin_name		=trim($_POST['admin_name']);
			$admin_email	=trim($_POST['admin_email']);
			$admin_password=compile_password(trim($_POST['admin_password']),$safe_key);
			$db_host		=trim($_POST['DB_HOST']);
			$db_user		=trim($_POST['DB_USER']);
			$db_pass	=trim($_POST['DB_PASSWORD']);
			$db_name		=trim($_POST['DB_NAME']);
			$path		=trim($_POST['PATH']);
			if(empty($path)){
				$path='/';
			}
			$this->config_db($db_host,$db_user,$db_pass,$db_name);
			#数据库创建表
			$query=array();
			$query[]="CREATE TABLE IF NOT EXISTS `user` (
			  `user_id` mediumint(8) NOT NULL AUTO_INCREMENT,
			  `user_name` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `user_pass` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `user_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `user_sign` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `user_avatar` varchar(160) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `user_sex` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `user_bg` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `user_login_time` int(10) unsigned NOT NULL DEFAULT '0',
			  `qq_token` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `weibo_token` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `user_old` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
			  `user_status` tinyint(1) NOT NULL,
			  `user_city` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  PRIMARY KEY (`user_id`),
			  KEY `open_id` (`qq_token`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

			$query[]="INSERT INTO `user` VALUES (1, '$admin_name', '$admin_password','$admin_email','','','','','0','','','18','3','');";

			$query[]="CREATE TABLE IF NOT EXISTS `apps` (
			  `app_name` varchar(10) NOT NULL,
			  `app_key` text NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";


			$query[]="INSERT INTO `apps` VALUES ('apps','user|admin|tweet|water'), ('user','YToxMTp7czo4OiJxcV9hcHBpZCI7czowOiIiO3M6OToicXFfYXBwa2V5IjtzOjA6IiI7czoxMToicXFfY2FsbGJhY2siO3M6NDA6Ind3dy7kvaDnmoTln5/lkI0uY29tL2FwcC91c2VyL3FxY2FsbGJhY2siO3M6NToid2VpYm8iO2k6MDtzOjExOiJ3ZWlib19hcHBpZCI7czowOiIiO3M6MTI6IndlaWJvX2FwcGtleSI7czowOiIiO3M6MTQ6IndlaWJvX2NhbGxiYWNrIjtzOjQzOiJ3d3cu5L2g55qE5Z+f5ZCNLmNvbS9hcHAvdXNlci93ZWlib2NhbGxiYWNrIjtzOjM6ImlucyI7aTowO3M6OToiaW5zX2FwcGlkIjtzOjA6IiI7czoxMDoiaW5zX2FwcGtleSI7czowOiIiO3M6MTI6Imluc19jYWxsYmFjayI7czo0ODoiaHR0cDovL3d3dy7kvaDnmoTln5/lkI0uY29tL2FwcC91c2VyL2luc2NhbGxiYWNrIjt9'), ('tweet','YTozOntzOjEwOiJpbmRleF9zaXplIjtzOjI6IjE1IjtzOjE0OiJjb250ZW50X2ZpbHRlciI7czo1Njoi5rWL6K+V5LiA5LiLLGNlc2hpLOWFvOiBjCzlsI/lp5As6KO46IGKLOWFseS6p+WFmizlgrvpgLwiO3M6MjE6ImNvbW1lbnRfaW50ZXJ2YWxfdGltZSI7czoyOiIxNSI7fQ=='), ('admin','YTo4OntzOjg6InNpdGVuYW1lIjtzOjc6IlN1cGVyVEsiO3M6MTE6InNpdGVzdWJuYW1lIjtzOjI0OiLnhafniYfjgIHkuqTlj4vjgIHml4XooYwiO3M6MTI6InNpdGVrZXl3b3JkcyI7czozMzoi54Wn54mHLOS6pOWPiyzml4XooYws5YiG5Lqr54Wn54mHIjtzOjE1OiJzaXRlZGVzY3JpcHRpb24iO3M6MzM6IueFp+eJhyzkuqTlj4ss5peF6KGMLOWIhuS6q+eFp+eJhyI7czo4OiJzdGF0Y29kZSI7czowOiIiO3M6Njoibm90aWNlIjtzOjEyOiLmrKLov47kvb/nlKgiO3M6MjoiYWQiO3M6MTIyOiI8YSB0YXJnZXQ9Il9ibGFuayIgaHJlZj0iaHR0cDovL3N1cGVydGsubWwvIj48aW1nIHNyYz0iaHR0cDovL3d3dy5lcjhkLmNvbS9zdXBlcnRrL2FkLmpwZyIgd2lkdGg9IjMwMCIgaGVpZ2h0PSIzMDAiIC8+PC9hPiI7czozOiJpY3AiO3M6MDoiIjt9'), 
			('water','YToxOntzOjg6IndhdGVyaW1nIjtzOjk6IndhdGVyLnBuZyI7fQ==');";


			$query[]="CREATE TABLE IF NOT EXISTS `liked` (
			  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
			  `tid` mediumint(8) NOT NULL,
			  `user_id` mediumint(8) NOT NULL,
			  PRIMARY KEY (`id`),
			  KEY `user_id` (`user_id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

			$query[]="CREATE TABLE IF NOT EXISTS `user_follow` (
			  `follow_time` int(10) unsigned NOT NULL DEFAULT '0',
			  `follow_id` bigint(20) unsigned NOT NULL DEFAULT '0',
			  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
			  KEY `follow_id` (`follow_id`,`user_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";


			$query[]="CREATE TABLE IF NOT EXISTS `tweet` (
			  `tid` mediumint(8) NOT NULL AUTO_INCREMENT,
			  `content` text COLLATE utf8_unicode_ci NOT NULL,
			  `file` varchar(1000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `lastdate` int(10) unsigned NOT NULL DEFAULT '0',
			  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
			  `agent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `hot` mediumint(8) unsigned NOT NULL DEFAULT '0',
			  `user_id` mediumint(8) NOT NULL,
			  `status` tinyint(1) NOT NULL,
			  PRIMARY KEY (`tid`),
			  KEY `user_id` (`user_id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

			$query[]="CREATE TABLE IF NOT EXISTS `notification` (
			  `nid` mediumint(8) NOT NULL AUTO_INCREMENT,
			  `content` text COLLATE utf8_unicode_ci NOT NULL,
			  `tid` mediumint(8) NOT NULL,
			  `atuid` mediumint(8) NOT NULL,
			  `user_id` mediumint(8) NOT NULL,
			  `lastdate` int(10) unsigned NOT NULL DEFAULT '0',
			  `isread` tinyint(1) unsigned zerofill NOT NULL DEFAULT '0',
			  `rid` mediumint(8) NOT NULL,
			  `flag` tinyint(1) NOT NULL,
			  PRIMARY KEY (`nid`),
			  KEY `atuid` (`atuid`,`isread`,`nid`),
			  KEY `tid` (`tid`),
			  KEY `user_id` (`user_id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

			$query[]="CREATE TABLE IF NOT EXISTS `reply` (
			  `rid` mediumint(8) NOT NULL AUTO_INCREMENT,
			  `content` text COLLATE utf8_unicode_ci NOT NULL,
			  `lastdate` int(10) unsigned NOT NULL DEFAULT '0',
			  `tid` mediumint(8) NOT NULL DEFAULT '0',
			  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
			  `user_id` mediumint(8) NOT NULL,
			  PRIMARY KEY (`rid`),
			  KEY `tid` (`tid`,`user_id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

			$query[]="CREATE TABLE `link` (
			  `link_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
			  `link_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
			  `link_text` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `link_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  PRIMARY KEY (`link_id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

			$query[] ="CREATE TABLE IF NOT EXISTS `tweet_tag` (
				tag_id bigint(20) NOT NULL AUTO_INCREMENT,
				tag_name varchar(50) NOT NULL DEFAULT '',
				tag_count int(4) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`tag_id`),
				KEY `tag_name` (`tag_name`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";


			$query[]="ALTER TABLE notification ADD prid mediumint(8) NOT NULL DEFAULT '0' AFTER rid;";

			$query[]="ALTER TABLE tweet ADD title varchar(100) NOT NULL DEFAULT '' AFTER tid;";

			$query[]="INSERT INTO `link` VALUES ('1','SuperTk','发现精彩好玩的！','http://www.supertk.ml');";
			
			#执行命令
			if(count($query)>0){
				foreach($query as $sql){
					$this->query($sql);
				}
			}
			$CONFIG="<?php\n";
			$CONFIG.="define('PATH','$path');\n\n";
			$CONFIG.="define('DB_HOST','$db_host');\n\n";
			$CONFIG.="define('DB_USER','$db_user');\n\n";
			$CONFIG.="define('DB_PASS','$db_pass');\n\n";
			$CONFIG.="define('DB_NAME','$db_name');\n\n";
			$CONFIG.="define('KEY','$safe_key');\n\n";
			file_put_contents(ROOT.'system/config.php',$CONFIG) or die("请检查文件system/config.php的权限是否为0777!");
			file_put_contents('install.lock', time());
			alert('恭喜！安装完毕，为安全起见，建议您删除【install.php】',$path);
		}
	}
}
new install();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>Install</title>
<style type="text/css">
*{padding:0;margin:0;}
html,body{font:normal 12px 'Microsoft Yahei';color:#666;}
.install{width: 300px;margin:20px auto;}
#info{background:#3385ff;overflow:hidden;height:30px;color: #fff;text-align: center;line-height: 30px;}
p{height: 30px;line-height: 30px;font-weight: bold;}
.input{width:278px;border:1px solid #ccc;background:#fff;padding:10px;font:normal 12px 'Microsoft Yahei';outline:0;color:#222}
.input:focus{border:1px solid #555;}
.submit{border-radius:2px;text-align:center;border:none;padding:9px 15px;background:#3385ff;cursor:pointer;font:bold 12px 'Microsoft Yahei';color:#fff;margin-top: 10px;}
</style>
 </head>
 <body>
 <div id="info">环境：<?php echo $_SERVER['SERVER_SOFTWARE'];?>
</div>
 <div class="install">
	 <form method="post">
	 <p>数据库主机</p>
	 <input type="text" name="DB_HOST" size="30" class="input" value="localhost" />
	 <p>数据库用户</p>
	 <input type="text" name="DB_USER" size="30"  class="input" value="root" />
	 <p>数据库密码</p>
	 <input type="text" name="DB_PASSWORD" size="30"  class="input"  value=""/>
	 <p>数据库名</p>
	 <input type="text" name="DB_NAME" size="30"  class="input" value="test" />
	 <p>管理员昵称</p>
	 <input type="text" name="admin_name" size="30" class="input" value="admin" />
	 <p>管理员邮箱</p>
	 <input type="text" name="admin_email" size="30" class="input" value="admin@admin.com" />
	 <p>管理员密码</p>
	 <input type="text" name="admin_password" size="30" class="input" value="111111" />
	 <p>安全密匙KEY</p>
	 <input type="text" name="safe_key" size="30" class="input" value="<?php echo getRandomKey(15)?>" />
	 <div onclick="if(confirm('确定要安装吗?')){document.forms[0].submit()}" class="submit">开始安装</div>
	 <input type="hidden" name="PATH" size="30"  class="input" value="<?php echo str_replace('install.php','',$_SERVER['SCRIPT_NAME'])?>"/>
	 </form>
 </div>
 </body>
 </html>