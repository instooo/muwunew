<?php

/**

     @function  全局控制

	 @file GlobalAction.class.php $

	 @Author shf_l@163.com 

	 @copyright (C) 2012-2013 31wan

	 @license	http://demo.31wan.cn/license

	 @lastmodify	2013-09-22

	 @version v4.0



*/

class GlobalAction extends CommonAction {

	public $config;

	public function __construct() {

		parent::_initialize ();

		parent::__construct ();

		$this->config = C ( "ADMIN_THEME" );

		parent::action_access ( MODULE_NAME, ACTION_NAME );

	}

	/**

	 *

	 * @name 角色权限设置

	 */

	public function role_priv() {

		import ( "@.ORG.Util.Category" );

		$category_list = M ( 'category_list' );

		$list = $category_list->order ( 'id asc' )->select ();

		$cat = new Category ( array (

				'id',

				'fid',

				'category',

				'cname' 

		) );

		$s = $cat->getTree ( $list ); // 获取分类数据树结构

		$roleid = $_REQUEST ['roleid'];

		$role_access = M ( 'role_access' );

		$list_role = $role_access->where ( 'role_id = ' . $roleid )->order ( 'role_id asc' )->select ();

		// 判断出是否选择

		foreach ( $s as $k => $v ) {

			foreach ( $list_role as $kk => $vv ) {

				if ($vv ['access_id'] == $v ['id']) {

					$s [$k] ['flag'] = 1;

				}

			}

		}

		$log_str = '权限设置';

		parent::admin_log ( $log_str );

		if (isset ( $_REQUEST ['dosubmit'] )) {

			$role_access->where ( 'role_id = ' . $_REQUEST ['roleid'] )->delete (); // 删除数据

			foreach ( $_REQUEST ['menuid'] as $id => $listorder ) {

				$info = $category_list->where ( 'id = ' . $listorder )->find ();

				// print_r($info);

				$conds ['access_fid'] = $info ['fid'];

				$conds ['access_id'] = $info ['id'];

				$conds ['module'] = $info ['category'];

				$conds ['access_name'] = $info ['module_alias'];

				if ($conds ['access_fid'] != 0) {

					// 子节点

					$cd ['id'] = $conds ['access_fid'];

					$category = $category_list->where ( $cd )->getField ( 'category' );

					$module = $category_list->where ( $cd )->getField ( 'module_alias' );

					$conds ['parent_module_alias'] = $category;

					$conds ['parent_module'] = $module;

				} else {

					$conds ['parent_module_alias'] = $conds ['module'];

				}

				$conds ['role_id'] = $_REQUEST ['roleid'];

				$insert = $role_access->data ( $conds )->add ();

				if (! $insert) {

					die ( mysql_error () );

					// $this->error('添加失败'.mysql_error());

				}

				unset ( $conds );

			}

			$this->success ( '添加成功', U ( 'Global/role_list' ) );

		}

		

		$this->assign ( 'list', $s );

		$this->assign ( 'roleid', $roleid );

		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/role_priv.html' );

	}

	/**

	 *

	 * @name 站点配置

	 */

	public function siteconf() {

		$conn = M ( 'webconfig' );

		$arr ['id'] = '1';

		$info = $conn->where ( $arr )->find ();

		$this->assign ( 'info', $info );

		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/siteconf.html' );

	}

	/*ajax发送短信测试*/

	public function ce_phone($phone, $app_id, $app_secret, $access_token, $dx_url,$ms,$dx_id) {

		//ms ( 0-免费 1-收费) 

		import ( "@.ORG.Net.Mobileverify" );

		$mobileverify = new Mobileverify ();

		$timestamp = date ( 'Y-m-d H:i:s' );

		if($ms==0)

		{			

			$json = $mobileverify->fs_yz ( $phone, $app_id, $app_secret, $access_token, $dx_url, $timestamp );

			$str = json_decode ( $json );

			$type = M ( 'yzdx' );

			$map = $type->create ();

			$whr ['identifier'] = array (

					'in',

					$str->identifier 

			);

			$flag = $type->where ( $whr )->delete (); // 删除数据

			if ($flag) {

				$this->ajaxReturn ( $phone, '发送成功', 1 );

			} else {

				$this->ajaxReturn ( $phone, '发送失败', 1 );

			}

		}

		else

		{

	

			$template_param = "{\"param1\":\"测试用户\",\"param2\":\"EES123\",\"param3\":\"30分钟\"}";	

			$json = $mobileverify->fs_shdx ($app_id,$access_token,$phone,$dx_id,$template_param,$timestamp,$app_secret);

			$str = json_decode ( $json );

			if($srt->res_code == 0)

			{

				$this->ajaxReturn ( $phone, '发送成功', 1 );

			}

			else

			{

				$this->ajaxReturn ( $phone, '发送失败', 1 );

			}

		}



	}

	/*ajax测试FTP是否开通*/

	function ajax_ftpopen($server_ip,$server_port,$server_username,$server_password)

	{

		//exit('ss'.$server_password);

		$zt = $this->open_ftp($server_ip,$server_port,$server_username,$server_password);

		if($zt==1)

		{

			$this->ajaxReturn ( $server_ip, '成功', 1 );

		}

		if($zt==0)

		{

			$this->ajaxReturn ( $server_ip, '失败', 1 );

		}

	}

	/**

	 *

	 * @name 添加站点

	 *       @id 站点的id

	 */

	public function add_web() {

		$str = '';

		$dir = ROOT_PATH."/www/Tpl/";

		$preg2 = '/[A-Za-z]/';

		if (is_dir ( $dir )) {

			if ($dh = opendir ( $dir )) {

				while ( ($file = readdir ( $dh )) !== false ) {

					if (preg_match ( $preg2, $file )) {

						$str .= '<option value=' . $file . '>' . $file . '</option>';

					}

				}

				closedir ( $dh );

			}

		}

		$this->assign ( 'file_moban', $str );

		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/web_add.html' );

	}

