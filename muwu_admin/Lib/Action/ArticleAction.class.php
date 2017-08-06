<?php
/**
	 @function 文章管理
	 @file ArticleAction.class.php $
	 @Author shf_l@163.com 
	 @copyright (C) 2012-2013 31wan
	 @license	http://demo.31wan.cn/license
	  @lastmodify	2013-09-07
	  @version 4.0
*/
class ArticleAction extends CommonAction {
	public $config;
	public function __construct() {
		parent::_initialize ();
		parent::__construct ();
		$this->config = C ( "ADMIN_THEME" );
		parent::action_access ( MODULE_NAME, ACTION_NAME );
	}
	
	/*清楚大栏目静态页面*/
	public function ajx_dlm($id,$wei_id)
	{
		sleep(1);
		
		if ($_SESSION ['group'] == '') {
			
			$wei_id = $_SESSION ['qzpf_zd'];
		}
		/*查询*/
		$webcon = M ( 'webconfig' );
		$map['id'] = $wei_id;
		$webconlist = $webcon->where ($map)->find ();
		$domain  = explode('.',$webconlist['domain']);		
		switch($id)
		{
			case 1:
					$dir1 = DOC_ROOT . 'html/'.$domain[0];
					$dir2 = DOC_ROOT. 'html/'.$domain[0];
					$st = deldir ( $dir1 );
					$st = deldir ( $dir2 );			
					
			break;
			case 2://首页
					$dir = DOC_ROOT. 'html/'.$domain[0].'/index/index.html';	
					unlink ( $dir );
					$this->curl_post($webconlist['domain']);
			break;
			case 3://充值中心
					$dir = DOC_ROOT. 'html/'.$domain[0].'/pay/';
					$st = deldir ( $dir );
					$this->curl_post($webconlist['domain'].'/pay');
					
			break;
			case 4://游戏中心
					$dir = DOC_ROOT. 'html/'.$domain[0].'/hall/';
					$st = deldir ( $dir );
					$this->curl_post($webconlist['domain'].'/games');
					
			break;
			case 5://登录,注册
					$dir = DOC_ROOT. 'html/'.$domain[0].'/accounts/';
					$st = deldir ( $dir );
					$this->curl_post($webconlist['domain'].'/accounts/login');
					$this->curl_post($webconlist['domain'].'/accounts/register');
				 
			break;
			case 6://客服系统			
					$dir = DOC_ROOT. 'html/'.$domain[0].'/service/';
					$st = deldir ( $dir );
					$this->curl_post($webconlist['domain'].'/service');
			break;
			case 7://文章中心	
					$article = M ( 'article' );
					$articlemap['webconfig_id'] = $wei_id;
					$artunm = count($article->where ($map)->select());
					$zs = ceil($artunm/25);
					for($i=1;$i<=$zs;$i++)
					{
						$dir = DOC_ROOT. 'html/'.$domain[0].'/article/'.$i.'.html';				
						unlink( $dir );
					}							
			break;
			case 8://手游中心					
					$dir1 = DOC_ROOT. 'html/'.$domain[0].'/index/list';
					$dir2 = DOC_ROOT. 'html/'.$domain[0].'/index/phone';
					$dir3 = DOC_ROOT. 'html/'.$domain[0].'/index/phone_game.html';					
					deldir ( $dir1 );
					deldir ( $dir2 );		
					unlink ( $dir3 );			
			break;
		}
		$this->ajaxReturn ( $id, '成功', 1 );
	}
	
	//生成静态栏目
	public function clear_html() {
			$conn = M ( 'webconfig' );
			$list='';
			$ma_list = $conn->select ();
			for($i = 0; $i < count ( $ma_list ); $i ++) {					
				$list.= '<option value='.$ma_list[$i]["id"].'>'.$ma_list[$i]["sitename"].'</option>';
			}
			
			if ($_SESSION ['group'] == '超级管理员') 
			{
				$group_role = '1';
			} 
			else 
			{
				$group_role = '0';
			}
						
			$this->assign ( 'group_role', $group_role );	
			$this->assign ( 'op_list', $list );		
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/clear_html.html' );	
	}

