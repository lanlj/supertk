<?php
return array(
	'app'=>'tag',
	'info'=>array(
		'name'=>'标签管理',
		'description'=>'快速删除无意义标签',
		'version'=>'1.0.0',
		'author'=>'Drop',
		'date'=>'2015'		
		),
	 'menu'=>array(
		'name'=>'标签管理',
		'item'=>array(
			array('title'=>'标签列表','url'=>'?app=tag&action=admin_tag')
		)
	)
);