<?php
return array(
	'app'=>'user',
	'icon'=>'icon-yonghu',
	'info'=> array(
		'name'=>'会员管理',
		'description'=>'用于网站基础登录功能，支持QQ、微博、Instagram登录',
		'version'=>'1.0.0',
		'author'=>'Drop',
		'date'=>'2015'
		),
	'menu'=>array(
		'name'=>'会员管理',
		'item'=>array(
			array('title'=>'会员列表','url'=>'?app=user&action=admin_user')
		)
	)
);