	public function article_index_fl($fi_id) {
		$article = D ( 'ArticleView' );
		import ( '@.ORG.Util.Page' );
		if (isset ( $_REQUEST ['typeid'] ) && $_REQUEST ['typeid'] != '') {
			$count = $article->where ( ' article.articledel = 1 and article.typeid=' . $_REQUEST ['typeid'] . ' and article.webconfig_id=' . $fi_id )->order ( 'updatetime desc' )->count ();
			$p = new Page ( $count, 20 );
			$list = $article->where ( '  article.articledel = 1 and article.typeid=' . $_REQUEST ['typeid'] . ' and article.webconfig_id=' . $fi_id )->order ( 'updatetime desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
			$this->assign ( 'typeid', $_REQUEST ['typeid'] );
		} elseif (isset ( $_REQUEST ['status1'] ) && $_REQUEST ['status1'] != '') {			
			$count = $article->where ( ' article.articledel = 1 and article.status=' . $_REQUEST ['status1'] . ' and article.webconfig_id=' . $fi_id )->order ( 'updatetime desc' )->count ();
			$p = new Page ( $count, 20 );
			$list = $article->where ( ' article.articledel = 1 and article.status=' . $_REQUEST ['status1'] . ' and article.webconfig_id =' . $fi_id )->order ( 'updatetime desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
			$this->assign ( 'status', $_REQUEST ['status'] );
		} elseif (isset ( $_REQUEST ['title'] ) && $_REQUEST ['title'] != '') {
			$count = $article->where ( " article.articledel = 1 and article.title like '%" . trim ( $_REQUEST ['title'] ) . "%'" . ' and article.webconfig_id=' . $fi_id )->order ( 'updatetime desc' )->count ();
			$p = new Page ( $count, 20 );
			$list = $article->where ( " article.articledel = 1 and article.title like '%" . trim ( $_REQUEST ['title'] ) . "%'" . ' and article.webconfig_id=' . $fi_id )->order ( 'updatetime desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
			$this->assign ( 'title', $_REQUEST ['title'] );
		} else {
			$count = $article->where ( " article.articledel = 1 and article.webconfig_id=" . $fi_id )->order ( 'updatetime desc' )->count ();
			$p = new Page ( $count, 20 );
			$list = $article->where ( "  article.articledel = 1 and article.webconfig_id=" . $fi_id )->order ( 'updatetime desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
		}
		
		foreach ( $list as $k => $v ) {
			$str = explode ( ',', $v ['isshouye'] );
			$a = '';
			for($j = 0; $j < count ( $str ); $j ++) {
				$a .= $str [$j] == 'a' ? '<font color="red">[推荐]</font>' : '';
				$a .= $str [$j] == 'b' ? '<font color="red">[幻灯]</font>' : '';
				$a .= $str [$j] == 'c' ? '<font color="red">[置顶]</font>' : '';
				$a .= $str [$j] == 'd' ? '<font color="red">[首页]</font>' : '';
				$a .= $str [$j] == 'e' ? '<font color="red">[跳转]</font>' : '';
				$a .= $str [$j] == 'f' ? '<font color="red">[图片]</font>' : '';
				$a .= $str [$j] == 'g' ? '<font color="red">[滚动]</font>' : '';
				$a .= $str [$j] == 'h' ? '<font color="red">[加粗]</font>' : '';
				$a .= $str [$j] == 'i' ? '<font color="red">[头条]</font>' : '';
			}
			$list [$k] ['sub'] = $a;
		}
		
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '篇文章' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
			<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>篇文章 20篇/每页</span></li>" );
		$this->assign ( 'page', $p->show () );
		$this->assign ( 'list', $list );
		$this->moveop (); // 文章编辑option
		$this->jumpop (); // 快速跳转option
		$this->urlmode ();
		
		$type = M ( 'category' );
		// 获取栏目option
		$list = $type->where ( "webconfig_id =" . $fi_id )->select ();
		import ( "@.ORG.Util.Category" );
		$cat = new Category ( array (
				'typeid',
				'fid',
				'typename',
				'cname' 
		) );
		$s = $cat->getTree ( $list ); // 获取分类数据树结构
		
		/*文章前台显示的域名*/
		$webconfig = M ( 'webconfig' );
		$web_list = $webconfig->where ( "id =" . $fi_id )->find();
		
		$this->assign ( 'ym', $web_list['domain'] );
		$this->assign ( 'fi_id', $fi_id );
		$this->assign ( 'option', $s );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/article_index.html' );
	}
	
	/* 文章管理 */
	public function article_index() {
		if ($_SESSION ['group'] == "超级管理员") {
			$this->category_index_cr ( 'wz' );
		} else {
			$qzpf_zd = $_SESSION ['qzpf_zd'];
			$this->article_index_fl ( $qzpf_zd );
		}
	}
	// 发布文章
	public function add($smid = '', $amid = '') {
		if ($_SESSION ['group'] == '超级管理员') {
			$group_role = 1;
			$conn1 = M ( 'webconfig' );
			$ma_list = $conn1->select ();
		} else {
			$group_role = 0;
			$smid = $_SESSION ['qzpf_zd'];
		}
		
		for($i = 0; $i < count ( $ma_list ); $i ++) {
			$zt = '';
			if ($ma_list [$i] ['id'] == $smid) {
				$zt = 'selected';
			}
			$ma_bq .= '<option value=' . $ma_list [$i] ["id"] . ' ' . $zt . '>' . $ma_list [$i] ["sitename"] . '</option>';
		}
		//查找所属模型
		
		
		$category = M('category');		
		$where['typeid'] = $amid;
		$result = $category->where($where)->find();	
		$this->assign ( 'ma_bq', $ma_bq );
		$this->assign ( 'group_role', $group_role );
		$this->assign ( 'amid', $amid );
		$this->addop ( $smid );	
		if($result['ispage']==2){			
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/model/case/article_add.html' );
		}else{
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/article_add.html' );
		}
		
		
		
	}
	private function addop($topid = '') {
		$type = M ( 'category' );
		// 获取栏目option
		$catmap['webconfig_id'] = $topid;
		//$catmap['ispage'] = 0;
		$ma_list = $type->where ($catmap)->select ();
		import ( "@.ORG.Util.Category" );
		$cat = new Category ( array (
				'typeid',
				'fid',
				'typename',
				'cname' 
		) );
		$s = $cat->getTree ( $ma_list ); // 获取分类数据树结构		
		$this->assign ( 'option', $s );
	}
	public function doadd() {		
		$log_str = '文章发布';
		/* 判断权限 */
		if ($_SESSION ['group'] == '超级管理员') {
			$data ['webconfig_id'] = $_REQUEST ['webconfig_id'];
		} else {
			$data ['webconfig_id'] = $_SESSION ['qzpf_zd'];
		}
		parent::admin_log ( $log_str );
		if ($_REQUEST) {
			if ($_REQUEST ['content'] == "" || $_REQUEST ['typeid'] == "" || $_REQUEST ['title'] == "") {
				$this->error ( "请把文章填写完整!" );
			}
			$view = M ( "article" );
			$data ['title'] = $_REQUEST ['title'];
			if ($_REQUEST ['titlecorlor'] == "") {
				$data ['titlecorlor'] = "black";
			} else {
				$data ['titlecorlor'] = $_REQUEST ['titlecorlor'];
			}
			
			$data ['author'] = $_REQUEST ['author'];
			$data ['tags'] = $_REQUEST ['tags'];
			$data ['description'] = $_REQUEST ['description'];
			$data ['from'] = $_REQUEST ['from'];
			$data ['redirect'] = $_REQUEST ['redirect'];
			$data ['title_jl'] = $_REQUEST ['title_jl'];
			$data ['keywords'] = $_REQUEST ['keywords'];
			$data ['weight'] = $_REQUEST ['weight'];
			$data ['discuss_open'] = $_REQUEST ['discuss_open'];
			
			$pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
			$output = preg_match_all ( $pattern, stripslashes ( $_REQUEST ['content'] ), $matches );
			
			import ( '@.ORG.Image' );
			$Image = new Image ();
			
			/* 查询水印 */
			$webcon = M ( 'webconfig' );
			$webmap['id'] = $data ['webconfig_id'];
			$webconlist = $webcon->where ($webmap)->find ();
			$shuiyin = $_SERVER ['DOCUMENT_ROOT'].'/'.$webconlist ['watermark'];
			
			if(file_exists ($shuiyin ) == true && $webconlist ['is_watermark'] == 1)
			{
				/* 图片上水印 */
				for($i = 0; $i < count ( $matches [1] ); $i ++) {
					$str_pj = substr ( $matches [1] [$i], strrpos ( $matches [1] [$i], '/Public' ) );				
					$dz = '..' . $str_pj;
					$fale = $Image->water ( $dz, $shuiyin, '', 20 );
					if($fale != false)
					{
						/* 图片上传ftp服务器 */
						$zt = $this->ftp_imagearticle_com ( $str_pj, $data ['webconfig_id'] );
					}
					
					
				}
			}

			
			if ($_REQUEST ['addtime'] == "") {
				$data ['addtime'] = time ();
			} else {
				$data ['addtime'] = strtotime ( $_REQUEST ['addtime'] );
			}
			
			if ($_REQUEST ['updatetime'] == "") {
				$data ['updatetime'] = time ();
			} else {
				$data ['updatetime'] = strtotime ( $_REQUEST ['updatetime'] );
			}
			
			if($_REQUEST ['endtime']){
				$data ['endtime'] = strtotime ( $_REQUEST ['endtime'] );
			}
			
			$data ['imgurl'] = getimg ( stripslashes ( $_REQUEST ['content'] ) );
			if ($_REQUEST ['ishot'] != null) {
				$data ['ishot'] = $_REQUEST ['ishot'];
			}
			$data ['content'] = stripslashes ( $_REQUEST ['content'] );
			$data ['hits'] = $_REQUEST ['hits'];
			$data ['typeid'] = $_REQUEST ['typeid'];
			if ($_REQUEST ['isflash'] != null) {
				$data ['isflash'] = $_REQUEST ['isflash'];
			}
			if ($_REQUEST ['istop'] != null) {
				$data ['istop'] = $_REQUEST ['istop'];
			}
			$a = $_REQUEST ['ishot'] == 1 ? 'a' : '';
			$b = $_REQUEST ['isflash'] == 1 ? 'b' : '';
			$c = $_REQUEST ['istop'] == 1 ? 'c' : '';
			$d = $_REQUEST ['isshouye'] == 1 ? 'd' : '';
			$e = $_REQUEST ['istiaozhuan'] == 1 ? 'e' : '';
			$f = $_REQUEST ['istupian'] == 1 ? 'f' : '';
			$g = $_REQUEST ['isgundong'] == 1 ? 'g' : '';
			$h = $_REQUEST ['isjiacu'] == 1 ? 'h' : '';
			$i = $_REQUEST ['istoutiao'] == 1 ? 'i' : '';
			$data ['isshouye'] = $a . ',' . $b . ',' . $c . ',' . $d . ',' . $e . ',' . $f . ',' . $g . ',' . $h . ',' . $i;
			if (! empty ( $_FILES ["photo1"] ["name"] )) {
				$list_img = $_FILES ["photo1"];
				$ima_name1 = $this->ftp_image_com ( $list_img, 'Article', 'you', $data ['webconfig_id'], '1' );
				if($ima_name1==1)
				{
					$this->error('上传的图片超过2M!');
					
				}
				if($ima_name1==2)
				{
					$this->error('上传的是必须是图片');
					
				}
				$data ['article_img'] = $ima_name1; // 文章图片(缩略图加s_)
			}
			else
			{
				$data ['article_img'] = substr ( $matches [1] [0], strrpos ( $matches [1] [0], 'Public' ) );
			}
			
			$st = $view->data ( $data )->add ();
			if ($st) {
				if ($_SESSION ['group'] == '超级管理员') {
					$this->success ( '文章发布成功!', U ( 'Article/article_index_fl?fi_id=' . $data ['webconfig_id'] ) );
				} else {
					//$this->success ( '文章发布成功!', U ( 'Article/category_index' ) );
					$this->success ( '文章发布成功!', U ( 'Article/article_index_fl?fi_id=' . $data ['webconfig_id'] ) );
				}
			} else {
				$this->error ( "发生错误:" . mysql_error () );
			}
		} else {
			$this->error ( "Request Error" );
		}
	}
	
	/* 编辑文章 */
	public function edit() {
		$webconfig_id = $_REQUEST ['web_id'];
		$type = M ( 'article' );
		$arr['aid'] = $_REQUEST ['aid']; 
		$list = $type->where ($arr)->find ();		
		if ($_SESSION ['group'] == '超级管理员') {
			$group_role = 1;
			$conn1 = M ( 'webconfig' );
			$fruit = $_REQUEST ['fruit'];
			$map ['fruit'] = '';
			for($i = 0; $i < count ( $fruit ); $i ++) {
				if ($i > 0) {
					$map ['fruit'] .= ',';
				}
				$map ['fruit'] .= $fruit [$i];
			}
			$ma_list = $conn1->select ();
		} else {
			$group_role = 0;
			$smid = $_SESSION ['qzpf_zd'];
		}
		
		for($i = 0; $i < count ( $ma_list ); $i ++) {
			$zt = '';
			if ($ma_list [$i] ['id'] == $list ['webconfig_id']) {
				$zt = 'selected';
			}
			$ma_bq .= '<option value=' . $ma_list [$i] ["id"] . ' ' . $zt . '>' . $ma_list [$i] ["sitename"] . '</option>';
		}
		$weblist = M ( 'webconfig' );
		$lar['id'] = $list ['webconfig_id'];
		$list2 = $weblist->where ($lar)->find ();
		
		/* 文章的属性(首页,置顶) */
		$a = stristr ( $list ['isshouye'], 'a' ) == false ? '' : 'checked';
		$b = stristr ( $list ['isshouye'], 'b' ) == false ? '' : 'checked';
		$c = stristr ( $list ['isshouye'], 'c' ) == false ? '' : 'checked';
		$d = stristr ( $list ['isshouye'], 'd' ) == false ? '' : 'checked';
		$e = stristr ( $list ['isshouye'], 'e' ) == false ? '' : 'checked';
		$f = stristr ( $list ['isshouye'], 'f' ) == false ? '' : 'checked';
		$g = stristr ( $list ['isshouye'], 'g' ) == false ? '' : 'checked';
		$h = stristr ( $list ['isshouye'], 'h' ) == false ? '' : 'checked';
		$i = stristr ( $list ['isshouye'], 'i' ) == false ? '' : 'checked';
		
		$ls_a = " 推荐<input name='ishot' type='checkbox' class='noborder' id='ishot' value='1' " . $a . ">
				幻灯<input name='isflash' type='checkbox' class='noborder' id='IsFlash' value='1'  " . $b . " >
				置顶<input name='istop' type='checkbox' class='noborder' id='istop' value='1' " . $c . " >
				首页<input name='isshouye' type='checkbox' class='noborder' id='isshouye' value='1'  " . $d . ">
				跳转<input name='istiaozhuan' type='checkbox' class='noborder' id='istiaozhuan' value='1'  " . $e . ">
				图片<input name='istupian' type='checkbox' class='noborder' id='istupian' value='1' " . $f . ">
				滚动<input name='isgundong' type='checkbox' class='noborder' id='isgundong' value='1'  " . $g . ">
				加粗<input name='isjiacu' type='checkbox' class='noborder' id='isjiacu' value='1'  " . $h . ">
				头条<input name='istoutiao' type='checkbox' class='noborder' id='istoutiao' value='1' " . $i . ">";
		
		$this->assign ( 'ma_bq', $ma_bq );
		$this->assign ( 'group_role', $group_role );
		$this->assign ( 'amid', $amid );
		
		$this->assign ( 'list', $list );
		$this->assign ( 'ls_a', $ls_a );
		$this->editop ( $webconfig_id ); // 文章编辑option
		$this->jumpop (); // 快速跳转option
		$this->vote ( $list ['voteid'] );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/article_edit.html' );
	}
	
	public function doedit() {
		$log_str = '文章修改';
		parent::admin_log ( $log_str );
		if (empty ( $_REQUEST ['title'] )) {
			$this->error ( "标题不能为空!" );
		}
		if (empty ( $_REQUEST ['typeid'] )) {
			$this->error ( "请选择栏目!" );
		}
		if (isset ( $_REQUEST ['imgurl'] )) {
			$data ['imgurl'] = trim ( $_REQUEST ['imgurl'] );
		}
		if (! empty ( $_REQUEST ['TitleFontColor'] )) {
			$data ['titlecolor'] = trim ( $_REQUEST ['TitleFontColor'] );
		}
		$data ['aid'] = $_REQUEST ['aid'];
		$map ['aid'] = $_REQUEST ['aid'];
		$data ['redirect'] = $_REQUEST ['redirect'];
		if ($_REQUEST ['voteid'] != "") {
			$data ['voteid'] = $_REQUEST ['voteid'];
		}
		if($_REQUEST ['endtime']){
			$data ['endtime'] = strtotime ( $_REQUEST ['endtime'] );
		}
		$data ['content'] = stripslashes ( $_REQUEST ['content'] );
		$data ['title'] = trim ( $_REQUEST ['title'] );
		$data ['hits'] = trim ( $_REQUEST ['hits'] );
		$data ['typeid'] = trim ( $_REQUEST ['typeid'] );	
		$data ['title_jl'] = $_REQUEST ['title_jl'];
		$data ['keywords'] = $_REQUEST ['keywords'];
		$data ['weight'] = $_REQUEST ['weight'];
		$data ['discuss_open'] = $_REQUEST ['discuss_open'];
		$data ['updatetime'] = strtotime ( $_REQUEST ['updatetime'] );
		$data ['author'] = $_REQUEST ['author'];
		$data ['tags'] = $_REQUEST ['tags'];
		$data ['description'] = $_REQUEST ['description'];
		$data ['from'] = $_REQUEST ['from'];
				
		$a = $_REQUEST ['ishot'] == 1 ? 'a' : '';
		$b = $_REQUEST ['isflash'] == 1 ? 'b' : '';
		$c = $_REQUEST ['istop'] == 1 ? 'c' : '';
		$d = $_REQUEST ['isshouye'] == 1 ? 'd' : '';
		$e = $_REQUEST ['istiaozhuan'] == 1 ? 'e' : '';
		$f = $_REQUEST ['istupian'] == 1 ? 'f' : '';
		$g = $_REQUEST ['isgundong'] == 1 ? 'g' : '';
		$h = $_REQUEST ['isjiacu'] == 1 ? 'h' : '';
		$i = $_REQUEST ['istoutiao'] == 1 ? 'i' : '';
		
		$data ['isshouye'] = $a . ',' . $b . ',' . $c . ',' . $d . ',' . $e . ',' . $f . ',' . $g . ',' . $h . ',' . $i;
		$data ['imgurl'] = getimg ( stripslashes ( $_REQUEST ['content'] ) );
		
		/* 判断权限 */
		if ($_SESSION ['group'] == '超级管理员') {
			$data ['webconfig_id'] = $_REQUEST ['webconfig_id'];
		} else {
			$data ['webconfig_id'] = $_SESSION ['qzpf_zd'];
		}
		
		$pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/";
		$output = preg_match_all ( $pattern, stripslashes ( $_REQUEST ['content'] ), $matches );
		$first_img = $matches [1] [0];
		
		import ( '@.ORG.Image' );
		$Image = new Image ();
		
		/* 查询水印 */
		$webcon = M ( 'webconfig' );
		$symap['id'] = $data ['webconfig_id'];
		$webconlist = $webcon->where ($symap)->find ();
		$shuiyin = $_SERVER ['DOCUMENT_ROOT'].'/'.$webconlist ['watermark'];		
		
		if(file_exists ($shuiyin ) == true && $webconlist ['is_watermark'] == 1){
			/* 图片上水印 */
			for($i = 0; $i < count ( $matches [1] ); $i ++) {
				$str_pj = substr ( $matches [1] [$i], strrpos ( $matches [1] [$i], '/Public' ) );
				$dz = '..' . $str_pj;
				$fale = $Image->water ( $dz, $shuiyin, '', 20 );
				if($fale != false)
				{
					/* 图片上传ftp服务器 */
					$zt = $this->ftp_imagearticle_com ( $str_pj, $data ['webconfig_id'] );
				}
				
			}
		}
		

		
		if (! empty ( $_FILES ["photo1"] ["name"] )) {
			$list_img = $_FILES ["photo1"];
			$ima_name1 = $this->ftp_image_com ( $list_img, 'Article', 'you', $_REQUEST ['webid'], '1' );
			if($ima_name1==1)
			{
				$this->error('上传的图片超过2M!');
				
			}
			if($ima_name1==2)
			{
				$this->error('上传的是必须是图片');
				
			}
			$data ['article_img'] = $ima_name1; // 文章的图片(缩略图加个s_)
		}
		else
		{		
				if($_REQUEST['yc_article_img'] =='')
				{
					$data ['article_img'] = substr ( $matches [1] [0], strrpos ( $matches [1] [0], 'Public' ) );
				}											
		}
		
		$article = M ( 'article' );
		$artzt  = $article->where ( $map )->save ( $data );
	
		if ( $artzt !== false) {
			$this->success ( '文章修改成功!', U ( 'Article/article_index_fl?fi_id=' . $data ['webconfig_id'] ) );
		}
		else
		{
			$this->error ( '操作失败!,please contcat administrator' . mysql_error () );
		}
		
		
	}
	public function del() {
		$log_str = '文章删除';
		parent::admin_log ( $log_str );
		$article = D ( 'article' );
		
		$article_list = $article->where(' aid = '.$_REQUEST ['aid'])->field('webconfig_id')->find();
		$webconfig = M ('webconfig');
		$web_list = $webconfig->where(' id = '.$article_list['webconfig_id'])->field('is_recycler')->find();
		if($web_list['is_recycler'] == 1)
		{		
			//开启回收站			
			$nap ['articledel'] = 0;
			$flag = $article->where (' aid = '.$_REQUEST ['aid'] )->save($nap);
		}
		else
		{	
			//关闭回收站	
			$flag = $article->where (' aid = '.$_REQUEST ['aid'] )->delete ();							
		}
		
		$web_id = $_REQUEST ['web_id']; 		
		if ($flag!==false) {
			$this->success ( "操作成功!", U ( 'Article/article_index_fl?fi_id='.$web_id  ) );
		}
		$this->error ( '操作失败!' );
	}
	public function status() {
		$a = M ( 'article' );
		$map['aid'] = $_REQUEST ['aid']; 
		$web_id = $_REQUEST ['web_id']; 		
		if ($_REQUEST ['status'] == 0) {
			$a->where ($map)->setField ( 'status', 1 );
		} elseif ($_REQUEST ['status'] == 1) {
			$a->where ($map)->setField ( 'status', 0 );
		} else {
			$this->error ( '非法操作' );
		}
		
		$this->success ( "操作成功!", U ( 'Article/article_index_fl?fi_id='.$web_id  ) );
	}
	
	/*
	 * @批量修改属性 @参数 type (add-添加属性,delete-删除熟悉) @参数 map (文章的id) @参数 zf (修改的参数a,b,c,d,e,f,g,h,i @参数fi_id (站点id)
	 */
	public function delall_update($type, $map, $zf, $fi_id = '') {
		$article = D ( 'article' );
		$list = $article->where ( $map )->select ();
		
		for($i = 0; $i < count ( $list ); $i ++) {
			if ($type == 'add') {
				$data ['isshouye'] = $list [$i] ['isshouye'] . ',' . $zf;
			}
			if ($type == 'delete') {
				$str = str_replace ( $zf, '', $list [$i] ['isshouye'] );
				$data ['isshouye'] = $str;
			}
			$arr['aid'] =  $list [$i] ['aid'];
			$article->where ($arr)->save ( $data );
		}
		$this->success ( '操作成功!', U ( 'Article/article_index_fl?fi_id=' . $fi_id ) );
	}
	
	
	public function delall($fi_id = '') {
		$log_str = '文章批量删除';
		parent::admin_log ( $log_str );
		$aid = $_REQUEST ['aid']; // 获取文章aid
		$aids = implode ( ',', $aid ); // 批量获取aid
		$id = is_array ( $aid ) ? $aids : $aid;
		$map ['aid'] = array ('in',$id);
		if (! $aid) {
			$this->error ( '请勾选记录!' );
		}
		$article = D ( 'article' );
		
		if ($_REQUEST ['Del'] == '更新时间') {
			$data ['addtime'] = date ( 'Y-m-d H:i:s' );
			if ($article->where ( $map )->save ( $data )) {
				$this->error ( '操作成功!', U ( 'Article/article_index' ) );
			}
			$this->error ( '操作失败!' );
		}
		
		if ($_REQUEST ['Del'] == '删除') {
			
			$article_list = $article->where(' aid = '.$aid[0])->field('webconfig_id')->find();
			$webconfig = M ('webconfig');
			$web_list = $webconfig->where(' id = '.$article_list['webconfig_id'])->field('is_recycler')->find();
			if($web_list['is_recycler'] == 1)
			{
				$nap['articledel'] = 0;
				$flag = $article->where ( $map )->save($nap); // 假删除
			}
			else
			{
				$flag = $article->where ( $map )->delete (); //真删除
			}
			
			$web_id = $_REQUEST ['web_id']; 	
			if ($flag!==false) {
				$this->success ( "操作成功!", U ( 'Article/article_index_fl?fi_id='.$web_id  ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Article/article_index' ) );
			}
		}
		
		if ($_REQUEST ['Del'] == '批量未审') {
			$data ['status'] = 1;
			if ($article->where ( $map )->save ( $data )) {
				$this->error ( '操作成功!', U ( 'Article/article_index' ) );
			}
			$this->error ( '操作失败!' );
		}
		
		if ($_REQUEST ['Del'] == '批量审核') {
			$data ['status'] = 0;
			if ($article->where ( $map )->save ( $data )) {
				$this->error ( '操作成功!', U ( 'Article/article_index' ) );
			}
			$this->error ( '操作失败!' );
		}
		

		
		if ($_REQUEST ['Del'] == '移动') {
			$data ['typeid'] = $_REQUEST ['typeid'];
			if ($article->where ( $map )->save ( $data )) {
				$this->error ( '操作成功!', U ( 'Article/article_index' ) );
			}
			$this->error ( '操作失败!', 1 );
		}
	}
	
	
	public function delall_shenhe($fi_id = '') {
		$log_str = '文章批量审核';
		parent::admin_log ( $log_str );
		$aid = $_REQUEST ['aid']; // 获取文章aid
		$aids = implode ( ',', $aid ); // 批量获取aid
		$id = is_array ( $aid ) ? $aids : $aid;
		$map ['aid'] = array ('in',$id);
		if (! $aid) {
			$this->error ( '请勾选记录!' );
		}
		$article = D ( 'article' );		
		
		
		$web_id = $_REQUEST ['web_id']; 	

		if ($_REQUEST ['Del'] == '批量未审') {
			$data ['status'] = 1;
			if ($article->where ( $map )->save ( $data )) {
				$this->success ( "操作成功!", U ( 'Article/article_index_fl?fi_id='.$web_id  ) );
			}
			$this->error ( '操作失败!' );
		}
		
		if ($_REQUEST ['Del'] == '批量审核') {
			$data ['status'] = 0;
			if ($article->where ( $map )->save ( $data )) {
				$this->success ( "操作成功!", U ( 'Article/article_index_fl?fi_id='.$web_id  ) );
			}
			$this->error ( '操作失败!' );
		}
		

	}
	
	public function delall_shux($fi_id = '') {
		$log_str = '文章批量修改属性';
		parent::admin_log ( $log_str );
		$aid = $_REQUEST ['aid']; // 获取文章aid
		$aids = implode ( ',', $aid ); // 批量获取aid
		$id = is_array ( $aid ) ? $aids : $aid;
		$map ['aid'] = array ('in',$id);
		if (! $aid) {
			$this->error ( '请勾选记录!' );
		}
		$article = D ( 'article' );		
	
		
		if ($_REQUEST ['Del'] == '推荐') {
			$this->delall_update ( 'add', $map, 'a', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '解除推荐') {
			$this->delall_update ( 'delete', $map, 'a', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '置顶') {
			$this->delall_update ( 'add', $map, 'c', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '解除置顶') {
			$this->delall_update ( 'delete', $map, 'c', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '幻灯') {
			$this->delall_update ( 'add', $map, 'b', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '解除幻灯') {
			$this->delall_update ( 'delete', $map, 'b', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '首页') {
			$this->delall_update ( 'add', $map, 'd', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '解除首页') {
			$this->delall_update ( 'delete', $map, 'd', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '跳转') {
			$this->delall_update ( 'add', $map, 'e', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '解除跳转') {
			$this->delall_update ( 'delete', $map, 'e', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '图片') {
			$this->delall_update ( 'add', $map, 'f', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '解除图片') {
			$this->delall_update ( 'delete', $map, 'f', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '滚动') {
			$this->delall_update ( 'add', $map, 'g', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '解除滚动') {
			$this->delall_update ( 'delete', $map, 'g', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '加粗') {
			$this->delall_update ( 'add', $map, 'h', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '解除加粗') {
			$this->delall_update ( 'delete', $map, 'h', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '头条') {
			$this->delall_update ( 'add', $map, 'i', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '解除头条') {
			$this->delall_update ( 'delete', $map, 'i', $fi_id );
		}
		
		if ($_REQUEST ['Del'] == '移动') {
			$data ['typeid'] = $_REQUEST ['typeid'];
			if ($article->where ( $map )->save ( $data )) {
				$this->error ( '操作成功!', U ( 'Article/article_index' ) );
			}
			$this->error ( '操作失败!', 1 );
		}
	}
	
	// 文章模块 批量移动option
	private function moveop() {
		$type = M ( 'category' );
		$oplist = $type->where ( 'islink=0' )->field ( "typeid,typename,fid,concat(path,'-',typeid) as bpath" )->order ( 'bpath' )->select ();
		foreach ( $oplist as $k => $v ) {
			if ($v ['fid'] == 0) {
				$count [$k] = '';
			} else {
				for($i = 0; $i < count ( explode ( '-', $v ['bpath'] ) ) * 2; $i ++) {
					$count [$k] .= '&nbsp;';
				}
			}
			$op .= "<option value=\"{$v['typeid']}\">{$count[$k]}|-{$v['typename']}</option>";
		}
		$this->assign ( 'op2', $op );
	}
	
	// 文章模块 快速跳转栏目option
	private function jumpop() {
		$type = M ( 'category' );
		$oplist = $type->where ( 'islink=0' )->field ( "typeid,typename,fid,concat(path,'-',typeid) as bpath" )->order ( 'bpath' )->select ();
		foreach ( $oplist as $k => $v ) {
			if ($v ['fid'] == 0) {
				$count [$k] = '';
			} else {
				for($i = 0; $i < count ( explode ( '-', $v ['bpath'] ) ) * 2; $i ++) {
					$count [$k] .= '&nbsp;';
				}
			}
			$op .= "<option value=\"" . U ( 'Article/index?typeid=' . $v ['typeid'] ) . "\">{$count[$k]}|-{$v['typename']}</option>";
		}
		$this->assign ( 'op', $op );
	}
	
	// 文章模块-编辑-栏目option
	public function editop($web_id) {
		$article = M ( 'article' );
		$arr['aid'] = $_REQUEST ['aid'];
		$a = $article->where ($arr)->field ( 'typeid' )->find ();
		$type = M ( 'category' );
		
		// 获取栏目option
		$map['webconfig_id'] = $web_id; 
		//$map['ispage'] = 0; 
		$list = $type->where ($map)->select ();
		import ( "@.ORG.Util.Category" );
		$cat = new Category ( array (
				'typeid',
				'fid',
				'typename',
				'cname' 
		) );
		$option = $cat->getTree ( $list ); // 获取分类数据树结构
		$this->assign ( 'atypeid', $a );		
		$this->assign ( 'option', $option );
	}
	
	// 投票模块:for add()
	private function vote($vid) {
		$vote = M ( 'vote' );
		$vo = $vote->where ( 'status=1' )->getField ( 'id,title' );
		if ($vid == 0) {
			$votehtml = '<option value=\"0\" selected>不投票</option>';
		} else {
			$votehtml = '<option value=\"0\">不投票</option>';
		}
		foreach ( $vo as $k => $v ) {
			if ($k == $vid) {
				$votehtml .= "<option value=\"{$k}\" selected>{$v}</option>";
			} else {
				$votehtml .= "<option value=\"{$k}\">{$v}</option>";
			}
		}
		$this->assign ( 'votehtml', $votehtml );
		unset ( $votehtml );
	}
	// 评论模块也调用此方法
	public function urlmode() {
		$config = F ( 'basic', '', './Web/Conf/' );
		switch ($config ['urlmode']) {
			case 0 :
				$urlmode = 'index.php/';
				break;
			case 1 :
				$urlmode = '';
				break;
			case 2 :
				$urlmode = 'index.php?s=';
		}
		switch ($config ['suffix']) {
			case 0 :
				$suffix = '.html';
				break;
			case 1 :
				$suffix = '.htm';
				break;
			case 2 :
				$suffix = '.shtml';
				break;
		}
		$this->assign ( 'urlmode', $urlmode );
		$this->assign ( 'suffix', $suffix );
		unset ( $config );
	}
	public function search() {
		$article = D ( 'ArticleView' );
		import ( '@.ORG.Util.Page' );
		$map ['title'] = array ('like','%' . $_REQUEST ['keywords'] . '%');
		$count = $article->where ( $map )->order ( 'addtime desc' )->count ();
		$p = new Page ( $count, 20 );
		$list = $article->where ( $map )->order ( 'addtime desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '篇文章' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
		<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>篇文章 20篇/每页</span></li>" );
		$this->assign ( 'page', $p->show () );
		$this->assign ( 'list', $list );
		$this->moveop (); // 文章编辑option
		$this->jumpop (); // 快速跳转option
		$this->urlmode ();
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/article_index.html' );
	}
	
	/*站点列表*/
	public function category_index_cr($type) {		
		import ( '@.ORG.Util.Page' );
		$admin_role = M ( "webconfig" ); // 实例化admin_role对象
		$count = $admin_role->order ( 'id asc' )->count (); // 超级管理员查询所有的
		$p = new Page ( $count, 20 );
		$list = $admin_role->order ( 'id asc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '区服' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
			<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>篇文章 20篇/每页</span></li>" );
		$this->assign ( 'page', $p->show () );
		
		$this->assign ( 'list', $list );
		switch ($type) {
			case "lm" :
				$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/category_index_cr.html' );
				break;
			case "wz" :
				$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/category_index_cr.html' );
				break;
			case "tz" :
				$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/cnotice_index_cr.html' );
				break;
		}
	}
	
	/* 公告列表 */
	function fenli_notice($qzpf_zd) {
		import ( "@.ORG.Util.Category" );
		$type = M ( 'notice' );
		$map['webconfig_id'] = $qzpf_zd;
		$list = $type->where ($map)->order ( 'id desc' )->select ();
		$this->assign ( 'lm_id', $qzpf_zd );
		$this->assign ( 'list', $list );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/notice_index.html' );
	}
	
	
	/* 栏目列表 */
	public function fenli($qzpf_zd) {
		import ( "@.ORG.Util.Category" );
		$type = M ( 'category' );
		$article = M ( 'article' );
		$map['webconfig_id'] = $qzpf_zd;
		$list = $type->where ($map)->order ( 'typeid desc' )->select ();
		foreach ( $list as $k => $v ) {
			$list [$k] ['count'] = count ( explode ( '-', $v ['bpath'] ) );
			$list [$k] ['total'] = $article->where ( 'typeid=' . $v ['typeid'] )->count ();
			
		}
		$cat = new Category ( array (
				'typeid',
				'fid',
				'typename',
				'cname' 
		) );
		$s = $cat->getTree ( $list ); // 获取分类数据树结构
		
		$this->assign ( 'lm_id', $qzpf_zd );
		$this->assign ( 'list', $s );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/category_index.html' );
	}
	
	
	/*网站公告*/
	public function notice_index() {
		if ($_SESSION ['group'] == "超级管理员") {
			$this->category_index_cr ( 'tz' );
		} else {
			$qzpf_zd = $_SESSION ['qzpf_zd'];
			$this->fenli_notice ( $qzpf_zd );
		}
	}
	
	/* 栏目管理 */
	public function category_index() {
		if ($_SESSION ['group'] == "超级管理员") {
			$this->category_index_cr ( 'lm' );
		} else {
			$qzpf_zd = $_SESSION ['qzpf_zd'];
			$this->fenli ( $qzpf_zd );
		}
	}
	
	/* 添加公告跳转 */
	public function notice_add($topid = '') {
		$type = M ( 'category' );
		if ($_SESSION ['group'] == '超级管理员') {
			$group_role = 1;
			$conn1 = M ( 'webconfig' );
			$ma_list = $conn1->select ();
		} else {
			$group_role = 0;
			$topid = $_SESSION ['qzpf_zd'];
		}
		
		$sel = '<select id="webconfig_id" name="webconfig_id" disabled="disabled">'; //
		for($i = 0; $i < count ( $ma_list ); $i ++) {
			$zt = '';
			if ($ma_list [$i] ['id'] == $topid) {
				$zt = 'selected';
			}
			$sel .= '<option value=' . $ma_list [$i] ['id'] . ' ' . $zt . '>' . $ma_list [$i] ['sitename'] . '</option>';
		}
		$sel .= '</select>';
		
		$this->assign ( 'ma_list', $ma_list );
		$this->assign ( 'group_role', $group_role );
		$this->assign ( 'sel', $sel );
		$this->assign ( 'topid', $topid );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/notice_add.html' );
	}
	
	/* 添加栏目跳转 */
	public function category_add($topid = '') {
		$type = M ( 'category' );
		if ($_SESSION ['group'] == '超级管理员') {
			$group_role = 1;
			$conn1 = M ( 'webconfig' );
			$ma_list = $conn1->select ();
		} else {
			$group_role = 0;
			$topid = $_SESSION ['qzpf_zd'];
		}
		// 获取栏目option
		$arr['webconfig_id'] = $topid;
		$list = $type->where ($arr)->select ();
		import ( "@.ORG.Util.Category" );
		$cat = new Category ( array (
				'typeid',
				'fid',
				'typename',
				'cname' 
		) );
		
		$sel = '<select id="webconfig_id" name="webconfig_id" disabled="disabled">';
		for($i = 0; $i < count ( $ma_list ); $i ++) {
			$zt = '';
			if ($ma_list [$i] ['id'] == $topid) {
				$zt = 'selected';
			}
			$sel .= '<option value=' . $ma_list [$i] ['id'] . ' ' . $zt . '>' . $ma_list [$i] ['sitename'] . '</option>';
		}
		$sel .= '</select>';
		
		$s = $cat->getTree ( $list ); // 获取分类数据树结构
		$this->assign ( 'ma_list', $ma_list );
		$this->assign ( 'group_role', $group_role );
		$this->assign ( 'sel', $sel );
		$this->assign ( 'topid', $topid );
		$this->assign ( 'list', $s );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/category_add.html' );
	}
	
	/* 公告增加 */
	public function notice_doadd() {
		$log_str = '公告增加';
		parent::admin_log ( $log_str );
		if ($_SESSION ['group'] == '超级管理员') {
			
			$qzpf_zd = $_REQUEST ['webconfig_id'];
		} else {
			$qzpf_zd = $_SESSION ['qzpf_zd'];
		}
		
		if (empty ( $_REQUEST ['title'] )) {
			$this->error ( '栏目名称不能为空!' );
		}
		
		$data ['content'] = stripslashes ( $_REQUEST ['content'] );
		
		$type = M ( 'notice' );
		if ($type->create ()) {
			$map = $type->create ();
			$map ['webconfig_id'] = $qzpf_zd;
			if ($type->add ( $map )) {
				
				if ($_SESSION ['group'] == '超级管理员') {
					$this->success ( '操作成功!', U ( 'Article/fenli_notice?qzpf_zd=' . $map ['webconfig_id'] ) );
				} else {
					$this->success ( '操作成功!', U ( 'Article/fenli_notice?qzpf_zd=' . $map ['webconfig_id'] ) );
				}
			}
		}
	}
	
	/* 增加栏目 */
	public function category_doadd() {		
		$log_str = '栏目增加';
		parent::admin_log ( $log_str );
		if ($_SESSION ['group'] == '超级管理员') {
			$qzpf_zd = $_REQUEST ['webconfig_id'];
		} else {
			$qzpf_zd = $_SESSION ['qzpf_zd'];
		}		

		if (empty ( $_REQUEST ['typename'] )) {
			$this->error ( '栏目名称不能为空!' );
		}		
		
		$type = D ( 'Category' );
		
		if ($type->create ()) {
			$map = $type->create ();			
			$map ['webconfig_id'] = $qzpf_zd;
			if ($type->add ( $map )) {				
				if ($_SESSION ['group'] == '超级管理员') {
					$this->success ( '操作成功!', U ( 'Article/fenli?qzpf_zd=' . $map ['webconfig_id'] ) );
				} else {
					$this->success ( '操作成功!', U ( 'Article/fenli?qzpf_zd=' . $map ['webconfig_id'] ) );
				}
			}
			else
			{
				$this->success ( '操作失败!', U ( 'Article/fenli?qzpf_zd='.$map ['webconfig_id']) );
			}				
		}
	}
	/* 修改通知 */
	public function notice_edit() {
		$type = M ( 'notice' );
		$weblist = M ( 'webconfig' );
		if ($_SESSION ['group'] == '超级管理员') {
			$group_role = 1;
			$conn1 = M ( 'webconfig' );			
			$ma_l = $conn1->select ();
		} else {
			$group_role = 0;
		}
		$map['id'] =  $_REQUEST ['typeid'];
		$list = $type->where ($map)->find ();
		$dm = '<select id="webconfig_id" name="webconfig_id" disabled="disabled">';
		for($i = 0; $i < count ( $ma_l ); $i ++) {
			$zt = '';
			if ($ma_l [$i] ['id'] == $list ['webconfig_id']) {
				$zt = 'selected';
			}
			$dm .= "<option value=" . $ma_l [$i] ['id'] . " " . $zt . ">" . $ma_l [$i] ['sitename'] . "</option>";
		}
		$dm .= '</select>';
		$this->assign ( 'dm_list', $dm );
		$this->assign ( 'group_role', $group_role );
		$this->assign ( 'list', $list );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/notice_edit.html' );
	}
	
	/* 修改栏目 */
	public function category_edit() {
		$type = M ( 'category' );
		$weblist = M ( 'webconfig' );
		if ($_SESSION ['group'] == '超级管理员') {
			$group_role = 1;
			$conn1 = M ( 'webconfig' );
			$ma_l = $conn1->select ();
		} else {
			$group_role = 0;
		}
		$arr['typeid'] = $_REQUEST ['typeid'];
		$list = $type->where ($arr)->find ();
		
		// 获取栏目option
		$srr['webconfig_id'] = $_REQUEST ['web_id'];
		$list1 = $type->where ($srr)->select (); // 全部信息更爱等级option
		import ( "@.ORG.Util.Category" );
		$cat = new Category ( array (
				'typeid',
				'fid',
				'typename',
				'cname' 
		) );
		
		// 站点option
		$dm = '<select id="webconfig_id" name="webconfig_id" disabled="disabled">';
		for($i = 0; $i < count ( $ma_l ); $i ++) {
			$zt = '';
			if ($ma_l [$i] ['id'] == $list ['webconfig_id']) {
				$zt = 'selected';
			}
			$dm .= "<option value=" . $ma_l [$i] ['id'] . " " . $zt . ">" . $ma_l [$i] ['sitename'] . "</option>";
		}
		$dm .= '</select>';
		$option = $cat->getTree ( $list1 ); // 获取分类数据树结构
		$this->assign ( 'option', $option );
		$this->assign ( 'dm_list', $dm );
		$this->assign ( 'group_role', $group_role );		
		$this->assign ( 'list', $list );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/category_edit.html' );
	}
	
	// 执行通知编辑
	public function notice_cat() {
		$log_str = '通知修改';
		parent::admin_log ( $log_str );
		if ($_SESSION ['group'] == '超级管理员') {
			$web_id = $_REQUEST ['webconfig_id'];
		} else {
			$web_id = $_SESSION ['qzpf_zd'];
		}
		if (empty ( $_REQUEST ['title'] )) {
			$this->error ( '公告标题不能为空!' );
		}
		$type = D ( 'notice' );
		$type->create ();
		$map['id'] = $_REQUEST ['typeid'];
		$result = $type->where ( $map )->save ($date);
		if ($result !== false) {
				$this->success ( '操作成功!', U ( 'Article/fenli_notice?qzpf_zd=' . $web_id ) );		
		}
		else
		{
			$this->error ( '操作失败!' );
		}
		
	}
	
	// 栏目修改
	public function doedit_cat() {
		$log_str = '栏目修改';
		parent::admin_log ( $log_str );
		if ($_SESSION ['group'] == '超级管理员') {
			$map ['webconfig_id'] = $_REQUEST ['webconfig_id'];
		} else {
			$map ['webconfig_id'] = $_SESSION ['qzpf_zd'];
		}

		if (empty ( $_REQUEST ['typename'] )) {
			$this->error ( '栏目名称不能为空!' );
		}
		$type = D ( 'Category' );
		$arr['typeid'] = $_REQUEST ['typeid'];
		$list = $type->where($arr)->find ();
		$map = $type->create();
		
		if ($type->save($map)!==false) {				
			$this->success ( '操作成功!', U ( 'Article/fenli?qzpf_zd='.$map ['webconfig_id'] ) );			
		}
		else
		{
			$this->error ( '操作失败!' );
		}
		
	}
	
	/* 执行通知删除 */
	public function notice_del() {
		$log_str = '通知删除';
		parent::admin_log ( $log_str );
		$type = M ( 'notice' );
		$map['id'] = $_REQUEST ['typeid'];
		if ($type->where ($map)->delete ()) {
			$this->success ( '操作成功!', U ( 'Article/fenli_notice?qzpf_zd=' . $_REQUEST ['web_id'] ) );
			// $this->success ( '操作成功!', U ( 'Article/fenli?qzpf_zd='.$_REQUEST ['web_id']) );
		}
	}
	
	// 删除栏目&执行删除
	public function category_del() {
		$log_str = '栏目删除';
		parent::admin_log ( $log_str );
		$type = M ( 'category' );
		$article = M ( 'article' );
		$fid['fid'] = $_REQUEST ['typeid'];
		if ($type->where ($fid)->select ()) {
			$this->error ( '请先删除子栏目!', U ( 'Article/category_index' ) );
		}
		$typeid['typeid'] =  $_REQUEST ['typeid'];
		if ($article->where ($typeid)->select ()) {
			$this->error ( '请先清空栏目下文章!', U ( 'Article/category_index' ) );
		}
		if ($type->where ($typeid)->delete ()) {
			// $this->success ( '删除成功!', U ( 'Article/category_index' ) );
			$this->success ( '操作成功!', U ( 'Article/fenli?qzpf_zd=' . $_REQUEST ['web_id'] ) );
		}
	}
	public function category_status() {
		$a = M ( 'category' );
		$s = explode ( "-", $_REQUEST ['s'] );
		if ($s [1] == 0) {
			$a->where ( 'typeid=' . $_REQUEST ['typeid'] )->setField ( $s [0], 1 );
		} elseif ($s [1] == 1) {
			$a->where ( 'typeid=' . $_REQUEST ['typeid'] )->setField ( $s [0], 0 );
		} else {
			$this->error ( '非法操作' );
		}
		$this->redirect ( U ( 'Article/index' ) );
	}
	
	/*批量静态化文章*/
	public function ajax_pl_jth($str,$web_id)
	{
		sleep(1);
		$article = M ( 'article' );

		$webcon = M ( 'webconfig' );
		$map['id'] = $web_id;
		$webconlist = $webcon->where ($map)->find ();
		$domain  = explode('.',$webconlist['domain']);
		$arr = explode(',',$str);
		$j=0;
		for($i=0;$i<count($arr);$i++)
		{
			$mapcont['aid'] = $arr[$i]; 	
			$artcont = $article->where ($mapcont)->find ();
			$pages = explode('_baidu_page_break_tag_',$artcont['content']);	
			for($n=1;$n<=count($pages);$n++)
			{
				$dir = $_SERVER ['DOCUMENT_ROOT'] . '/html/'.$domain[0].'/article/read/' . $arr[$i]. '_'.$n.'.html';		
				$dir1 = $_SERVER ['DOCUMENT_ROOT'] . '/html/'.$domain[0].'/article/read/' . $arr[$i]. '_.html';		
				unlink ( $dir );
				unlink ( $dir1 );
				$url = "http://".$webconlist['domain']."/article/read?aid=".$arr[$i]."&p=".$n;
				$this->curl_post($url);
			}			
			$j++;
		}		
		$this->ajaxReturn ($j, '成功', 1 );
	}
	

		
	/*静态化单片文章*/
	public function ajax_jth($aid) {
		sleep(1);
		$article = M ( 'article' );
		$arr['aid'] = $aid;
		$articlelist = $article->where ($arr)->find ();	
		$webcon = M ( 'webconfig' );
		$map['id'] = $articlelist['webconfig_id'];
		$webconlist = $webcon->where ($map)->find ();
		$domain  = explode('.',$webconlist['domain']);
		
		$pages = explode('_baidu_page_break_tag_',$articlelist['content']);	
		for($n=1;$n<=count($pages);$n++)
		{
			$dir = $_SERVER ['DOCUMENT_ROOT'] . '/html/'.$domain[0].'/article/read/' . $aid. '_'.$n.'.html';	
			$dir1 = $_SERVER ['DOCUMENT_ROOT'] . '/html/'.$domain[0].'/article/read/' . $aid. '_.html';//删除前台生成的			
			unlink ( $dir );
			unlink ( $dir1);
			$url = "http://".$webconlist['domain']."/article/read?aid=".$aid."&p=".$n;
			//exit($url);
			$this->curl_post($url);
		}		
		$this->ajaxReturn ($aid, '成功', 1 );
	}
	
	/*按日期静态化文章*/
	public function ajax_time_article($wei_id,$ksj,$jsj)
	{
		//set_time_limit(0); 
		sleep(1);
		if ($_SESSION ['group'] == '') {
			
			$wei_id = $_SESSION ['qzpf_zd'];
		}		
		$ksj = strtotime($ksj.'00:00:00');//比他大
		$jsj = strtotime($jsj.'23:59:59');//比他小
		
		$webcon = M ( 'webconfig' );
		$map['id'] = $wei_id;
		$webconlist = $webcon->where ($map)->find ();
		$domain  = explode('.',$webconlist['domain']);	
		/*查询文章*/
		$article = M ( 'article' );
		$article_map['webconfig_id']  = $wei_id;
		$article_map['updatetime']  = array('between',''.$ksj.','.$jsj.'');
		$arr = $article->where ($article_map)->select();//' updatetime >'.$ksj.' and updatetime <'.$jsj
		$j++;
		
		
		for($i=0;$i<count($arr);$i++)
		{
			/*查询单片的文章的页数*/
			$pages = explode('_baidu_page_break_tag_',$arr[$i]['content']);			 
			for($n=1;$n<=count($pages);$n++)			{
		
				$dir = $_SERVER ['DOCUMENT_ROOT'] . '/html/'.$domain[0].'/article/read/' . $arr[$i]['aid']. '_'.$n.'.html';
				$dir1 = $_SERVER ['DOCUMENT_ROOT'] . '/html/'.$domain[0].'/article/read/' . $arr[$i]['aid']. '_.html';
				unlink ( $dir );
				unlink ( $dir1 );
				$url = "http://".$webconlist['domain']."/article/read?aid=".$arr[$i]['aid']."&p=".$n;
				$this->curl_post($url);
			}			
			//$url = "http://".$webconlist['domain']."/article/read?aid=".$arr[$i]['aid'];
			
			
			$j++;
		}	
		$this->ajaxReturn ($j, '成功', 1 );
	}
	
	/*按照栏目静态化*/
	public function ajax_lm_article($wei_id,$ld_lm)
	{
		sleep(1);
		if ($_SESSION ['group'] == '') {
			
			$wei_id = $_SESSION ['qzpf_zd'];
		}
		
		$webcon = M ( 'webconfig' );
		$map['id'] = $wei_id;
		$webconlist = $webcon->where ($map)->find ();
		$domain  = explode('.',$webconlist['domain']);	
		
		/*查询栏目*/	
		$category = M ( 'category' );
		$map['webconfig_id']  = $webconlist['id'];
		$map['typeid']  = $ld_lm;
		$arr = $category->where ($map)->find();		
		$dir = $_SERVER ['DOCUMENT_ROOT'] . '/html/'.$domain[0].'/article/list/'.$arr['typeid'];
		$st = deldir ( $dir );
		$this->ajaxReturn ('1', '成功', 1 );
	}
	
	// 发布案例
	public function caselist($smid = '', $amid = '') {
		$smid=110;
		import ( '@.ORG.Util.Page' );
		$case = M('case');
		if (isset ( $_REQUEST ['typeid'] ) && $_REQUEST ['typeid'] != '') {			
			$count = $case->where ( 'typeid=' . $_REQUEST ['typeid'] )->order ( 'addtime desc' )->count ();
			$p = new Page ( $count, 20 );
			$list = $case->where ( ' typeid='.$_REQUEST ['typeid'])->order ( 'addtime desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
			$this->assign ( 'typeid', $_REQUEST ['typeid'] );
		} elseif (isset ( $_REQUEST ['title'] ) && $_REQUEST ['title'] != '') {
			$count = $case->where ( "title like '%" . trim ( $_REQUEST ['title'] ) . "%'" )->order ( 'addtime desc' )->count ();
			$p = new Page ( $count, 20 );
			$list = $case->where ( "title like '%" . trim ( $_REQUEST ['title'] ) . "%'")->order ( 'addtime desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
			$this->assign ( 'title', $_REQUEST ['title'] );		
		} else {
			$count = $case->order ( 'addtime desc' )->count ();
			$p = new Page ( $count, 20 );
			$list = $case->order ( 'addtime desc' )->limit ( $p->firstRow . ',' . $p->listRows )->select ();			
		}
		$p->setConfig ( 'prev', '上一页' );
		$p->setConfig ( 'header', '篇文章' );
		$p->setConfig ( 'first', '首 页' );
		$p->setConfig ( 'last', '末 页' );
		$p->setConfig ( 'next', '下一页' );
		$p->setConfig ( 'theme', "%first%%upPage%%linkPage%%downPage%%end%
			<li><span><select name='select' onChange='javascript:window.location.href=(this.options[this.selectedIndex].value);'>%allPage%</select></span></li>\n<li><span>共<font color='#009900'><b>%totalRow%</b></font>篇文章 20篇/每页</span></li>" );
			
		$type = M ( 'category' );
		// 获取栏目option
		$type = $type->select ();		
		foreach($type as $key=>$val){
			$typearr[$val['typeid']]=$val['typename'];
		}
		foreach($list as $key=>$val){
			$list[$key]['typename']=$typearr[$val['typeid']];
		}	

		import ( "@.ORG.Util.Category" );
		$cat = new Category ( array (
				'typeid',
				'fid',
				'typename',
				'cname' 
		) );	
		$this->assign ( 'page', $p->show () );
		$this->assign ( 'list', $list );
		$this->assign ( 'option', $type );		
		$this->assign ( 'fi_id', $smid );
		$this->assign ( 'amid', $amid );			
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/model/case/case_index.html' );
	}	
	// 发布案例
	public function addcase($smid = '', $amid = '') {		
		if(IS_POST){			
			$log_str = '添加案例';
			/* 判断权限 */
			if ($_SESSION ['group'] == '超级管理员') {
				$data ['webconfig_id'] = $_REQUEST ['webconfig_id'];
			} else {
				$data ['webconfig_id'] = $_SESSION ['qzpf_zd'];
			}
			parent::admin_log ( $log_str );	

			if ($_REQUEST ['typeid'] == "" || $_REQUEST ['title'] == "") {
				$this->error ( "请把文章填写完整!" );
			}		
			$data ['title'] = $_REQUEST ['title'];	
			$data ['title_jl'] = $_REQUEST ['title_jl'];
			$data ['weight'] = $_REQUEST ['weight'];			
			$data ['author'] = $_REQUEST ['author'];
			$data ['description'] = $_REQUEST ['description'];
			$data ['addtime'] = time();				
			$data ['contentbj'] = $_REQUEST ['contentbj'];
			$data ['contentxq'] = $_REQUEST ['contentxq'];	
			$data ['typeid'] = $_REQUEST ['typeid'];
			$data ['status'] = 1;			
			if (!empty ( $_FILES ["uploads"] ["name"][0] )) {				
				$list_img = $_FILES ["uploads"];				
				$ima_name1 = $this->ftp_image_com_multiple ( $list_img, 'Article', 'you', $data ['webconfig_id'], '1' );
				if($ima_name1==1)
				{
					$this->error('上传的图片超过2M!');
					
				}
				if($ima_name1==2)
				{
					$this->error('上传的是必须是图片');
					
				}
				$data ['article_img'] = json_encode($ima_name1); // 文章图片(缩略图加s_)				
			}		
			$data ['imgurl'] = $ima_name1[0];
			$st = M('case')->data ( $data )->add ();
			if($st){
				$this->success ( '文章发布成功!', U ( 'Article/caselist?fi_id=' . $data ['webconfig_id'] ) );
			}
			
		}else{
			$smid=($smid=='')?110:$smid;
			//$amid=($amid=='')?21:$amid;
			if ($_SESSION ['group'] == '超级管理员') {
				$group_role = 1;
				$conn1 = M ( 'webconfig' );
				$ma_list = $conn1->select ();
			} else {
				$group_role = 0;
				$smid = $_SESSION ['qzpf_zd'];
			}		
			for($i = 0; $i < count ( $ma_list ); $i ++) {
				$zt = '';
				if ($ma_list [$i] ['id'] == $smid) {
					$zt = 'selected';
				}
				$ma_bq .= '<option value=' . $ma_list [$i] ["id"] . ' ' . $zt . '>' . $ma_list [$i] ["sitename"] . '</option>';
			}
			
			$this->assign ( 'ma_bq', $ma_bq );
			$this->assign ( 'group_role', $group_role );
			$this->assign ( 'amid', $amid );
			$this->addop ( $smid );			
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/model/case/case_add.html' );	
		}		
	}	
	// 发布案例
	public function editcase($smid = '', $amid = '') {
		if(IS_POST){
			$log_str = '修改案例';
			/* 判断权限 */
			if ($_SESSION ['group'] == '超级管理员') {
				$data ['webconfig_id'] = $_REQUEST ['webconfig_id'];
			} else {
				$data ['webconfig_id'] = $_SESSION ['qzpf_zd'];
			}
			parent::admin_log ( $log_str );	

			if ($_REQUEST ['typeid'] == "" || $_REQUEST ['title'] == "") {
				$this->error ( "请把文章填写完整!" );
			}	
			$data ['aid'] = $_REQUEST ['aid'];
			$map ['aid'] = $_REQUEST ['aid'];			
			$data ['title'] = $_REQUEST ['title'];	
			$data ['title_jl'] = $_REQUEST ['title_jl'];
			$data ['weight'] = $_REQUEST ['weight'];			
			$data ['author'] = $_REQUEST ['author'];
			$data ['description'] = $_REQUEST ['description'];
			$data ['addtime'] = time();				
			$data ['contentbj'] = $_REQUEST ['contentbj'];
			$data ['contentxq'] = $_REQUEST ['contentxq'];	
			$data ['typeid'] = $_REQUEST ['typeid'];
			$data ['status'] = 1;
			$old_art=json_decode(urldecode($_REQUEST['old_art']));				
			if (!empty ( $_FILES ["uploads"] ["name"][0] )) {				
				$list_img = $_FILES ["uploads"];				
				$ima_name1 = $this->ftp_image_com_multiple ( $list_img, 'Article', 'you', $data ['webconfig_id'], '1' );
				if($ima_name1==1)
				{
					$this->error('上传的图片超过2M!');
					
				}
				if($ima_name1==2)
				{
					$this->error('上传的是必须是图片');
					
				}				
				$old_art =($old_art=="")?$ima_name1:array_merge($old_art,$ima_name1);						
			}				
			$data ['article_img']=json_encode($old_art);				
			$data ['imgurl'] = $ima_name1[0];
			$st = M('case')->where ( $map )->save ( $data );
			if($st){
				$this->success ( '文章发布成功!', U ( 'Article/caselist?fi_id=' . $data ['webconfig_id'] ) );
			}
			
		}else{			
			$webconfig_id = $_REQUEST ['web_id'];
			$case = M ( 'case' );
			$arr['aid'] = $_REQUEST ['aid']; 
			$list = $case->where ($arr)->find ();		
			if ($_SESSION ['group'] == '超级管理员') {
				$group_role = 1;
				$conn1 = M ( 'webconfig' );
				$fruit = $_REQUEST ['fruit'];
				$map ['fruit'] = '';
				for($i = 0; $i < count ( $fruit ); $i ++) {
					if ($i > 0) {
						$map ['fruit'] .= ',';
					}
					$map ['fruit'] .= $fruit [$i];
				}
				$ma_list = $conn1->select ();
			} else {
				$group_role = 0;
				$smid = $_SESSION ['qzpf_zd'];
			}
			
			for($i = 0; $i < count ( $ma_list ); $i ++) {
				$zt = '';
				if ($ma_list [$i] ['id'] == $list ['webconfig_id']) {
					$zt = 'selected';
				}
				$ma_bq .= '<option value=' . $ma_list [$i] ["id"] . ' ' . $zt . '>' . $ma_list [$i] ["sitename"] . '</option>';
			}
			$weblist = M ( 'webconfig' );
			$lar['id'] = $list ['webconfig_id'];
			$list2 = $weblist->where ($lar)->find ();
			$list['article_img_json'] = urlencode($list['article_img']);
			$list['article_img'] = json_decode($list['article_img']);			
			$this->assign ( 'ma_bq', $ma_bq );
			$this->assign ( 'group_role', $group_role );
			$this->assign ( 'amid', $list['typeid'] );			
			$this->assign ( 'list', $list );
			$this->assign ( 'ls_a', $ls_a );
			$this->editop ( $webconfig_id ); // 文章编辑option
			$this->jumpop (); // 快速跳转option
			$this->vote ( $list ['voteid'] );	
			$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/model/case/case_edit.html' );	
		}		
	}
	// 发布案例
	public function delcase($smid = '', $amid = '') {		
		$log_str = '案例删除';
		parent::admin_log ( $log_str );
		$case = M ( 'case' );		
		$article_list = $case->where(' aid = '.$_REQUEST ['aid'])->field('webconfig_id')->find();
		$webconfig = M ('webconfig');
		$web_list = $webconfig->where(' id = '.$article_list['webconfig_id'])->field('is_recycler')->find();
		$flag = $case->where (' aid = '.$_REQUEST ['aid'] )->delete ();	
		$web_id = $_REQUEST ['web_id']; 		
		if ($flag!==false) {
			$this->success ( '案例删除成功!', U ( 'Article/caselist?fi_id=' . $web_id ) );
		}		
	}
	
	
}

?>