	/**

	 *

	 * @name 更新站点配置

	 */

	public function save_basic() {

		import ( "@.ORG.Net.UploadFile" );

		$arr ['id'] = $_REQUEST ['info_id'];

		

		$server_string = $_REQUEST ['server_ip'] . '#' . $_REQUEST ['server_port'] . '#' . $_REQUEST ['server_username'] . '#' . $_REQUEST ['server_password'] . '#' . $_REQUEST ['server_open'];

		if (! empty ( $_FILES ["photo1"] ["name"] )) {

			$list_img = $_FILES ["photo1"];

			$ima_name1 = $this->ftp_image_com ( $list_img, 'Logo', 'wu', $server_string );

			if($ima_name1==1)

			{

				$this->error('上传的图片超过2M!');

				

			}

			if($ima_name1==2)

			{

				$this->error('上传的是必须是图片');

				

			}

			$map ['favicon'] = $ima_name1;

		}

		if (! empty ( $_FILES ["photo2"] ["name"] )) {

			$list_img = $_FILES ["photo2"];

			$ima_name2 = $this->ftp_image_com ( $list_img, 'Logo', 'wu', $server_string );

			if($ima_name2==1)

			{

				$this->error('上传的图片超过2M!');

				

			}

			if($ima_name2==2)

			{

				$this->error('上传的是必须是图片');

				

			}

			$map ['logo'] = $ima_name2;

		}

		if (! empty ( $_FILES ["photo3"] ["name"] )) {

			$list_img = $_FILES ["photo3"];

			$ima_name3 = $this->ftp_image_com ( $list_img, 'Logo', 'wu', $server_string );

			if($ima_name3==1)

			{

				$this->error('上传的图片超过2M!');

				

			}

			if($ima_name3==2)

			{

				$this->error('上传的是必须是图片');

				

			}

			$map ['watermark'] = $ima_name3;

		}

		

		$map ['sitename'] = trim ( $_REQUEST ['sitename'] );

		$map ['sitename2'] = trim ( $_REQUEST ['sitename2'] );

		$map ['domain'] = trim ( $_REQUEST ['domain'] );

		$map ['keywords'] = trim ( $_REQUEST ['keywords'] );

		$map ['descriptions'] = trim ( $_REQUEST ['descriptions'] );

		$map ['url_model'] = $_REQUEST ['urlmode'];

		$map ['url_html_suffix'] = $_REQUEST ['suffix'];

		$map ['open'] = $_REQUEST ['open'];

		$map ['openreg'] = $_REQUEST ['openreg'];

		$map ['gzip'] = $_REQUEST ['gzip'];

		

		$map ['uc_api'] = $_REQUEST ['uc_api'];

		$map ['uc_password'] = $_REQUEST ['uc_password'];

		$map ['uc_key'] = $_REQUEST ['uc_key'];

		$map ['mail_password'] = $_REQUEST ['mail_password'];

		$map ['mail_user'] = $_REQUEST ['mail_user'];

		$map ['mail_from'] = $_REQUEST ['mail_from'];

		$map ['mail_port'] = $_REQUEST ['mail_port'];

		$map ['mail_server'] = $_REQUEST ['mail_server'];

		$map ['appkey'] = $_REQUEST ['appkey'];

		

		$map ['appid'] = $_REQUEST ['appid'];

		$map ['is_sdk'] = $_REQUEST ['is_sdk'];

		$map ['social_login'] = $_REQUEST ['social_login'];

		$map ['maxloginfailedtimes'] = $_REQUEST ['maxloginfailedtimes'];

		$map ['max_error'] = $_REQUEST ['max_error'];

		$map ['save_errorlog'] = $_REQUEST ['save_errorlog'];

		$map ['save_uselog'] = $_REQUEST ['save_uselog'];

		$map ['is_html'] = $_REQUEST ['is_html'];

		$map ['icp'] = $_REQUEST ['icp'];

		$map ['wenww'] = $_REQUEST ['wenww'];

		$map ['beian'] = $_REQUEST ['beian'];

		$map ['tj'] = stripslashes ( $_REQUEST ['tj'] );

		$map ['fujian_ip'] = $_REQUEST ['fujian_ip'];

		$map ['delay_time'] = $_REQUEST ['delay_time'];

		$map ['server_open'] = $_REQUEST ['server_open'];

		$map ['server_ip'] = $_REQUEST ['server_ip'];

		$map ['server_port'] = $_REQUEST ['server_port'];

		$map ['server_username'] = $_REQUEST ['server_username'];

		$map ['server_password'] = $_REQUEST ['server_password'];

		$map ['theme'] = $_REQUEST ['theme'];

		$map ['isduanxin'] = $_REQUEST ['isduanxin'];

		$map ['app_id'] = $_REQUEST ['app_id'];

		$map ['app_secret'] = $_REQUEST ['app_secret'];

		$map ['access_token'] = $_REQUEST ['access_token'];

		$map ['dx_url'] = $_REQUEST ['dx_url'];

		$map ['isweixin'] = $_REQUEST ['isweixin'];

		$map ['token'] = $_REQUEST ['token'];

		$map ['is_score'] = $_REQUEST ['is_score'];

		$map ['is_recharge'] = $_REQUEST ['is_recharge'];

		$map ['ucconfig'] = stripslashes ( $_REQUEST ['ucconfig'] );

		$map ['is_fsdxms'] = $_REQUEST ['is_fsdxms'];

		$map ['dx_id'] = $_REQUEST ['dx_id'];

		$map ['popularize_name'] = $_REQUEST ['popularize_name'];//默认推广帐号		

		$map ['is_recycler'] = $_REQUEST ['is_recycler'];//是否开启回收站

		$map ['service_hotline'] = $_REQUEST ['service_hotline'];//客服热线

		$map ['service_email'] = $_REQUEST ['service_email'];//客服邮箱

		$map ['service_QQ'] = $_REQUEST ['service_QQ'];//是客服QQ

		

		

		$conn = M ( 'webconfig' );

		$dostr = explode ( '.', $map ['domain'] );

		$dir = $_SERVER ['DOCUMENT_ROOT'];

		if (! $dir . '/Conf/' . $dostr ['0'] . '/' . 'config.inc.php') {

			$this->write_file ( $dir . '/Conf/' . $dostr ['0'] . '/' . 'config.inc.php', '<?php ' . $map ['ucconfig'] );

		}

		

		$data_re ['theme'] = $map ['theme'];

		$data_re ['url_html_suffix'] = $map ['url_html_suffix'];

		$data_re ['is_html'] = $map ['is_html'];

		$data_re ['url_model'] = $map ['url_model'];

		$map ['is_watermark'] = $_REQUEST ['is_watermark'];

		

		$data = array_change_key_case ( $data_re, CASE_UPPER );

		

		

/* 	 	$sdsd['APP_SUB_DOMAIN_RULES']=array(

				'www'=>array('Template_www/'),  // djj域名指向djj分组

				'test4'=>array('Template_djj/'),  // jlc域名指向jlc分组

		);

	 	print_r($sdsd);exit; */

		$data['APP_SUB_DOMAIN_RULES'] =array(

				"{$dostr ['0']}"=>array($map['theme'].'/'),  // jlc域名指向jlc分组

		); 

		

		

		$dir = $_SERVER ['DOCUMENT_ROOT'];

		

		/*

		 * //腾讯QQ登录配置 'THINK_SDK_QQ' => array( 'APP_KEY' => '100395853', //应用注册成功后分配的 APP ID 'APP_SECRET' => '539feea20d6fd2befbf6a9c32710db8a', //应用注册成功后分配的KEY 'CALLBACK' => URL_CALLBACK . 'qq', ), //腾讯微博配置 'THINK_SDK_TENCENT' => array( 'APP_KEY' => '', //应用注册成功后分配的 APP ID 'APP_SECRET' => '', //应用注册成功后分配的KEY 'CALLBACK' => URL_CALLBACK . 'tencent', ),

		 */

		if ($_REQUEST ['is_sdk'] == '1') {

			

			$arr_tp ['THINK_SDK_QQ'] = array (

					'APP_KEY' => $_REQUEST ['appid1'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey1'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=qq' 

			);

			$arr_tp ['THINK_SDK_TENCENT'] = array (

					'APP_KEY' => $_REQUEST ['appid2'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey2'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=tencnt' 

			);

			$arr_tp ['THINK_SDK_SINA'] = array (

					'APP_KEY' => $_REQUEST ['appid3'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey3'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=sina' 

			);

			$arr_tp ['THINK_SDK_T163'] = array (

					'APP_KEY' => $_REQUEST ['appid4'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey4'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=t163' 

			);

			$arr_tp ['THINK_SDK_RENREN'] = array (

					'APP_KEY' => $_REQUEST ['appid5'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey5'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=renren' 

			);

			$arr_tp ['THINK_SDK_X360'] = array (

					'APP_KEY' => $_REQUEST ['appid6'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey6'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=x360' 

			);

			$arr_tp ['THINK_SDK_DOUBAN'] = array (

					'APP_KEY' => $_REQUEST ['appid7'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey7'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=douban' 

			);

			$arr_tp ['THINK_SDK_GITHUB'] = array (

					'APP_KEY' => $_REQUEST ['appid8'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey8'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=github' 

			);

			$arr_tp ['THINK_SDK_GOOGLE'] = array (

					'APP_KEY' => $_REQUEST ['appid9'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey9'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=google' 

			);

			$arr_tp ['THINK_SDK_MSN'] = array (

					'APP_KEY' => $_REQUEST ['appid10'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey10'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=msn' 

			);

			

			$arr_tp ['THINK_SDK_DIANDIAN'] = array (

					'APP_KEY' => $_REQUEST ['appid11'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey11'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=diandian' 

			);

			

			$arr_tp ['THINK_SDK_TAOBAO'] = array (

					'APP_KEY' => $_REQUEST ['appid12'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey12'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=taobao' 

			);

			

			$arr_tp ['THINK_SDK_BAIDU'] = array (

					'APP_KEY' => $_REQUEST ['appid13'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey13'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=baidu' 

			);

			

			$arr_tp ['THINK_SDK_KAIXIN'] = array (

					'APP_KEY' => $_REQUEST ['appid14'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey14'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=kaixin' 

			);

			

			$arr_tp ['THINK_SDK_SOHU'] = array (

					'APP_KEY' => $_REQUEST ['appid15'], // 应用注册成功后分配的 APP ID

					'APP_SECRET' => $_REQUEST ['appkey15'], // 应用注册成功后分配的KEY

					'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=sohu' 

			);

			$newglobal = array_merge ( $data, $arr_tp );

			//F ( 'basic', $newglobal, $dir . '/Conf/' . $dostr ['0'] . '/' );

		} else {

			//F ( 'basic', $data, $dir . '/Conf/' . $dostr ['0'] . '/' );

		}

		

		$st = $conn->where ( $arr )->data ( $map )->save ();

		if ($st !== false) {

			/* 配置sdk */

			$conn1 = M ( 'websdk' );

			$arrlist = array (

					'',

					'qq',

					'tencent',

					'sina',

					't163',

					'renren',

					'x360',

					'douban',

					'github',

					'google',

					'msn',

					'diandian',

					'taobao',

					'baidu',

					'kaixin',

					'sohu' 

			);

			for($i = 1; $i < 16; $i ++) {

				

				$bust ['webconfig_id'] = $_REQUEST ['info_id'];

				$bust ['type'] = $arrlist [$i];

				$mapstr ['appid'] = $_REQUEST ['appid' . $i];

				$mapstr ['appkey'] = $_REQUEST ['appkey' . $i];

				$mapstr ['is_open'] = $_REQUEST ['is_open' . $i];

				$conn1->where ( $bust )->data ( $mapstr )->save ();

				// exit($conn1->getLastSql());

				unset ( $bust );

			}

			/* 配置sdk */

			

			$this->success ( "成功!", U ( 'Global/weblist' ) );

		} else {

			/* 图片上传 */

			$this->success ( "失败!", U ( 'Global/weblist' ) );

		}

	}

	

	/**

	 *

	 * @name 添加站点配置

	 */

	public function save_basic_add() {

		$server_string = $_REQUEST ['server_ip'] . '#' . $_REQUEST ['server_port'] . '#' . $_REQUEST ['server_username'] . '#' . $_REQUEST ['server_password'] . '#' . $_REQUEST ['server_open'];

		if (! empty ( $_FILES ["photo1"] ["name"] )) {

			$list_img = $_FILES ["photo1"];

			$ima_name1 = $this->ftp_image_com ( $list_img, 'Logo', 'wu', $server_string );

			if($ima_name1==1)

			{

				$this->error('上传的图片超过2M!');

				

			}

			if($ima_name1==2)

			{

				$this->error('上传的是必须是图片');

				

			}

			$map ['favicon'] = $ima_name1;

		}

		if (! empty ( $_FILES ["photo2"] ["name"] )) {

			$list_img = $_FILES ["photo2"];

			$ima_name2 = $this->ftp_image_com ( $list_img, 'Logo', 'wu', $server_string );

			if($ima_name2==1)

			{

				$this->error('上传的图片超过2M!');

				

			}

			if($ima_name2==2)

			{

				$this->error('上传的是必须是图片');

				

			}

			$map ['logo'] = $ima_name2;

		}

		if (! empty ( $_FILES ["photo3"] ["name"] )) {

			$list_img = $_FILES ["photo3"];

			$ima_name3 = $this->ftp_image_com ( $list_img, 'Logo', 'wu', $server_string );

			if($ima_name3==1)

			{

				$this->error('上传的图片超过2M!');

				

			}

			if($ima_name3==2)

			{

				$this->error('上传的是必须是图片');

				

			}

			$map ['watermark'] = $ima_name3;

		}

		

		import ( "@.ORG.Net.UploadFile" );

		

		$map ['sitename'] = trim ( $_REQUEST ['sitename'] );

		$map ['sitename2'] = trim ( $_REQUEST ['sitename2'] );

		$map ['domain'] = trim ( $_REQUEST ['domain'] );

		$map ['keywords'] = trim ( $_REQUEST ['keywords'] );

		$map ['descriptions'] = trim ( $_REQUEST ['descriptions'] );

		$map ['url_model'] = $_REQUEST ['urlmode'];

		$map ['url_html_suffix'] = $_REQUEST ['suffix'];

		$map ['open'] = $_REQUEST ['open'];

		$map ['openreg'] = $_REQUEST ['openreg'];

		

		$map ['uc_api'] = $_REQUEST ['uc_api'];

		$map ['mail_password'] = $_REQUEST ['mail_password'];

		$map ['mail_user'] = $_REQUEST ['mail_user'];

		$map ['mail_from'] = $_REQUEST ['mail_from'];

		$map ['mail_port'] = $_REQUEST ['mail_port'];

		$map ['mail_server'] = $_REQUEST ['mail_server'];

		$map ['is_sdk'] = $_REQUEST ['is_sdk'];

		$map ['max_error'] = $_REQUEST ['max_error'];

		$map ['save_uselog'] = $_REQUEST ['save_uselog'];

		$map ['is_html'] = $_REQUEST ['is_html'];

		$map ['icp'] = $_REQUEST ['icp'];

		$map ['wenww'] = $_REQUEST ['wenww'];

		$map ['beian'] = $_REQUEST ['beian'];

		$map ['fujian_ip'] = $_REQUEST ['fujian_ip'];

		$map ['tj'] = stripslashes ( $_REQUEST ['tj'] );

		$map ['server_open'] = $_REQUEST ['server_open'];

		$map ['server_ip'] = $_REQUEST ['server_ip'];

		$map ['server_port'] = $_REQUEST ['server_port'];

		$map ['server_username'] = $_REQUEST ['server_username'];

		$map ['server_password'] = $_REQUEST ['server_password'];

		$map ['theme'] = $_REQUEST ['theme'];

		$map ['isduanxin'] = $_REQUEST ['isduanxin'];

		$map ['app_id'] = $_REQUEST ['app_id'];

		$map ['app_secret'] = $_REQUEST ['app_secret'];

		$map ['access_token'] = $_REQUEST ['access_token'];

		$map ['dx_url'] = $_REQUEST ['dx_url'];

		$map ['isweixin'] = $_REQUEST ['isweixin'];

		$map ['token'] = $_REQUEST ['token'];

		$map ['is_score'] = $_REQUEST ['is_score'];

		$map ['is_recharge'] = $_REQUEST ['is_recharge'];

		$map ['ucconfig'] = stripslashes ( $_REQUEST ['ucconfig'] );

		$map ['is_watermark'] = $_REQUEST ['is_watermark'];

		$map ['popularize_name'] = $_REQUEST ['popularize_name'];//默认推广帐号		

		$map ['is_recycler'] = $_REQUEST ['is_recycler'];//是否开启回收站

		$map ['service_hotline'] = $_REQUEST ['service_hotline'];//客服热线

		$map ['service_email'] = $_REQUEST ['service_email'];//客服邮箱

		$map ['service_QQ'] = $_REQUEST ['service_QQ'];//是客服QQ

		

		

		$dostr = explode ( '.', $map ['domain'] );

		$dir = $_SERVER ['DOCUMENT_ROOT'];

		if (! $dir . '/Conf/' . $dostr ['0'] . '/' . 'config.inc.php') {

			//$this->write_file ( $dir . '/Conf/' . $dostr ['0'] . '/' . 'config.inc.php', '<?php ' . $map ['ucconfig'] );

		}

		

		$conn = M ( 'webconfig' );

		$st = $conn->add ( $map );

		if (! $st) {

			$this->error ( "添加错误:" . mysql_error (), U ( 'Global/weblist' ) );

		} else {

			

			/* 配置sdk */

			$conn1 = M ( 'websdk' );

			$arrlist = array (

					'',

					'qq',

					'tencent',

					'sina',

					't163',

					'renren',

					'x360',

					'douban',

					'github',

					'google',

					'msn',

					'diandian',

					'taobao',

					'baidu',

					'kaixin',

					'sohu' 

			);

			for($i = 1; $i < 16; $i ++) {

				$mapstr ['webconfig_id'] = $st;

				$mapstr ['appid'] = $_REQUEST ['appid' . $i];

				$mapstr ['appkey'] = $_REQUEST ['appkey' . $i];

				$mapstr ['is_open'] = $_REQUEST ['is_open' . $i];

				$mapstr ['type'] = $arrlist [$i];

				$conn1->add ( $mapstr );

			}

			

			$data_re ['theme'] = $map ['theme'];

			$data_re ['url_html_suffix'] = $map ['url_html_suffix'];

			$data_re ['is_html'] = $map ['is_html'];

			$data_re ['url_model'] = $map ['url_model'];

			$data = array_change_key_case ( $data_re, CASE_UPPER );

			

			$data['APP_SUB_DOMAIN_RULES'] =array(

					"{$dostr ['0']}"=>array($map['theme'].'/'),  // jlc域名指向jlc分组

			);

			

			$dir = $_SERVER ['DOCUMENT_ROOT'];

			if ($_REQUEST ['is_sdk'] == '1') {

				

				$arr_tp ['THINK_SDK_QQ'] = array (

						'APP_KEY' => $_REQUEST ['appid1'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey1'], // 应用注册成功后分配的KEY

						'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=qq' 

				);

				$arr_tp ['THINK_SDK_TENCENT'] = array (

						'APP_KEY' => $_REQUEST ['appid2'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey2'], // 应用注册成功后分配的KEY

						'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=tencnt' 

				);

				$arr_tp ['THINK_SDK_SINA'] = array (

						'APP_KEY' => $_REQUEST ['appid3'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey3'], // 应用注册成功后分配的KEY

						'CALLBACK' =>'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=sina' 

				);

				$arr_tp ['THINK_SDK_T163'] = array (

						'APP_KEY' => $_REQUEST ['appid4'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey4'], // 应用注册成功后分配的KEY

						'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=t163' 

				);

				$arr_tp ['THINK_SDK_RENREN'] = array (

						'APP_KEY' => $_REQUEST ['appid5'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey5'], // 应用注册成功后分配的KEY

						'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=renren' 

				);

				$arr_tp ['THINK_SDK_X360'] = array (

						'APP_KEY' => $_REQUEST ['appid6'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey6'], // 应用注册成功后分配的KEY

						'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=x360' 

				);

				$arr_tp ['THINK_SDK_DOUBAN'] = array (

						'APP_KEY' => $_REQUEST ['appid7'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey7'], // 应用注册成功后分配的KEY

						'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=douban' 

				);

				$arr_tp ['THINK_SDK_GITHUB'] = array (

						'APP_KEY' => $_REQUEST ['appid8'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey8'], // 应用注册成功后分配的KEY

						'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=github' 

				);

				$arr_tp ['THINK_SDK_GOOGLE'] = array (

						'APP_KEY' => $_REQUEST ['appid9'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey9'], // 应用注册成功后分配的KEY

						'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=google' 

				);

				$arr_tp ['THINK_SDK_MSN'] = array (

						'APP_KEY' => $_REQUEST ['appid10'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey10'], // 应用注册成功后分配的KEY

						'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=msn' 

				);

				

				$arr_tp ['THINK_SDK_DIANDIAN'] = array (

						'APP_KEY' => $_REQUEST ['appid11'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey11'], // 应用注册成功后分配的KEY

						'CALLBACK' =>'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=diandian' 

				);

				

				$arr_tp ['THINK_SDK_TAOBAO'] = array (

						'APP_KEY' => $_REQUEST ['appid12'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey12'], // 应用注册成功后分配的KEY

						'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=taobao' 

				);

				

				$arr_tp ['THINK_SDK_BAIDU'] = array (

						'APP_KEY' => $_REQUEST ['appid13'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey13'], // 应用注册成功后分配的KEY

						'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=baidu' 

				);

				

				$arr_tp ['THINK_SDK_KAIXIN'] = array (

						'APP_KEY' => $_REQUEST ['appid14'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey14'], // 应用注册成功后分配的KEY

						'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=kaixin' 

				);

				

				$arr_tp ['THINK_SDK_SOHU'] = array (

						'APP_KEY' => $_REQUEST ['appid15'], // 应用注册成功后分配的 APP ID

						'APP_SECRET' => $_REQUEST ['appkey15'], // 应用注册成功后分配的KEY

						'CALLBACK' => 'http://'.$_SERVER['HTTP_HOST'].'/open/callback?type=sohu' 

				);

				$newglobal = array_merge ( $data, $arr_tp );

				F ( 'basic', $newglobal, $dir . '/Conf/' . $dostr ['0'] . '/' );

			} else {

				F ( 'basic', $data, $dir . '/Conf/' . $dostr ['0'] . '/' );

			}

			

			/* 配置sdk */

			$this->success ( "站点添加成功!", U ( 'Global/weblist' ) );

		}

	}

	

	// 写入文件

	private function write_file($l1, $l2 = '') {

		$dir = dirname ( $l1 );

		

		if (! is_dir ( $dir )) {

			

			mkdirss ( $dir );

		}

		

		return @file_put_contents ( $l1, $l2 );

	}

	

	// 递归创建文件

	private function mkdirss($dirs, $mode = 0777) {

		if (! is_dir ( $dirs )) {

			

			mkdirss ( dirname ( $dirs ), $mode );

			

			return @mkdir ( $dirs, $mode );

		}

		

		return true;

	}

	

	/**

	 *

	 * @name 管理员管理

	 */

	public function manager_list() {

		$conn = M ( 'manager' );

		$admin_role = M ( "admin_role" ); // 实例化admin_role对象

		import ( '@.ORG.Util.Page' );

		$count = $conn->order ( 'roleid desc' )->count ();

		$p = new Page ( $count, 20 );

		$list = $conn->order ( 'roleid desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();

		$p->setConfig ( 'prev', '上一页' );		

		$p->setConfig ( 'first', '首 页' );

		$p->setConfig ( 'last', '末 页' );

		$p->setConfig ( 'next', '下一页' );

		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%

			<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>篇文章 20篇/每页</span></li>" );

		$this->assign ( 'page', $p->show () );

		

		foreach ( $list as $k => $v ) {

			$infog = $admin_role->where ( "roleid = " . $v ['roleid'] )->find ();

			$list [$k] ['rolename'] = $infog ['rolename'];

		}

		$this->assign ( 'list', $list );

		$role_list = $admin_role->order ( 'roleid desc' )->select ();

		$this->assign ( 'role_list', $role_list );

		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/manager_list.html' );

	}

	/**

	 *

	 * @name 增加角色

	 */

	public function manager_add() {

		$log_str = '增加角色';

		parent::admin_log ( $log_str );

		$conn = M ( 'manager' );

		$conn1 = M ( 'webconfig' );

		

		$admin_role = M ( "admin_role" ); // 实例化admin_role对象

		if ($_REQUEST ['dosubmit']) {

			$map ['username'] = trim ( $_REQUEST ['username'] );

			

			$map ['email'] = trim ( $_REQUEST ['email'] );

			$map ['roleid'] = trim ( $_REQUEST ['roleid'] );

			$fruit = $_REQUEST ['fruit'];

			$map ['fruit'] = '';

			for($i = 0; $i < count ( $fruit ); $i ++) {

				if ($i > 0) {

					$map ['fruit'] .= ',';

				}

				$map ['fruit'] .= $fruit [$i];

			}

			// exit('lh:'.$map ['fruit']);

			

			if (! preg_match ( "/^([a-zA-Z0-9]|[._]){5,22}$/", $map ['username'] )) {

				$this->error ( '您提交的数据有误:用户名非法,请检查您的输入!' );

			}

			if (strlen ( $_REQUEST ['password'] ) < 6 || strlen ( $_REQUEST ['password'] ) > 22 || $_REQUEST ['password'] == "") {

				$this->error ( '您提交的数据有误:密码长度为6到22位的字符,请检查您的输入!' );

			}

			$map ['password'] = MD5 ( trim ( $_REQUEST ['password'] ) );

			if (! preg_match ( "/\b(^(\S+@).+((\.com)|(\.net)|(\.org)|(\.info)|(\.edu)|(\.mil)|(\.gov)|(\.biz)|(\.ws)|(\.us)|(\.tv)|(\.cc)|(\..{2,2}))$)\b/", $map ['email'] )) {

				$this->error ( '您提交的数据有误:邮箱格式不正确,请检查您的输入!' );

			}

			

			$flag = $conn->where ( "username='" . $map ['username'] . "'" )->getField ( 'uid' );

			

			if ($flag) {

				$this->error ( "管理员已存在！" );

			} else {

				

				if ($conn->add ( $map )) {

					$this->success ( "管理员添加成功!", U ( 'Global/manager_list' ) );

				} else {

					$this->error ( "发生错误:" . mysql_error (), U ( 'Global/manager_list' ) );

				}

			}

		}

		$role_list = $admin_role->order ( 'roleid desc' )->select ();

		$ma_list = $conn1->select ();

		$this->assign ( 'ma_list', $ma_list );

		$this->assign ( 'role_list', $role_list );

		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/manager_add.html' );

	}

	/**

	 *

	 * @name 角色信息修改该

	 */

	public function manager_edit() {

		$log_str = '角色修改';

		parent::admin_log ( $log_str );

		$conn = M ( 'manager' );

		$conn1 = M ( 'webconfig' );

		$ma_l = $conn1->select ();

		

		$admin_role = M ( "admin_role" ); // 实例化admin_role对象

		$arr ['uid'] = $_REQUEST ['uid'];

		$info = $conn->where ( $arr )->find ();

		$fg = explode ( ',', $info ['fruit'] );

		for($i = 0; $i < count ( $ma_l ); $i ++) {

			$zt = '';

			for($j = 0; $j < count ( $fg ); $j ++) {

				if ($fg [$j] == $ma_l [$i] ['id']) {

					$zt = 'checked';

				}

			}

			$dm .= '<input type="checkbox" name="fruit[]" value ="' . $ma_l [$i] ['id'] . '" ' . $zt . '>' . $ma_l [$i] ['sitename'] . '<br>';

		}

		

		$this->assign ( 'info', $info );

		

		$this->assign ( 'uid', $arr ['uid'] );

		if ($_REQUEST ['dosubmit']) {

			

			if ($_REQUEST ['password']) {

				$map ['password'] = md5 ( trim ( $_REQUEST ['password'] ) );

			}

			$map ['email'] = trim ( $_REQUEST ['email'] );

			$map ['status'] = trim ( $_REQUEST ['status'] );

			$map ['roleid'] = trim ( $_REQUEST ['roleid'] );

			$arr1 ['uid'] = $_REQUEST ['uid'];

			$fruit = $_REQUEST ['fruit'];

			$map ['fruit'] = '';

			for($i = 0; $i < count ( $fruit ); $i ++) {

				if ($i > 0) {

					$map ['fruit'] .= ',';

				}

				$map ['fruit'] .= $fruit [$i];

			}

			$st = $conn->where ( $arr1 )->save ( $map ); // 根据条件保存修改的数据

			                                             // echo $conn->getLastSql();

			if ($st) {

				$this->success ( "修改成功!", U ( 'Global/manager_list' ) );

			} else {

				$this->error ( "发生错误:" . mysql_error (), U ( 'Global/manager_list' ) );

			}

		}

		$role_list = $admin_role->order ( 'roleid desc' )->select ();

		

		$this->assign ( 'xian_shi', $dm );

		$this->assign ( 'role_list', $role_list );

		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/manager_edit.html' );

	}

	/**

	 *

	 * @name 删除管理员

	 */

	public function manager_delete() {

		$log_str = '删除管理员';

		parent::admin_log ( $log_str );

		$uid = $_REQUEST ['uid'];

		$uids = implode ( ',', $uid ); // 批量获取gid

		$id = is_array ( $uid ) ? $uids : $uid;

		if ($id == "1") {

			if ($_SESSION ['user'] != "admin") {

				$this->error ( '不能删除管理员账户,您没有权限' );

			}

		}

		$map ['uid'] = array (

				'in',

				$id 

		);

		if (! $uid) {

			$this->error ( '请勾选记录!', 1 );

		}

		$manager = M ( "manager" ); // 实例化game对象

		$flag = $manager->where ( $map )->delete (); // 删除数据

		if ($flag) {

			$this->success ( "管理员删除成功!", U ( 'Global/manager_list' ) );

		} else {

			$this->error ( "发生错误:" . mysql_error (), U ( 'Global/manager_list' ) );

		}

	}

	/**

	 *

	 * @name 角色管理

	 */

	public function role_list() {

		$admin_role = M ( "admin_role" ); // 实例化admin_role对象

		import ( '@.ORG.Util.Page' );

		$count = $admin_role->order ( 'roleid desc' )->count ();

		$p = new Page ( $count, 20 );

		$list = $admin_role->order ( 'roleid desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();

		$p->setConfig ( 'prev', '上一页' );		

		$p->setConfig ( 'first', '首 页' );

		$p->setConfig ( 'last', '末 页' );

		$p->setConfig ( 'next', '下一页' );

		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%

			<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>篇文章 20篇/每页</span></li>" );

		$this->assign ( 'page', $p->show () );

		$this->assign ( 'list', $list );

		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/role_list.html' );

	}

	/**

	 *

	 * @name 站点管理

	 */

	public function weblist() {

		import ( '@.ORG.Util.Page' );

		$admin_role = M ( "webconfig" ); // 实例化admin_role对象

		if ($_SESSION ['group'] == '超级管理员') {

			$count = $admin_role->order ( 'id asc' )->count (); // 超级管理员查询所有的

		} else {

			$map ['id'] = $_SESSION ['qzpf_zd'];

			$count = $admin_role->where ( $map )->order ( 'id desc' )->count (); // 查询自己所管理的

		}

		

		$p = new Page ( $count, 20 );

		

		if ($_SESSION ['group'] == '超级管理员') {

			$list = $admin_role->order ( 'id asc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();

		} else {

			$map ['id'] = $_SESSION ['qzpf_zd'];

			$list = $admin_role->where ( $map )->order ( 'id desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();

		}

		

		$p->setConfig ( 'prev', '上一页' );

		$p->setConfig ( 'header', '区服' );

		$p->setConfig ( 'first', '首 页' );

		$p->setConfig ( 'last', '末 页' );

		$p->setConfig ( 'next', '下一页' );

		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%

			<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>篇文章 20篇/每页</span></li>" );

		$this->assign ( 'page', $p->show () );

		

		

		foreach ( $list as $k => $v ) {

			if($k==0)

			{

				$list [$k] ['sh_pd'] = '1';

			}

			

			

		}

		

		$this->assign ( 'list', $list );

		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/web_list.html' );

	}

	/**

	 *

	 * @name 增加角色

	 */

	public function role_add() {

		$log_str = '增加角色';

		parent::admin_log ( $log_str );

		if ($_REQUEST ['dosubmit']) {

			$map ['rolename'] = trim ( $_REQUEST ['rolename'] );

			$map ['description'] = trim ( $_REQUEST ['description'] );

			$map ['disabled'] = trim ( $_REQUEST ['disabled'] );

			

			$admin_role = M ( "admin_role" ); // 实例化admin_role对象

			$flag = $admin_role->where ( 'rolename=' . $map ['rolename'] )->getField ( 'rolename' );

			if ($flag) {

				$this->error ( "角色已存在！" );

			}

			if ($admin_role->add ( $map )) {

				$this->success ( "角色添加成功!", U ( 'Global/role_list' ) );

			} else {

				$this->error ( "发生错误:" . mysql_error (), U ( 'Global/role_list' ) );

			}

		}

		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/role_add.html' );

	}

	

	/**

	 *

	 * @name 角色信息修改

	 */

	public function role_edit() {

		$log_str = '角色修改';

		parent::admin_log ( $log_str );

		$conn = M ( 'admin_role' );

		$arr ['roleid'] = $_REQUEST ['roleid'];

		if ($_REQUEST ['roleid'] == "1") {

			if ($_SESSION ['group'] != "超级管理员") {

				$this->error ( '您没有权限编辑超级管理员组' );

			}

		}

		$info = $conn->where ( $arr )->find ();

		$this->assign ( 'info', $info );

		$this->assign ( 'roleid', $arr ['roleid'] );

		

		$model = M ( 'category_list' );

		$maps ['fid'] = "0";

		$list = $model->where ( $maps )->select ();

		$this->assign ( 'list', $list );

		if ($_REQUEST ['dosubmit']) {

			

			$map ['disabled'] = trim ( $_REQUEST ['disabled'] );

			$map ['description'] = trim ( $_REQUEST ['description'] );

			$arr1 ['roleid'] = trim ( $_REQUEST ['roleid'] );

			$st = $conn->where ( $arr1 )->save ( $map ); // 根据条件保存修改的数据

			if ($st) {

				$this->success ( "修改成功!", U ( 'Global/role_list' ) );

			} else {

				$this->error ( "发生错误:" . mysql_error (), U ( 'Global/role_list' ) );

			}

		}

		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/role_edit.html' );

	}

	/**

	 *

	 * @name 站点信息修改

	 */

	public function web_edit() {

		$log_str = '站点修改';

		$conn = M ( 'webconfig' );

		$arr ['id'] = $_REQUEST ['web_id'];

		$sid ['webconfig_id'] = $_REQUEST ['web_id'];

		$info = $conn->where ( $arr )->find ();

		

		$websdk = M ( 'websdk' );

		$listtext = array (

				'腾讯QQ登录配置',

				'腾讯微博配置',

				'新浪微博配置',

				'网易微博配置',

				'人人网配置',

				'360配置',

				'豆瓣配置',

				'Github配置',

				'Google配置',

				'MSN配置',

				'点点配置',

				'淘宝网配置',

				'百度配置',

				'开心网配置',

				'搜狐微博配置' 

		);

		$sdklist = $websdk->where ( $sid )->select ();

		foreach ( $sdklist as $k => $v ) {

			$sdklist [$k] ['web_txt'] = $listtext [$k];

		}

		

		/* 查找文件下所有文件名 */

		$str = '';

		$dir = ROOT_PATH."/www/Tpl/";

		$preg2 = '/[A-Za-z]/';

		if (is_dir ( $dir )) {

			if ($dh = opendir ( $dir )) {

				while ( ($file = readdir ( $dh )) !== false ) {

					if (preg_match ( $preg2, $file )) {

						$zt = '';

						if ($info ['theme'] == $file) {

							$zt = 'selected';

						}

						$str .= '<option value=' . $file . ' ' . $zt . '>' . $file . '</option>';

					}

				}

				closedir ( $dh );

			}

		}

		

/*		if(file_exists('../'.$info['favicon']) != true)

		{			

			$info['favicon'] = 'Public/images/moren.jpg';

		}*/

		

		

		$this->assign ( 'file_moban', $str );

		$this->assign ( 'info', $info );

		$this->assign ( 'ser_http', $ser_http );

		$this->assign ( 'sdklist', $sdklist );

		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/web_edit.html' );

	}

	/**

	 *

	 * @name 删除角色

	 */

	public function role_delete() {

		$log_str = '删除角色';

		parent::admin_log ( $log_str );

		$roleid = $_REQUEST ['roleid'];

		$roleids = implode ( ',', $roleid ); // 批量获取gid

		

		$id = is_array ( $roleid ) ? $roleids : $roleid;

		if ($id == "1") {

			if ($_SESSION ['user'] != "admin") {

				$this->error ( '您没有权限删除超级管理员组' );

			}

		}

		$map ['roleid'] = array (

				'in',

				$id 

		);

		if (! $roleid) {

			$this->error ( '请勾选记录!', 1 );

		}

		$admin_role = M ( "admin_role" ); // 实例化game对象

		$flag = $admin_role->where ( $map )->delete (); // 删除数据

		if ($flag) {

			$this->success ( "角色删除成功!", U ( 'Global/role_list' ) );

		} else {

			$this->error ( "发生错误:" . mysql_error (), U ( 'Global/role_list' ) );

		}

	}

	/**

	 *

	 * @name 删除web站点

	 */

	public function web_delete() {

		$log_str = '删除站点';

		parent::admin_log ( $log_str );

		$roleid = $_REQUEST ['web_id'];

		

		

		$webconfig = M ( "webconfig" ); // 实例化game对象

		$category = M ( 'category' );//栏目

		$notice = M ( 'notice' );//公告

		$link = M ( 'link' );//友情链接

		$ad = M ( 'ad' );//广告

		$ad_where = M ( 'ad_where' );//广告位

		$websdk = M ('websdk');//微博 微信等登陆

		$arr['webconfig_id'] = $roleid;

		if ($category->where ($arr)->select ()) {

			$this->error ( '请先删除栏目!', U ( 'Global/weblist' ) );//

		}

		if ($notice->where ($arr)->select ()) {

			$this->error ( '请先删除网站公告!', U ( 'Global/weblist' ) );//

		}

		if ($link->where ($arr)->select ()) {

			$this->error ( '请先删除友情链接!', U ( 'Global/weblist' ) );//

		}

		if ($ad->where ($arr)->select ()) {

			$this->error ( '请先删除广告!', U ( 'Global/weblist' ) );//

		}		

		if ($ad_where->where ($arr)->select ()) {

			$this->error ( '请先删除广告位!', U ( 'Global/weblist' ) );//

		}	

		$map['id'] = $roleid;

		if ($webconfig->where ($map)->delete ()) {			

			$websdk->where ($arr)->delete ();		

			$this->success ( "站点删除成功!", U ( 'Global/weblist' ) );

		}

		else

		{

			$this->error ( "发生错误:" . mysql_error (), U ( 'Global/weblist' ) );

		}
	}

}

?>