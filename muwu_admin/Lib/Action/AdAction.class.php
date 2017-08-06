<?php
/**
      @function 广告管理
	  @file AdAction.class.php
	  @Author shf_l@163.com 
	  @copyright (C) 2012-2013 31wan
	  @license	http://demo.31wan.cn/license
	  @lastmodify	2013-09-07
	  @version 4.0

*/
class AdAction extends CommonAction {
	public $config;
	public function __construct() {
		parent::_initialize ();
		parent::__construct ();
		$this->config = C ( "ADMIN_THEME" );
		parent::action_access ( MODULE_NAME, ACTION_NAME );
	}
	
	public function ad_link_lr($ad_id){
		$ad = M ( 'link' );
		$passed = isset ( $_REQUEST ['passed'] ) ? $map ['passed'] = $_REQUEST ['passed'] : $map ['passed'] = 1;
		$map ['webconfig_id'] = $ad_id;
		import ( '@.ORG.Util.Page' );
		$count = $ad->where ( $map )->count ();
		$p = new Page ( $count, 20 );
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '条记录' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>" );
		$this->assign ( 'page', $p->show () );
		$list = $ad->where ( $map )->order ( 'addtime desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
		$this->assign ( 'list', $list );
		$this->assign ( 'ad_id', $ad_id );
		$this->assign ( 'passed', $passed );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/ad_link.html' );
	}
	
	public function category_index_cr($yqlj = '') {
		import ( '@.ORG.Util.Page' );
		$admin_role = M ( "webconfig" ); // 实例化admin_role对象
		$count = $admin_role->order ( 'id desc' )->count (); // 超级管理员查询所有的
		$p = new Page ( $count, 20 );
		$list = $admin_role->order ( 'id desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '区服' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
			<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>篇文章 20篇/每页</span></li>" );
		$this->assign ( 'page', $p->show () );
		$this->assign ( 'list', $list );
		if ($yqlj == 'yqlj') {
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/ad_link_yq.html' );
		}else{	
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/ad_link_cr.html' );
		}
	}
	
	/**
	 *
	 * @name 友情链接管理
	 *      
	 */
	public function ad_link() {
		if ($_SESSION ['group'] == "超级管理员"){
			$this->category_index_cr ( 'yqlj' );
		}else{
		$qzpf_zd = $_SESSION ['qzpf_zd'];
	    $this->ad_link_lr ( $qzpf_zd );
		}
	}
	
	
	public function myfcun($smid = ''){
		if ($_SESSION ['group'] == '超级管理员'){
			$group_role = 1;
			$conn1 = M ( 'webconfig' );
			$fruit = $_REQUEST ['fruit'];
			$map ['fruit'] = '';
			for($i = 0; $i < count ( $fruit ); $i ++){
				if ($i > 0){
					$map ['fruit'] .= ',';
				}
				$map ['fruit'] .= $fruit [$i];
			}
			$ma_list = $conn1->select ();
			$ma_bq = '';
			for($i = 0; $i < count ( $ma_list ); $i ++){
				$zt = '';
				if ($ma_list [$i] ['id'] == $smid){
					$zt = 'selected';
				}
				$ma_bq .= '<option value=' . $ma_list [$i] ["id"] . ' ' . $zt . '>' . $ma_list [$i] ["sitename"] . '</option>';
			}
			return $ma_bq;
		}else{
			return 'close';
		}
	}
	
	/**
	 *
	 * @name 增加友情链接
	 *      
	 */
	public function ad_linkadd($webid = '') {
		$s1 = $this->myfcun ( $webid );
		if ($s1 != 'close'){
			$group_role = 1;
			$ma_bq = $s1;
		}else{
			$group_role = 0;
			$smid = $_SESSION ['qzpf_zd'];
		}
		$log_str = '增加友情链接';
		parent::admin_log ( $log_str );
		$link = M ( 'link' );
		if ($_REQUEST ['dosubmit']) {
			$map ['name'] = trim ( $_REQUEST ['name'] );
			$flag = $link->where ($map)->getField ( 'name' );
				if ($flag) {
				$this->error ( "友情链接已存在！" );
			}
			$map ['url'] = trim ( $_REQUEST ['url'] );
			$map ['username'] = trim ( $_REQUEST ['username'] );
			/* 判断权限 */
			if ($_SESSION ['group'] == '超级管理员'){
				$map ['webconfig_id'] = $_REQUEST ['webconfig_id'];
			}else{
				$map ['webconfig_id'] = $_SESSION ['qzpf_zd'];
			}
			if ($map ['name'] == '') {
				$this->error ( "网站名不能为空！" );
			}

			$map ['introduce'] = trim ( $_REQUEST ['introduce'] );
			$map ['elite'] = trim ( $_REQUEST ['elite'] );
			$map ['listorder'] = trim ( $_REQUEST ['listorder'] );
			$map ['passed'] = trim ( $_REQUEST ['passed'] );
			$map ['link_type'] = trim ( $_REQUEST ['link_type'] );
			$map ['addtime'] = time ();
			if (! empty ( $_FILES ["photo1"] ["name"] )) 
			{
				/* 图片上传 */
				
				$list_img = $_FILES ["photo1"];
				$ima_name1 = $this->ftp_image_com ( $list_img, 'Advert', 'you', $map ['webconfig_id'] );
				if($ima_name1==1)
				{
					$this->error('上传的图片超过2M!');
					
				}
				if($ima_name1==2)
				{
					$this->error('上传的是必须是图片');
					
				}
				$map ['link_img'] = $ima_name1;
				/* 图片上传 */
			}
			if ($link->add ( $map )) {
				$this->success ( '友情链接发布成功!', U ( 'Ad/ad_link_lr?ad_id=' . $map ['webconfig_id'] ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/ad_link' ) );
			}
		}
		$this->assign ( 'ma_bq', $ma_bq );
		$this->assign ( 'group_role', $group_role );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/ad_linkadd.html' );
	}
	
	/**
	 *
	 * @name 修改友情链接
	 *      
	 */
	public function ad_linkedit() {
		$log_str = '修改友情链接';
		parent::admin_log ( $log_str );
		$conn = M ( 'link' );
		$arr ['linkid'] = $_REQUEST ['linkid'];
		$info = $conn->where ( $arr )->find ();
		/* 判断图片显示路径 */
		$weblist = M ( 'webconfig' );
		/* 判断图片显示路径 */
		
		$s1 = $this->myfcun ( $info ['webconfig_id'] );
		if ($s1 != 'close'){
			$group_role = 1;
			$ma_bq = $s1;
		}else{
		    $group_role = 0;
			$smid = $_SESSION ['qzpf_zd'];
		}
		$this->assign ( 'info', $info );
		$this->assign ( 'linkid', $arr ['linkid'] );
		if ($_REQUEST ['dosubmit']) {
			$map ['name'] = trim ( $_REQUEST ['name'] );
			$map ['url'] = trim ( $_REQUEST ['url'] );
			$map ['username'] = trim ( $_REQUEST ['username'] );
			/* 判断权限 */
			if ($_SESSION ['group'] == '超级管理员'){
				$map ['webconfig_id'] = $_REQUEST ['webconfig_id'];
			}else{
				$map ['webconfig_id'] = $_SESSION ['qzpf_zd'];
			}
			if ($map ['name'] == ''){
				$this->error ( "网站名不能为空！" );
			}
			$map ['introduce'] = trim ( $_REQUEST ['introduce'] );
			$map ['elite'] = trim ( $_REQUEST ['elite'] );
			$map ['listorder'] = trim ( $_REQUEST ['listorder'] );
			$map ['passed'] = trim ( $_REQUEST ['passed'] );
			$arr1 ['linkid'] = trim ( $_REQUEST ['linkid'] );
			$map ['link_type'] = trim ( $_REQUEST ['link_type'] );
			if (! empty ( $_FILES ["photo1"] ["name"] )){
				/* 图片上传 */				
				$list_img = $_FILES ["photo1"];				
				$ima_name1 = $this->ftp_image_com ( $list_img, 'Advert', 'you', $map ['webconfig_id'] );
				if($ima_name1==1)
				{
					$this->error('上传的图片超过2M!');
					
				}
				if($ima_name1==2)
				{
					$this->error('上传的是必须是图片');
					
				}
				
				/* 图片上传 */
			}
			$map ['link_img'] = $ima_name1;
			$st = $conn->where ( $arr1 )->save ( $map ); // 根据条件保存修改的数据
			if ($st) {
				$this->success ( '修改成功!', U ( 'Ad/ad_link_lr?ad_id=' . $map ['webconfig_id'] ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/ad_link' ) );
			}
		}
		$this->assign ( 'ma_bq', $ma_bq );
		$this->assign ( 'group_role', $group_role );
		$this->assign ( 'ser_http', $ser_http );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/ad_linkedit.html' );
	}
	
	/**
	 *
	 * @name 删除友情链接
	 *      
	 */
	public function ad_linkdelete() {
		$log_str = '删除友情链接';
		parent::admin_log ( $log_str );
		$gid = $_REQUEST ['linkid'];
		$gids = implode ( ',', $gid ); // 批量获取gid
		$id = is_array ( $gid ) ? $gids : $gid;
		$map ['linkid'] = array ('in',$id);
		if (! $gid) {
			$this->error ( '请勾选记录!' );
		}
		$conn = M ( 'link' );
		$flag = $conn->where ( $map )->delete (); // 删除数据
		if ($flag){
			$this->success ( '删除成功!', U ( 'Ad/ad_link_lr?ad_id=' .$_REQUEST['web_id'] ));
		} else {
			$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/ad_link' ) );
		}
	}
	
	
	public function ad_index_lr($gl_id = '') {
		$ad = M ( 'ad' );
		$map ['webconfig_id'] = $gl_id;
		import ( '@.ORG.Util.Page' );
		$count = $ad->where ( $map )->count ();
		$p = new Page ( $count, 20 );
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '条记录' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>" );
		$this->assign ( 'page', $p->show () );
		$list = $ad->where ( $map )->order ( 'addtime desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
		$ad_where = M ( 'ad_where' );
		foreach ( $list as $k => $v ) {
			// 执行其他的数据操作
			$where_list = $ad_where->where ( 'id = ' . $v ['type'] )->find ();
			$list [$k] ['typetitle'] = $where_list ['title'];
		}
		$this->assign ( 'list', $list );
		$this->assign ( 'webid', $gl_id );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/ad_index.html' );
	}
	
	/**
	 *
	 * @name 广告管理
	 *      
	 */
	public function ad_index() {
		if ($_SESSION ['group'] == "超级管理员"){
			$this->category_index_cr ();
		}else{
			$qzpf_zd = $_SESSION ['qzpf_zd'];	
			$this->ad_index_lr ( $qzpf_zd );
		}
	}
	
	
	/*广告位列表*/
	public function adwhere_list_lr($dw_id = ''){
		$ad_where = M ( 'ad_where' );
		// 执行其他的数据操作
		import ( '@.ORG.Util.Page' );
		$map['webconfig_id'] = $dw_id;
		$count = $ad_where->where ($map)->count ();
		$p = new Page ( $count, 20 );
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '条记录' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>" );
		$this->assign ( 'page', $p->show () );
		$where_list = $ad_where->where ($map)->order ( 'id desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
		$this->assign ( 'where_list', $where_list );
		$this->assign ( 'dw_id', $dw_id );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/adwhere_list.html' );
	}
	
	
	
	
	/**
	 *
	 * @name 广告位列表
	 *      
	 */
	
	public function adwhere_list() {
		if ($_SESSION ['group'] == "超级管理员") {
			$this->category_index_cr ();
		}else{
			$qzpf_zd = $_SESSION ['qzpf_zd'];
			$this->adwhere_list_lr ( $qzpf_zd );
		}
	}
	
	/**
	 *
	 * @name 增加广告位
	 *      
	 */
	public function add_adwhere($webid = '') {
		$s1 = $this->myfcun ( $webid );
		if ($s1 != 'close'){
			$group_role = 1;
			$ma_bq = $s1;
		}else{
			$group_role = 0;	
			$smid = $_SESSION ['qzpf_zd'];
		}
		$arr = '增加广告位子';
		parent::admin_log ( $arr );
		if ($_REQUEST ['dosubmit']) {
			$map ['title'] = trim ( $_REQUEST ['title'] );
			if ($_SESSION ['group'] == '超级管理员'){
				$map ['webconfig_id'] = $_REQUEST ['webconfig_id'];
			}else{
				$map ['webconfig_id'] = $_SESSION ['qzpf_zd'];
			}
			$ad_where = M ( "ad_where" ); // 实例化gametype对象
			$flag = $ad_where->where ($map)->getField ( 'title' );
			if ($flag) {
				$this->error ( "广告位子已存在" );
			}
			if ($ad_where->add ( $map )) {
				$this->success ( "发布成功!", U ( 'Ad/adwhere_list_lr?dw_id='.$map ['webconfig_id'] ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/adwhere_list' ) );
			}
		}
		$this->assign ( 'group_role', $group_role );
		$this->assign ( 'ma_bq', $ma_bq );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/add_adwhere.html' );
	}
	
	/**
	 *
	 * @name 修改该广告位
	 *      
	 */
	public function edit_adwhere() {
		$arr_1 = '修改广告位子';
		parent::admin_log ( $arr_1 );
		$conn = M ( 'ad_where' );
		$arr ['id'] = $_REQUEST ['id'];
		$web_id = $_REQUEST['web_id'];
		$info = $conn->where ( $arr )->find ();
		$s1 = $this->myfcun ( $info ['webconfig_id'] );
		if ($s1 != 'close'){
			$group_role = 1;
			$ma_bq = $s1;
		}else{
			$group_role = 0;
			$smid = $_SESSION ['qzpf_zd'];
		}
		$this->assign ( 'info', $info );
		$this->assign ( 'id', $arr ['id'] );
		if ($_REQUEST ['dosubmit']) {
			$map ['title'] = trim ( $_REQUEST ['title'] );
			$arr1 ['id'] = trim ( $_REQUEST ['id'] );
			$web_id = trim ( $_REQUEST ['web_id'] );
			/* 判断权限 */
			if ($_SESSION ['group'] == '超级管理员'){
				$map ['webconfig_id'] = $_REQUEST ['webconfig_id'];
			}else{
				$map ['webconfig_id'] = $_SESSION ['qzpf_zd'];
			}
			if ($map ['title'] == '') {
				$this->error ( "广告位子不能为空！" );
			}
			$st = $conn->where ( $arr1 )->save ( $map ); // 根据条件保存修改的数据
			if ($st) {
				$this->success ( "修改成功!", U ( 'Ad/adwhere_list_lr?dw_id='.$web_id ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/adwhere_list' ) );
			}
		}
		$this->assign ( 'ma_bq', $ma_bq );
		$this->assign ( 'web_id', $web_id);
		$this->assign ( 'group_role', $group_role );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/edit_adwhere.html' );
	}
	
	/**
	 *
	 * @name 删除广告
	 *      
	 */
	public function adwhere_delete() {
		$arr = '删除广告位子';
		parent::admin_log ( $arr );
		$gid = $_REQUEST ['id'];
		$web_id = $_REQUEST['web_id'];
		$gids = implode ( ',', $gid ); // 批量获取gid
		$id = is_array ( $gid ) ? $gids : $gid;
		$map ['type'] = array ('in',$id);
		$map1 ['id'] = array ('in',$id);
		if (! $gid) {
			$this->error ( "请勾选记录！" );
		}
		$ad_where = M ( "ad_where" ); // 实例化game对象
		$ad = M ( 'ad' );
		$flag_game = $ad->where ( $map )->getField ( 'id' );
		if ($flag_game) {
			$this->error ( "该广告位下还有广告!", U ( 'Ad/adwhere_list' ) );
		} else {
			$flag = $ad_where->where ( $map1 )->delete (); // 删除数据
			if ($flag) {
				$this->success ( "删除成功!", U ( 'Ad/adwhere_list_lr?dw_id='.$web_id ) );
			} else {
				
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/adwhere_list' ) );
			}
		}
	}
	
	/**
	 *
	 * @name 增加广告
	 *      
	 */
	public function add($webid = '') {
		$s1 = $this->myfcun ( $webid );
		if ($s1 != 'close'){
			$group_role = 1;
			$ma_bq = $s1;
		}else{
			$group_role = 0;	
			$smid = $_SESSION ['qzpf_zd'];
		}
		$arr = '增加广告';
		parent::admin_log ( $arr );
		$ad = M ( "ad" ); // 实例化gametype对象
		if ($_REQUEST ['dosubmit']) {
			$map ['title'] = trim ( $_REQUEST ['title'] );
			$flag = $ad->where ($map)->getField ( 'title' );
			if ($flag) {
				$this->error ( "广告名称已存在！" );
			}
			if ($map ['title'] == '') {
				$this->error ( "广告名称不能为空" );
			}
			$map ['status'] = trim ( $_REQUEST ['status'] );
			$map ['url'] = trim ( $_REQUEST ['url'] );
			$map ['type'] = trim ( $_REQUEST ['type'] );
			$map ['end_time'] = strtotime ( trim ( $_REQUEST ['end_time'] ) );
			$map ['addtime'] = time ();
			$map ['description'] = trim ( $_REQUEST ['description'] );
			if ($_SESSION ['group'] == '超级管理员'){
				$map ['webconfig_id'] = $_REQUEST ['webconfig_id'];
			}else{
				$map ['webconfig_id'] = $_SESSION ['qzpf_zd'];
			}
			if (! empty ( $_FILES ["content"] ["name"] )){
				/* 图片上传 */
				$list_img = $_FILES ["content"];
				$ima_name1 = $this->ftp_image_com ( $list_img, 'Advert', 'you', $map ['webconfig_id'] );
				if($ima_name1==1)
				{
					$this->error('上传的图片超过2M!');
					
				}
				if($ima_name1==2)
				{
					$this->error('上传的是必须是图片');
					
				}
				$map ['content'] = $ima_name1;
				/* 图片上传 */
			}
			
			
			if (! empty ( $_FILES ["image"] ["name"] )){
				/* 图片上传 */
				$list_img = $_FILES ["image"];
				$ima_name1 = $this->ftp_image_com ( $list_img, 'Advert', 'you', $map ['webconfig_id'] );
				if($ima_name1==1)
				{
					$this->error('上传的图片超过2M!');
					
				}
				if($ima_name1==2)
				{
					$this->error('上传的是必须是图片');
					
				}
				$map ['image'] = $ima_name1;
				/* 图片上传 */
			}

			if ($ad->add ( $map )) {
				$this->success ( "广告发布成功!", U ( 'Ad/ad_index_lr?gl_id='.$map ['webconfig_id'] ) );
			} else {
				
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/ad_index' ) );
			}
		}
		$this->gltype($webid);
			
		$this->assign ( 'group_role', $group_role );
		$this->assign ( 'ma_bq', $ma_bq );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/ad_add.html' );
	}
	
	/**
	 *
	 * @name 修改广告
	 *      
	 */
	public function edit() {
		$arr = '修改广告';
		parent::admin_log ( $arr );
		$type = M ( 'ad' );
		$art['id'] = trim ( $_REQUEST ['id'] );
		if ($_REQUEST ['dosubmit']) {			
			$info = $type->where ($art)->find ();
			$map ['title'] = trim ( $_REQUEST ['title'] );
			if ($map ['title'] == '') {
				$this->error ( "广告名称不能为空！" );
			}
			$map ['status'] = trim ( $_REQUEST ['status'] );
			$map ['type'] = trim ( $_REQUEST ['type'] );
			$map ['end_time'] = strtotime ( trim ( $_REQUEST ['end_time'] ) );
			$map ['description'] = trim ( $_REQUEST ['description'] );
			$map ['url'] = trim ( $_REQUEST ['url'] );
			if ($_SESSION ['group'] == '超级管理员'){
				$map ['webconfig_id'] = $_REQUEST ['webconfig_id'];
			}else{
				$map ['webconfig_id'] = $_SESSION ['qzpf_zd'];
			}
			if (! empty ( $_FILES ["content"] ["name"] )){				
				/* 图片上传 */
				$list_img = $_FILES ["content"];	
				$ima_name1 = $this->ftp_image_com ( $list_img, 'Advert', 'you', $map ['webconfig_id'] );
				if($ima_name1==1)
				{
					$this->error('上传的图片超过2M!');
					
				}
				if($ima_name1==2)
				{
					$this->error('上传的是必须是图片');
					
				}
				$map ['content'] = $ima_name1;				
				/* 图片上传 */
			}
			
			if (! empty ( $_FILES ["image"] ["name"] )){
				/* 图片上传 */
				$list_img = $_FILES ["image"];
				$ima_name1 = $this->ftp_image_com ( $list_img, 'Advert', 'you', $map ['webconfig_id'] );
				if($ima_name1==1)
				{
					$this->error('上传的图片超过2M!');
					
				}
				if($ima_name1==2)
				{
					$this->error('上传的是必须是图片');
					
				}
				$map ['image'] = $ima_name1;
				/* 图片上传 */
			}
			
			$st = $type->where ($art)->save ( $map ); // 根据条件保存修改的数据
			if ($st) {	
				$this->success ( "广告发布成功!", U ( 'Ad/ad_index_lr?gl_id='.$map ['webconfig_id'] ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/ad_index' ) );
			}
		}
		$list = $type->where ($art)->find ();
		
		// 读取广告类型
		$this->gltype($list ['webconfig_id']);
		// 读取广告类型
		
		$s1 = $this->myfcun ( $list ['webconfig_id'] );
		if ($s1 != 'close'){
			$group_role = 1;
			$ma_bq = $s1;
		}else{
			$group_role = 0;	
			$smid = $_SESSION ['qzpf_zd'];
		}
		
		$this->assign ( 'ser_http', $ser_http );
		$this->assign ( 'list', $list );
		$this->assign ( 'group_role', $group_role );
		$this->assign ( 'ma_bq', $ma_bq );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/ad_edit.html' );
	}
	
	/*广告类型*/
	public function gltype($webconfig_id)
	{
		$ad_where = M ( 'ad_where' );
		$map['webconfig_id'] = $webconfig_id;
		$where_list = $ad_where->where ($map)->select ();	
		$this->assign ( 'where_list', $where_list );
	}
	
	/**
	 *
	 * @name 广告删除
	 *      
	 */
	public function del() {
		$log_str = '广告删除';
		parent::admin_log ( $log_str );
		$gid = $_REQUEST ['id'];
		$web_id = $_REQUEST ['web_id'];
		$gids = implode ( ',', $gid ); // 批量获取gid
		$id = is_array ( $gid ) ? $gids : $gid;
		$map1 ['id'] = array ('in',$id);
		if (! $gid){
			$this->error ( "请勾选记录！" );
		}
		$ad = M ( 'ad' );
		$flag = $ad->where ( $map1 )->delete (); // 删除数据
		if ($flag) {
			$this->success ( "删除成功!", U ( 'Ad/ad_index_lr?gl_id='.$web_id ) );
		} else {
			$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/ad_index' ) );
		}
		
	}
	
	
	/**
	 *
	 * @name 积分礼物管理
	 *      
	 */
	public function gift_index()
	{
		$log_str = '积分礼物管理';
		parent::admin_log ( $log_str );		
		$gift = M ( 'gift' );
		$map ['giftdelete'] = 1;
		if($_REQUEST['giftname'] !='')
		{
			$map["giftname"] = array("like", "%".$_REQUEST['giftname']."%");	
		}
		
		import ( '@.ORG.Util.Page' );
		$count = $gift->where ( $map )->count ();
		$p = new Page ( $count, 20 );
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '条记录' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>" );
		$this->assign ( 'page', $p->show () );
		$list = $gift->where ( $map )->order ( 'gift_time desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
		$this->assign ( 'list', $list );
		$this->assign ( 'giftname', $_REQUEST['giftname'] );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/gift_index.html' );
	}
	
	/**
	 *
	 * @name 增加积分礼物
	 *      
	 */
	public function gift_add()
	{
		$log_str = '增加积分礼物';
		parent::admin_log ( $log_str );		
		$gift_type = M ('gift_type');	
		$gift_type_list = $gift_type->where()->select();
		$this->assign ( 'gitylt', $gift_type_list );
		if ($_REQUEST ['dosubmit']) {
			$gift = M('gift');
			$map ['giftname'] = trim ( $_REQUEST ['giftname'] );
			$map ['giftcode'] = trim ( $_REQUEST ['giftcode'] );
			$map ['old_integral'] = trim ( $_REQUEST ['old_integral'] );
			$map ['new_integral'] = trim ( $_REQUEST ['new_integral'] );
			$map ['quantity'] = trim ( $_REQUEST ['quantity'] );
			$map ['sales_volume'] = trim ( $_REQUEST ['sales_volume'] );
			$map ['context'] = trim ( $_REQUEST ['context'] );
			$map ['gift_time'] = time();
			$map ['starttime'] = strtotime(trim ( $_REQUEST ['starttime'] ));
			$map ['endtiem'] = strtotime(trim ( $_REQUEST ['endtiem'] ));
			$map ['giftshow'] = trim ( $_REQUEST ['giftshow'] );
			$map ['weight'] = trim ( $_REQUEST ['weight'] );
			$map ['tuijian'] = trim ( $_REQUEST ['tuijian'] );
			$map ['gift_type_id'] = trim ( $_REQUEST ['gift_type_id'] );
			$map ['gift_card'] = trim ( $_REQUEST ['gift_card'] );						
			/*默认读取倒叙第一个服务器*/
			$conn1 = M( 'webconfig' );
			$list = $conn1->order ('id asc')->find();
			
			if (!empty($_FILES["image"]["name"]))
			{
				$list_img = $_FILES["image"];
				$ima_name1 = $this->ftp_image_com($list_img,'Gift','you',$list['id'],'1');
				if($ima_name1==1)
				{
					$this->error('上传的图片超过2M!');
					
				}
				if($ima_name1==2)
				{
					$this->error('上传的是必须是图片');
					
				}	
				$map['image'] = $ima_name1;//文章图片(缩略图加s_)
			}
			
			if ($gift->add ( $map )) {
				$this->success ( "发布成功!", U ( 'Ad/gift_index' ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/gift_index' ) );
			}			
		}
		else
		{
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/gift_add.html' );	
		}
		
	}
	
	/**
	 *
	 * @name 删除礼品
	 */
	public function gift_updel() {
		$arr = '删除礼品';
		parent::admin_log ( $arr );
		$gid = $_REQUEST ['id'];
		$gids = implode ( ',', $gid ); // 批量获取gid
		$id = is_array ( $gid ) ? $gids : $gid;
		$map ['id'] = array (
				'in',
				$id 
		);
		if (! $gid) {
			$this->error ( "请勾选记录！" );
		}
		$gift = M ( "gift" ); // 实例化game对象
		$nap ['giftdelete'] = 0;
		
		$webconfig = M ('webconfig');
		$web_list = $webconfig->order('id asc')->field('is_recycler')->find();
		if($web_list['is_recycler'] == 1)
		{
			$flag = $gift->where ( $map )->save($nap); // 假删除		
		}
		else
		{
			$flag = $gift->where ( $map )->delete (); //真删除
		}		
		
		if ($flag!==false) {
			$this->success ( "删除礼品成功!", U ( 'Ad/gift_index' ) );
		} else {
			$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/gift_index' ) );
		}
		
	}
	
	/**
	 *
	 * @name 修改礼品
	 */
	public function gift_edit()
	{
		$gift = M ('gift');
		$art['id'] = $_REQUEST['id'];
		$list = $gift->where ($art)->find ();
		$this->assign ( 'list', $list );		
		
		$gift_type = M ('gift_type');	
		$gift_type_list = $gift_type->where()->select();
		$this->assign ( 'gitylt', $gift_type_list );
		if ($_REQUEST ['dosubmit']) {
			$gift = M('gift');
			$map ['giftname'] = trim ( $_REQUEST ['giftname'] );
			$map ['giftcode'] = trim ( $_REQUEST ['giftcode'] );
			$map ['old_integral'] = trim ( $_REQUEST ['old_integral'] );
			$map ['new_integral'] = trim ( $_REQUEST ['new_integral'] );
			$map ['quantity'] = trim ( $_REQUEST ['quantity'] );
			$map ['sales_volume'] = trim ( $_REQUEST ['sales_volume'] );
			$map ['context'] = trim ( $_REQUEST ['context'] );
			$map ['gift_time'] = time();
			$map ['starttime'] = strtotime(trim ( $_REQUEST ['starttime'] ));
			$map ['endtiem'] = strtotime(trim ( $_REQUEST ['endtiem'] ));
			$map ['giftshow'] = trim ( $_REQUEST ['giftshow'] );
			$map ['weight'] = trim ( $_REQUEST ['weight'] );
			$map ['tuijian'] = trim ( $_REQUEST ['tuijian'] );
			$map ['gift_type_id'] = trim ( $_REQUEST ['gift_type_id'] );
			$map ['gift_card'] = trim ( $_REQUEST ['gift_card'] );						
			$uap['id'] = trim($_REQUEST['yc_id']);
			if (!empty($_FILES["image"]["name"]))
			{
				$list_img = $_FILES["image"];
				$ima_name1 = $this->ftp_image_com($list_img,'Gift','you',$list['id'],'1');
				if($ima_name1==1)
				{
					$this->error('上传的图片超过2M!');
					
				}
				if($ima_name1==2)
				{
					$this->error('上传的是必须是图片');
					
				}	
				$map['image'] = $ima_name1;//文章图片(缩略图加s_)
			}
			$flag = $gift->where ( $uap )->save($map); // 修改	
			if ($flag!==false) {
				$this->success ( "修改成功!", U ( 'Ad/gift_index' ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/gift_index' ) );
			}			
		}
		else
		{
			$this->assign ( 'yc_id', $_REQUEST['id'] );
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/gift_edit.html' );
		}
		
	}
	
	
	/**
	 *
	 * @name 礼物分类
	 *      
	 */
	public function gift_type_index()
	{
		$log_str = '礼物分类管理';
		parent::admin_log ( $log_str );		
		$gift_type = M ( 'gift_type' );
		if($_REQUEST['giftname'] !='')
		{
			$map["gifttype_name"] = array("like", "%".$_REQUEST['giftname']."%");	
		}
		
		import ( '@.ORG.Util.Page' );
		$count = $gift_type->where ( $map )->count ();
		$p = new Page ( $count, 20 );
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '条记录' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>" );
		$this->assign ( 'page', $p->show () );
		$list = $gift_type->where ( $map )->order ( 'gifttype_time desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
		$this->assign ( 'list', $list );
		$this->assign ( 'giftname', $_REQUEST['giftname'] );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/gift_type_index.html' );
	}
	
	/**
	 *
	 * @name 增加礼物分类
	 *      
	 */
	public function gift_type_add()
	{
		$log_str = '增加礼品分类';
		parent::admin_log ( $log_str );			
		if ($_REQUEST ['dosubmit']) {
			$gift_type = M('gift_type');
			$map ['gifttype_name'] = trim ( $_REQUEST ['gifttype_name'] );
			$map ['gifttype_time'] = time();
			
			if ($gift_type->add ( $map )) {
				$this->success ( "发布成功!", U ( 'Ad/gift_type_index' ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/gift_type_index' ) );
			}			
		}
		else
		{
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/gift_type_add.html' );	
		}
	}
	
	
	/**
	 *
	 * @name 修改礼品分类
	 */
	public function gift_type_edit()
	{
		$gift_type = M ('gift_type');
		$art['id'] = $_REQUEST['id'];
		$list = $gift_type->where ($art)->find ();
		$this->assign ( 'list', $list );		
		if ($_REQUEST ['dosubmit']) {
			$map ['gifttype_name'] = trim ( $_REQUEST ['gifttype_name'] );
			$uap['id'] = trim($_REQUEST['yc_id']);

			$flag = $gift_type->where ( $uap )->save($map); // 修改	
			if ($flag!==false) {
				$this->success ( "修改成功!", U ( 'Ad/gift_type_index' ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/gift_type_index' ) );
			}			
		}
		else
		{
			$this->assign ( 'yc_id', $_REQUEST['id'] );
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/gift_type_edit.html' );
		}
		
	}
	
	/**
	 *
	 * @name 删除礼品分类
	 */
	public function gift_type_updel() {
		$arr = '删除礼品分类';
		parent::admin_log ( $arr );
		$gid = $_REQUEST ['id'];
		$gids = implode ( ',', $gid ); // 批量获取gid
		$id = is_array ( $gid ) ? $gids : $gid;
		$map ['id'] = array (
				'in',
				$id 
		);
		if (! $gid) {
			$this->error ( "请勾选记录！" );
		}
		$gift_type = M ( "gift_type" ); // 实例化game对象	
		$gift = M ( "gift" ); // 实例化game对象				

		$flag_game = $gift->where (' gift_type_id = '.$id)->getField ( 'id' );
		
		if ($flag_game) {
			$this->error ( "该分类下有产品!", U ( 'Ad/gift_type_index' ) );
		} else {
			$flag = $gift_type->where ( $map )->delete (); //真删除		
			if ($flag) {
				$this->success ( "删除成功!", U ( 'Ad/gift_type_index') );
			} else {
				
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/gift_type_index' ) );
			}
		}		
	}
	
	
	/**
	 *
	 * @name 礼物兑换记录
	 *      
	 */
	public function gift_log_index()
	{
		$log_str = '礼物兑换记录';
		parent::admin_log ( $log_str );		
		$gift_log = M ( 'gift_log' );
		$member_extend_info = M ('member_extend_info');
		if($_REQUEST['giftname'] !='')
		{
			$map["giftname"] = array("like", "%".$_REQUEST['giftname']."%");	
		}
		
		import ( '@.ORG.Util.Page' );
		$count = $gift_log->where ( $map )->count ();
		$p = new Page ( $count, 20 );
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '条记录' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>" );
		$this->assign ( 'page', $p->show () );
		$list = $gift_log->where ( $map )->order ( 'send_state asc,receive_time desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
		foreach ( $list as $k => $v ) {
			if($v['send_state'] == 0)
			{
				//查询用户地址
				$mem_map['uid'] = $v ['uid'];
				// yumao change 2015-7-24 根据uid来判断是从哪一个表中查找数据
				$member_extend_info_new = D("FenbiaoMemberExtend")->getDao(array('uid'=>$mem_map['uid']));				
				$member_extend_list = $member_extend_info_new->where($mem_map)->find();
				// change end
				if($member_extend_list['address'] !='')
				{
					$list [$k] ['arres'] = $member_extend_list['address'].'( 邮编:'.$member_extend_list['zip_code'].')';	
				}
			}
		}
		
		$this->assign ( 'list', $list );
		$this->assign ( 'giftname', $_REQUEST['giftname'] );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/gift_log_index.html' );
	}
	
	
	/**
	 *
	 * @name 删除兑换记录
	 */
	public function gift_log_updel() {
		$arr = '删除兑换记录';
		parent::admin_log ( $arr );
		$gid = $_REQUEST ['id'];
		$gids = implode ( ',', $gid ); // 批量获取gid
		$id = is_array ( $gid ) ? $gids : $gid;
		$map ['id'] = array (
				'in',
				$id 
		);
		if (! $gid) {
			$this->error ( "请勾选记录！" );
		}
		$gift_log = M ( "gift_log" ); // 实例化game对象			
		$flag = $gift_log->where ( $map )->delete (); //真删除		
		if ($flag) {
			$this->success ( "删除成功!", U ( 'Ad/gift_log_index') );
		} else {
			
			$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/gift_log_index' ) );
		}
			
	}
	
	
	/**
	 *
	 * @name 任务管理
	 */
	public function task_index()
	{
		$log_str = '任务管理';
		parent::admin_log ( $log_str );		
		$task = M ( 'task' );
		if($_REQUEST['giftname'] !='')
		{
			$map["title"] = array("like", "%".$_REQUEST['giftname']."%");	
		}
		
		import ( '@.ORG.Util.Page' );
		$count = $task->where ( $map )->count ();
		$p = new Page ( $count, 20 );
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '条记录' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>" );
		$this->assign ( 'page', $p->show () );
		$list = $task->where ( $map )->order ( 'id desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
		$this->assign ( 'list', $list );
		$this->assign ( 'giftname', $_REQUEST['giftname'] );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/task_index.html' );
	}
	
	
	/**
	 *
	 * @name 增加任务
	 *      
	 */
	public function task_add()
	{
		$log_str = '增加任务';
		parent::admin_log ( $log_str );		
		if ($_REQUEST ['dosubmit']) {
			$task  = M('task ');
			$map ['title'] = trim ( $_REQUEST ['title'] );
			$map ['target'] = trim ( $_REQUEST ['target'] );
			$map ['integral'] = trim ( $_REQUEST ['integral'] );
			$map ['task_label'] = trim ( $_REQUEST ['task_label'] );
			$map ['task_url'] = trim ( $_REQUEST ['task_url'] );
			$map ['task_time'] = time();		
					

			/*默认读取倒叙第一个服务器*/
			$conn1 = M( 'webconfig' );
			$list = $conn1->order ('id asc')->find();
			
			if (!empty($_FILES["image"]["name"]))
			{
				$list_img = $_FILES["image"];
				$ima_name1 = $this->ftp_image_com($list_img,'Task','you',$list['id'],'1');
				if($ima_name1==1)
				{
					$this->error('上传的图片超过2M!');
					
				}
				if($ima_name1==2)
				{
					$this->error('上传的是必须是图片');//
					
				}	
				$map['image'] = $ima_name1;//文章图片(缩略图加s_)
			}
			
			if ($task->add ( $map )) {
				$this->success ( "发布成功!", U ( 'Ad/task_index' ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/task_index' ) );
			}			
		}
		else
		{
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/task_add.html' );	
		}
		
	}
	
	/**
	 *
	 * @name 修改任务
	 */
	public function task_edit()
	{
		$log_str = '修改任务';
		parent::admin_log ( $log_str );	
		$task = M ('task');
		$art['id'] = $_REQUEST['id'];
		$list = $task->where ($art)->find ();
		$this->assign ( 'list', $list );		
		if ($_REQUEST ['dosubmit']) {
			$map ['title'] = trim ( $_REQUEST ['title'] );
			$map ['target'] = trim ( $_REQUEST ['target'] );
			$map ['integral'] = trim ( $_REQUEST ['integral'] );
			$map ['task_label'] = trim ( $_REQUEST ['task_label'] );	
			$map ['task_url'] = trim ( $_REQUEST ['task_url'] );								
			$uap['id'] = trim($_REQUEST['yc_id']);			
			/*默认读取倒叙第一个服务器*/
			$conn1 = M( 'webconfig' );
			$list = $conn1->order ('id asc')->find();
			if (!empty($_FILES["image"]["name"]))
			{
				$list_img = $_FILES["image"];
				$ima_name1 = $this->ftp_image_com($list_img,'Task','you',$list['id'],'1');
				if($ima_name1==1)
				{
					$this->error('上传的图片超过2M!');
					
				}
				if($ima_name1==2)
				{
					$this->error('上传的是必须是图片');
					
				}	
				$map['image'] = $ima_name1;//文章图片(缩略图加s_)
			}
			$flag = $task->where ( $uap )->save($map); // 修改	
			if ($flag!==false) {
				$this->success ( "修改成功!", U ( 'Ad/task_index' ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/task_index' ) );
			}			
		}
		else
		{
			$this->assign ( 'yc_id', $_REQUEST['id'] );
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/task_edit.html' );
		}
		
	}
	
	
	/**
	 *
	 * @name 删除任务
	 */
	public function task_updel() {
		$arr = '删除任务';
		parent::admin_log ( $arr );
		$gid = $_REQUEST ['id'];
		$gids = implode ( ',', $gid ); // 批量获取gid
		$id = is_array ( $gid ) ? $gids : $gid;
		$map ['id'] = array ('in',$id);
		if (! $gid) {
			$this->error ( "请勾选记录！" );
		}
		$task = M ( "task" ); // 实例化game对象
		$flag = $task->where ( $map )->delete (); //真删除
					
		if ($flag!==false) {
			$this->success ( "删除任务成功!", U ( 'Ad/task_index' ) );
		} else {
			$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/task_index' ) );
		}
		
	}
	
	
	
	/**
	 *
	 * @name 抽奖管理
	 */
	public function luckdraw_index()
	{
		$log_str = '抽奖管理';
		parent::admin_log ( $log_str );		
		$luckdraw = M ( 'luckdraw' );
		if($_REQUEST['giftname'] !='')
		{
			$map["title"] = array("like", "%".$_REQUEST['giftname']."%");	
		}
		
		import ( '@.ORG.Util.Page' );
		$count = $luckdraw->where ( $map )->count ();
		$p = new Page ( $count, 20 );
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '条记录' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>" );
		$this->assign ( 'page', $p->show () );
		$list = $luckdraw->where ( $map )->order ( 'weight asc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();		
		$this->assign ( 'list', $list );
		$this->assign ( 'giftname', $_REQUEST['giftname'] );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/luckdraw_index.html' );
	}
	
	
	
	/**
	 *
	 * @name 检测奖品的权重 不可重复
	 *      
	 */
	public function ajax_getwei($weight,$ID)
	{
		$luckdraw = M ('luckdraw');		
		if($ID==0)
		{
			$f_map['weight'] = $weight;
			$luckdraw_id = $luckdraw->where($f_map)->getField('id');
			if($luckdraw_id)
			{
				$this->ajaxReturn ( $weight, '权重重复' ,1);
			}
			else
			{
				$this->ajaxReturn ( $weight, '不重复' ,2);
			}
			
			
		}	
		else
		{
			$f_map['weight'] = $weight;
			$f_map['id'] = $ID;
			$luckdraw_id = $luckdraw->where($f_map)->getField('id');
			if($luckdraw_id)
			{
				$this->ajaxReturn ( $weight, '权重重复' ,1);
			}
			else
			{
				$this->ajaxReturn ( $weight, '不重复' ,2);
			}
		}	
		
	}
	
	/**
	 *
	 * @name 增加奖品
	 *      
	 */
	public function luckdraw_add()
	{
		$log_str = '增加奖品';
		parent::admin_log ( $log_str );		
		$luckdraw  = M('luckdraw');	
		$l_sum = $luckdraw->sum('probability');
		$probability = (100-$l_sum);	
		$this->assign ( 'probability', $probability );
		if ($_REQUEST ['dosubmit']) {			
			$map ['title'] = trim ( $_REQUEST ['title'] );
			$map ['type'] = trim ( $_REQUEST ['type'] );
			$map ['weight'] = trim ( $_REQUEST ['weight'] );
			$map ['probability'] = trim ( $_REQUEST ['probability'] );
			$map ['luckdraw_card'] = trim ( $_REQUEST ['luckdraw_card'] );			
			$map ['luckdraw_time'] = time();
			
			$yc_probability = trim ( $_REQUEST ['yc_probability'] );
			if($map ['probability'] > $yc_probability)
			{
				$this->error ( "中奖概率不能大于".$yc_probability);
			}
			
			$f_map['weight'] = $map ['weight'];
			$cz_id = $luckdraw->where($f_map)->getField('id');
			if($cz_id)
			{
				$this->error ( "权重不能重复！");
			}
							
			if ($luckdraw->add ( $map )) {
				$this->success ( "发布成功!", U ( 'Ad/luckdraw_index' ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/luckdraw_index' ) );
			}			
		}
		else
		{
			
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/luckdraw_add.html' );	
		}
		
	}
	

	/**
	 *
	 * @name 修改奖品
	 */
	public function luckdraw_edit()
	{
		$log_str = '修改奖品';
		parent::admin_log ( $log_str );		
		$luckdraw = M ('luckdraw');
		$art['id'] = $_REQUEST['id'];
		$list = $luckdraw->where ($art)->find ();
		$this->assign ( 'list', $list );	
		$l_sum = $luckdraw->sum('probability');
		$probability = (100-$l_sum)+$list['probability'];	
		$this->assign ( 'probability', $probability );	
		if ($_REQUEST ['dosubmit']) {			
			$map ['title'] = trim ( $_REQUEST ['title'] );
			$map ['type'] = trim ( $_REQUEST ['type'] );
			$map ['weight'] = trim ( $_REQUEST ['weight'] );
			$map ['probability'] = trim ( $_REQUEST ['probability'] );
			$map ['luckdraw_card'] = trim ( $_REQUEST ['luckdraw_card'] );						
			$yc_probability = trim ( $_REQUEST ['yc_probability'] );	
			if($map ['probability'] > $yc_probability)
			{
				$this->error ( "中奖概率不能大于".$yc_probability);
			}
			
			$f_map['weight'] = $map ['weight'];
			$f_map['id'] = array('neq',$_REQUEST['yc_id']); 
			$yz_cf = $luckdraw->where($f_map)->getField('id');	
			if($yz_cf)
			{
				$this->error ( "权限重复");
			}
							
			$uap['id'] = trim($_REQUEST['yc_id']);
			$flag = $luckdraw->where ( $uap )->save($map); // 修改	
			if ($flag!==false) {
				$this->success ( "修改成功!", U ( 'Ad/luckdraw_index' ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/luckdraw_index' ) );
			}			
		}
		else
		{
			$this->assign ( 'yc_id', $_REQUEST['id'] );
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/luckdraw_edit.html' );
		}
		
	}
	
	/**
	 *
	 * @name 删除奖品
	 */
	public function luckdraw_updel() {
		$arr = '删除奖品';
		parent::admin_log ( $arr );
		$gid = $_REQUEST ['id'];
		$gids = implode ( ',', $gid ); // 批量获取gid
		$id = is_array ( $gid ) ? $gids : $gid;
		$map ['id'] = array ('in',$id);
		if (! $gid) {
			$this->error ( "请勾选记录！" );
		}
		$luckdraw = M ( "luckdraw" ); // 实例化game对象
		$flag = $luckdraw->where ( $map )->delete (); //真删除
					
		if ($flag!==false) {
			$this->success ( "删除任务成功!", U ( 'Ad/luckdraw_index' ) );
		} else {
			$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/luckdraw_index' ) );
		}
		
	}
	
	/**
	 *
	 * @name 领取任务记录
	 */
	public function task_log_index()
	{
		$log_str = '任务记录';
		parent::admin_log ( $log_str );		
		$task_log = M ( 'task_log' );
		/*查询所有任务*/
		$member = M ('member');
		$task = M ('task');
		$task_list = $task->where()->select();
		$this->assign ( 'task_list', $task_list );		
		if($_REQUEST['giftname'] >0)
		{
			$map["task_id"] = $_REQUEST['giftname'];	
		}
		
		import ( '@.ORG.Util.Page' );
		$count = $task_log->where ( $map )->count ();
		$p = new Page ( $count, 20 );
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '条记录' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>" );
		$this->assign ( 'page', $p->show () );
		$list = $task_log->where ( $map )->order ( 'id desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();		
		
		foreach ( $list as $k => $v ) {
			//查询用户名
			$mem_map['uid'] = $v ['uid'];
			$member_list = $member->where($mem_map)->getField( 'username' );		
			$list [$k] ['me_name'] = $member_list;
			//查任务名称
			$task_map['id'] = $v['task_id'];	
			$task_list = $task->where($task_map)->getField( 'title' );	
			$list [$k] ['ts_name'] = $task_list;
				
		}
			
		$this->assign ( 'list', $list );
		$this->assign ( 'giftname', $_REQUEST['giftname'] );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/task_log_index.html' );
	}
	
	
	/**
	 *
	 * @name 删除任务记录
	 */
	public function task_log_updel() {
		$arr = '删除任务领取记录';
		parent::admin_log ( $arr );
		$gid = $_REQUEST ['id'];
		$gids = implode ( ',', $gid ); // 批量获取gid
		$id = is_array ( $gid ) ? $gids : $gid;
		$map ['id'] = array ('in',$id);
		if (! $gid) {
			$this->error ( "请勾选记录！" );
		}
		$task_log = M ( "task_log" ); // 实例化game对象
		$flag = $task_log->where ( $map )->delete (); //真删除
					
		if ($flag!==false) {
			$this->success ( "删除任务记录成功!", U ( 'Ad/task_log_index' ) );
		} else {
			$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/task_log_index' ) );
		}
		
	}
	
	/**
	 *
	 * @name 中奖领取记录
	 */
	public function luckdraw_log_index()
	{
		$log_str = '中奖领取记录';
		parent::admin_log ( $log_str );		
		$luckdraw_log = M ( 'luckdraw_log' );
		/*查询所有任务*/
		$member = M ('member');
		$luckdraw = M ('luckdraw');
		$member_extend_info = M ('member_extend_info');
		$luckdraw_list = $luckdraw->where()->select();
		$this->assign ( 'luckdraw_list', $luckdraw_list );		
		if($_REQUEST['giftname'] >0)
		{
			$map["luckdraw_id"] = $_REQUEST['giftname'];	
		}
		
		import ( '@.ORG.Util.Page' );
		$count = $luckdraw_log->where ( $map )->count ();
		$p = new Page ( $count, 20 );
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '条记录' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>条记录 20条/每页</span></li>" );
		$this->assign ( 'page', $p->show () );
		$list = $luckdraw_log->where ( $map )->order ( 'type asc,luckdraw_log_time desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();		
		
		foreach ( $list as $k => $v ) {
			//查询用户名
			$mem_map['uid'] = $v ['uid'];
			$member_list = $member->where($mem_map)->getField( 'username' );
			$list [$k] ['me_name'] = $member_list;
			//查任务名称
			$luckdraw_map['id'] = $v['luckdraw_id'];	
			//$luckdraw_list = $luckdraw->where($luckdraw_map)->getField( 'title' );	
			$luckdraw_list = $luckdraw->where($luckdraw_map)->find();
			$list [$k] ['lu_name'] = $luckdraw_list['title'];
			if($luckdraw_list['type'] ==0)
			{
				//查询用户地址
				// yumao change 2015-7-24 根据uid来判断是从哪一个表中查找数据
				$member_extend_info_new = D("FenbiaoMemberExtend")->getDao(array('uid'=>$mem_map['uid']));				
				$member_extend_list = $member_extend_info_new->where($mem_map)->find();
				// change end
				// $member_extend_list = $member_extend_info->where($mem_map)->find();
				if($member_extend_list['address'] !='')
				{
					$list [$k] ['arres'] = $member_extend_list['address'].'( 邮编:'.$member_extend_list['zip_code'].')';	
				}
				
			}

				
		}
			
		$this->assign ( 'list', $list );
		$this->assign ( 'giftname', $_REQUEST['giftname'] );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/luckdraw_log_index.html' );
	}
	
	/**
	 *
	 * @name 发奖状态
	 */
	public function award($id)
	{
		$arr = '发奖状态';
		parent::admin_log ( $arr );
		$gid = $_REQUEST ['id'];
		$gids = implode ( ',', $gid ); // 批量获取gid
		$id = is_array ( $gid ) ? $gids : $gid;
		$map ['id'] = array ('in',$id);
		if (! $gid) {
			$this->error ( "请勾选记录！" );
		}
		$luckdraw_log = M ( "luckdraw_log" ); // 实例化game对象
		$mlp ['type'] = 1;
		$flag = $luckdraw_log->where ( $map )->save($mlp);
					
		if ($flag!==false) {
			$this->success ( "发奖状态修改成功!", U ( 'Ad/luckdraw_log_index' ) );
		} else {
			$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/luckdraw_log_index' ) );
		}
	}
	
	/**
	 *
	 * @name 礼品发货状态
	 */
	public function senaward($id)
	{
		$arr = '礼品发货状态';
		parent::admin_log ( $arr );
		$gid = $_REQUEST ['id'];
		$gids = implode ( ',', $gid ); // 批量获取gid
		$id = is_array ( $gid ) ? $gids : $gid;
		$map ['id'] = array ('in',$id);
		if (! $gid) {
			$this->error ( "请勾选记录！" );
		}
		$gift_log = M ( "gift_log" ); // 实例化game对象
		$mlp ['send_state'] = 1;
		$flag = $gift_log->where ( $map )->save($mlp);
					
		if ($flag!==false) {
			$this->success ( "礼品发货状态修改成功!", U ( 'Ad/gift_log_index') );
		} else {
			$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/gift_log_index' ) );
		}
	}
	
	
	/**
	 *
	 * @name 删除奖品领取记录
	 */
	public function luckdraw_log_updel() {
		$arr = '删除奖品领取记录';
		parent::admin_log ( $arr );
		$gid = $_REQUEST ['id'];
		$gids = implode ( ',', $gid ); // 批量获取gid
		$id = is_array ( $gid ) ? $gids : $gid;
		$map ['id'] = array ('in',$id);
		if (! $gid) {
			$this->error ( "请勾选记录！" );
		}
		$luckdraw_log = M ( "luckdraw_log" ); // 实例化game对象
		$flag = $luckdraw_log->where ( $map )->delete (); //真删除
					
		if ($flag!==false) {
			$this->success ( "删除礼品领取记录成功!", U ( 'Ad/luckdraw_log_index' ) );
		} else {
			$this->error ( "发生错误:" . mysql_error (), U ( 'Ad/luckdraw_log_index' ) );
		}
		
	}
	
	
	
	 
}

?>