<?php
return array(
	'app'=>'tweet',
	'icon'=>'icon-wenben',
	'info'=>array(
		'name'=>'动态',
		'description'=>'用简短的文字介绍旅行，用照片记录生活',
		'version'=>'1.0.0',
		'author'=>'Drop',
		'date'=>'2015.2.16'
		),
	'menu'=>array(
		'name'=>'内容管理',
		'item'=>array(
			array('title'=>'动态列表','url'=>'?app=tweet&action=admin_tweet&do=tweet_list')
		)
	)
);