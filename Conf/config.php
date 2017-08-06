<?php	
	$global = require_once './Conf/dbconfig.inc.php';	//数据库链接	    
	/* 查找模板文件下所有文件名，用于分组 */
	$group_list = '';
	$dir = "./Tpl/";
	$preg2 = '/[A-Za-z]/';
	if (is_dir ( $dir )) {
		if ($dh = opendir ( $dir )) {
			while ( ($file = readdir ( $dh )) !== false ) {
				if (preg_match ( $preg2, $file )) {
					$group_list .= $file . ',';
				}
			}
			closedir ( $dh );
		}
	}
	/* --end-- */
	$app = array(
			'URL_CASE_INSENSITIVE' =>true,//URL不区分大小写			
 			'URL_ROUTER_ON' => true,
			//Session配置
		    'SESSION_TYPE'            => 'db',            //数据库存储session
		    'SESSION_TABLE'            => 'mygame_session',    //存session的表
		    'SESSION_EXPIRE'        => 600,                //session过期时间
			'COOKIE_PREFIX' => 'shouyou_',
			'SESSION_AUTO_START'=>true,
			'APP_GROUP_LIST' => $group_list, //项目分组设定
			'DEFAULT_GROUP'  => 'Template_muwu/', //默认分组			
			/* 系统数据加密设置 */
            'DATA_AUTH_KEY' => '*0o[pFqc#n/wYB:e>-dzL^vIKRH%j$6.1MJU5=9h', //默认数据加密KEY
			'USER_AUTH_ON' =>false,
			'USER_AUTH_TYPE'=>2,		// 默认认证类型 1 登录认证 2 实时认证
			'USER_AUTH_KEY' =>'authId',	// 用户认证SESSION标记
			'ADMIN_AUTH_KEY'=>'administrator',
			'USER_AUTH_MODEL'=>'Admin',	// 默认验证数据表模型
			'AUTH_PWD_ENCODER'=>'md5',	// 用户认证密码加密方式
			'NOT_AUTH_MODULE'=> '',	// 默认无需认证模块
			'REQUIRE_AUTH_MODULE'=>'',		// 默认需要认证模块
			'NOT_AUTH_ACTION'=>'',		// 默认无需认证操作
			'USER_AUTH_GATEWAY' =>  '',// 默认认证网关
			'REQUIRE_AUTH_ACTION'=>'',		// 默认需要认证操作
			'GUEST_AUTH_ON'=>false,    // 是否开启游客授权访问
			'GUEST_AUTH_ID'=>0,        // 游客的用户ID
			'STATIC_VERSION'=>'20160325',		//静态文件版本号
			'TMPL_PARSE_STRING' => array(
				'JINGTAIZHI' => 'WWW.baidu.com',
				'WWW_URL' =>'www.muwu.me'	
			),
			
			
	);

	$newglobal = array_merge($global,$app);	
	if($basic!=""){
		$newglobal_re = array_merge($basic,$newglobal);	
		return $newglobal_re; 	
    }else{    	
    	return $newglobal;
    }
?>