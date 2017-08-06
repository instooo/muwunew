<?php
$global = require_once '../Conf/dbconfig.inc.php';
$man = array (
		'ADMIN_PATH' => '',
		'URL_MODEL' => '0',
		'USER_AUTH_ON' => true,
		'USER_AUTH_TYPE' => 2, // 默认认证类型 1 登录认证 2 实时认证
		'USER_AUTH_KEY' => 'user', // 用户认证SESSION标记
		'ADMIN_AUTH_KEY' => 'administrator',
		'BLACK_LIST' => 'Close', // 开启访问黑名单
		'USER_AUTH_MODEL' => 'User', // 默认验证数据表模型
		'AUTH_PWD_ENCODER' => 'md5', // 用户认证密码加密方式
		'USER_AUTH_GATEWAY' => '/Public/login', // 默认认证网关
		'NOT_AUTH_MODULE' => 'Public', // 默认无需认证模块
		'REQUIRE_AUTH_MODULE' => '', // 默认需要认证模块
		'NOT_AUTH_ACTION' => '', // 默认无需认证操作
		'REQUIRE_AUTH_ACTION' => '', // 默认需要认证操作
		'GUEST_AUTH_ON' => false, // 是否开启游客授权访问
		'GUEST_AUTH_ID' => 0, // 游客的用户ID
		'DB_LIKE_FIELDS' => 'title|remark',		
		'URL_CASE_INSENSITIVE' => true,
		'TMPL_PARSE_STRING' => array(				
				'WWW_URL' =>'http://www.muwu.me',
				'ADMIN_PATH' => 'muwu_admin',					
			),		
);
$newglobal = array_merge ( $global, $man );
return $newglobal;
?>