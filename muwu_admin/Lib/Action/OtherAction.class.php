<?php
/**
     @name 拓展模块
	 @Author shf_l@163.com 
	 @copyright (C) 2012-2013 31wan
	 @license	http://demo.31wan.cn/license
	  @lastmodify	2013-09-07
	  @version 4.0
*/
class OtherAction extends CommonAction {
	public $config;
	public function __construct() {
		parent::_initialize ();
		parent::__construct();
		$this->config = C ( "ADMIN_THEME" );
		parent::action_access ( MODULE_NAME, ACTION_NAME );
	}
	
	/**
	 *
	 * @name 操作日志查看
	 */
	public function log() {
		import ( "ORG.Util.Page" );
		$model = M ( 'admin_exec_log' );
		$count = $model->count ( 'id' );
		$page = new Page ( $count, '25' );
		$show = $page->show ();
		$list = $model->order ( 'time desc' )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$this->assign ( 'list', $list );
		$this->assign ( 'page', $show );
		$this->display ( TMPL_PATH . $this->config . '/log.html' );
	}
	/**
	 *
	 * @name 删除日志
	 */
	public function del_log() {
		if ($_REQUEST ['id'] != "") {
			$model = M ( 'admin_exec_log' );
			foreach ( $_REQUEST ['id'] as $m )
				$map ['id'] = $m;
			$model->where ( $map )->delete ();
			$this->success ( '删除成功' );
		} else {
			$this->error ( '请选择要删除的日志' );
		}
	}
	
	/**
	 *
	 * @name 删除屏蔽的IP
	 */
	public function del_ip() {
		$model = M ( 'web_not_allow_ip' );
		
		if ($_REQUEST ['id'] != "") {
			foreach ( $_REQUEST ['id'] as $m ) {
				$map ['id'] = $m;
				$model->where ( $map )->delete ();
				$this->success ( '删除成功' );
			}
		} else {
			$this->error ( '请选择你要删除的IP' );
		}
	}
	/**
	 *
	 * @name 添加屏蔽IP地址，删除IP地址
	 */
	public function ip() {
		$model = M ( 'web_not_allow_ip' );
		
		if ($_REQUEST ['ip']) {
			$map ['ip'] = $_REQUEST ['ip'];
			$model->where ( $map )->delete ();
			$this->success ( '删除成功' );
		}
		$list = $model->select ();
		$this->assign ( 'list', $list );
		$this->display ( TMPL_PATH . $this->config . '/ip.html' );
	}
	/**
	 *
	 * @name 增加屏蔽IP地址
	 */
	public function ip_add() {
		$ip = $_REQUEST ['ip'];
		if ($ip != "") {
			$map ['ip'] = $ip;
			$model = M ( 'web_not_allow_ip' );
			$model->data ( $map )->add ();
			$this->success ( '添加黑名单成功' );
		} else {
			$this->error ( '请填入要屏蔽的IP再进行提交' );
		}
	}
	/**
	 *
	 * @name 后台栏目管理
	 */
	public function menu_list() {
		import ( "@.ORG.Util.Category" );
		$category_list = M ( 'category_list' );
		$list = $category_list->order ( 'listorder asc' )->select ();
		foreach ( $list as $k => $v ) {
			$list [$k] ['count'] = count ( explode ( '-', $v ['bpath'] ) );
		}
		$cat = new Category ( array (
				'id',
				'fid',
				'category',
				'cname' 
		) );
		$s = $cat->getTree ( $list ); // 获取分类数据树结构
		$this->assign ( 'list', $s );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/menu_list.html' );
	}
	/**
	 *
	 * @name 后台栏目排序
	 */
	public function menu_listorders() {
		if (isset ( $_REQUEST ['dosubmit'] )) {
			$category_list = M ( 'category_list' );
			foreach ( $_REQUEST ['listorders'] as $id => $listorder ) {
				$map ['listorder'] = $listorder;
				$category_list->where ( 'id =' . $id )->save ( $map ); // 根据条件保存修改的数据
			}
			$this->success ( '操作成功！' );
		} else {
			$this->success ( '操作失败！' );
		}
	}
	/**
	 *
	 * @name 增加后台栏目
	 */
	public function menu_add() {
		$log_str = '增加栏目';
		parent::admin_log ( $log_str );
		$category_list = M ( 'category_list' );
		// 查询出一级后台栏目
		$list = $category_list->where ( 'fid = 0' )->order ( 'id asc' )->select ();
		$this->assign ( 'list', $list );
		if ($_REQUEST ['dosubmit']) {
			$map ['category'] = trim ( $_REQUEST ['category'] );
			if ($map ['category'] == '') {
				$this->error ( "栏目名不能为空！" );
			}
			$map ['module_alias'] = trim ( $_REQUEST ['module_alias'] );
			if ($map ['module_alias'] == '') {
				$this->error ( "方法名不能为空！" );
			}
			$map ['hidden'] = trim ( $_REQUEST ['hidden'] );
			$map ['listorder'] = trim ( $_REQUEST ['listorder'] );
			$map ['fid'] = trim ( $_REQUEST ['fid'] );
			if ($category_list->add ( $map )) {
				$this->success ( "栏目添加成功!", U ( 'Other/menu_list' ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Other/menu_list' ) );
			}
		}
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/menu_add.html' );
	}
	/**
	 *
	 * @name 修改该后台栏目
	 */
	public function menu_edit() {
		$log_str = '修改栏目';
		parent::admin_log ( $log_str );
		$category_list = M ( 'category_list' );
		// 查询出一级后台栏目
		$list = $category_list->where ( 'fid = 0' )->order ( 'id asc' )->select ();
		$this->assign ( 'list', $list );
		$arr ['id'] = $_REQUEST ['id'];
		$info = $category_list->where ( $arr )->find ();
		$this->assign ( 'info', $info );
		if ($_REQUEST ['dosubmit']) {
			
			$map ['category'] = trim ( $_REQUEST ['category'] );
			if ($map ['category'] == '') {
				$this->error ( "栏目名不能为空！" );
			}
			$map ['module_alias'] = trim ( $_REQUEST ['module_alias'] );
			if ($map ['module_alias'] == '') {
				$this->error ( "方法名不能为空！" );
			}
			$map ['hidden'] = trim ( $_REQUEST ['hidden'] );
			$map ['listorder'] = trim ( $_REQUEST ['listorder'] );
			$map ['fid'] = trim ( $_REQUEST ['fid'] );
			$id ['id'] = trim ( $_REQUEST ['id'] );
			$st = $category_list->where ( $id )->save ( $map );
			if ($st) {
				$this->success ( "栏目修改成功!", U ( 'Other/menu_list' ) );
			} else {
				$this->error ( "发生错误:" . mysql_error (), U ( 'Other/menu_list' ) );
			}
		}
		
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/menu_edit.html' );
	}
	/**
	 *
	 * @name 删除后台栏目
	 */
	public function menu_del() {
		$log_str = '删除栏目';
		parent::admin_log ( $log_str );
		$cid = $_REQUEST ['id'];
		$cids = implode ( ',', $cid ); // 批量获取gid
		$id = is_array ( $cid ) ? $cids : $cid;
		$map ['id'] = array (
				'in',
				$id 
		);
		if (! $cid) {
			$this->error ( "请勾选记录！" );
		}
		$category_list = M ( "category_list" ); // 实例化category_list对象
		$flag = $category_list->where ( $map )->delete (); // 删除数据
		if ($flag) {
			$this->success ( "删除成功!", U ( 'Other/menu_list' ) );
		} else {
			$this->error ( "发生错误:" . mysql_error (), U ( 'Other/menu_list' ) );
		}
	}
	
	/**
	 *
	 * @name 回收站
	 */
	public function recycler()
	{
		$log_str = '回收站';
		parent::admin_log ( $log_str );	
		$mysql=$_REQUEST['Bisql']==''?"gift":$_REQUEST['Bisql']; 
		$game = M ('game');
		if($mysql=='PayView')
		{
			$gift = D ($mysql);
			$show_div = '2';
		}
		else if ($mysql=='server')
		{
			$gift = D ($mysql);
			$show_div = '3';
		}
		else
		{
			$gift = M ($mysql);	
			$show_div = '1';
		}
			
		switch($mysql)
		{
			case 'gift':
			$map ['giftdelete'] = 0;
			if($_REQUEST['giftname'] !='')
			{					
				$map ['giftname'] = $_REQUEST['giftname'];
			}				
			$yq_name ='giftname';
			$yq_id ='id';
			break;
			
			case 'article';
			$map ['articledel'] = 0;
			if($_REQUEST['giftname'] !='')
			{					
				$map ['title'] = $_REQUEST['giftname'];
			}	
			$yq_name ='title';
			$yq_id ='aid';
			break;
				
			case 'PayView':
			$map ['pay_ok.paydelete'] = '0';
			break;
			
			case 'server':
			$map ['server_delete'] = '0';
			if($_REQUEST['giftname'] !='')
			{					
				$map ['servername'] = $_REQUEST['giftname'];
			}	
			break;
						
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
		$list = $gift->where ( $map )->limit ( $p->firstRow . ',' . $p->listRows )->select ();
		
		if($mysql=='PayView')
		{
			foreach ( $list as $key => $v ) {
				$list [$key] ['bsql'] = $mysql;			
			}			
		}
		else if($mysql=='server')
		{		
			foreach ( $list as $key => $v ) {
				$list [$key] ['bsql'] = $mysql;			
				$info = $game->where ( 'gid =' . $v ['gid'] )->find ();
				$list [$key] ['gamename'] = $info ['gamename'];
			}	
		}
		else
		{
			foreach ( $list as $key => $v ) {
				$list [$key] ['re_name'] = $v[$yq_name];
				$list [$key] ['re_id'] = $v[$yq_id];
				$list [$key] ['bsql'] = $mysql;			
			}
			
		}
		
		
		$this->assign ( 'show_div', $show_div );
		$this->assign ( 'mysql', $mysql );
		$this->assign ( 'list', $list );
		$this->assign ( 'giftname', $_REQUEST['giftname'] );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/recycler_index.html' );
	}
	
	
	/**
	 *
	 * @name 回收站还原
	 */
	public function hy_update() {
		$arr = '还原回收站';
		parent::admin_log ( $arr );
		$gid = $_REQUEST ['id'];
		$gids = implode ( ',', $gid ); // 批量获取gid
		$id = is_array ( $gid ) ? $gids : $gid;		
		if (! $gid) {
			$this->error ( "请勾选记录！" );
		}
		
		$mysql = $_REQUEST['bsql'];		
		switch($mysql)
		{
			case 'gift':
			$map ['id'] = array ('in',$id);
			$nap ['giftdelete'] = 1;
			break; 
			case 'article':
			$map ['aid'] = array ('in',$id);
			$nap ['articledel'] = 1;
			break;
			case 'PayView':
			$map ['id'] = array ('in',$id);
			$nap ['paydelete'] = 1;			
			break;
			case 'server':
			$map ['sid'] = array ('in',$id);
			$nap ['server_delete'] = 1;			
			break;
		}
		
		
		if($mysql =='PayView')	
		{
			$gift = M ('pay_ok'); // 实例化game对象	
		}
		else
		{
			$gift = M ($mysql); // 实例化game对象	
		}	
			
		$flag = $gift->where ( $map )->save($nap); // 删除数据		
		if ($flag!==false) {
			$this->success ( "还原成功!", U ( 'Other/recycler?Bisql='.$mysql  ) );
		} else {
			$this->error ( "发生错误:" . mysql_error (), U ( 'Other/recycler' ) );
		}
		
	}
	
	
	/**
	 *
	 * @name 彻底删除
	 */
	public function hy_delete() {
		$arr = '彻底删除回收站';
		parent::admin_log ( $arr );
		$gid = $_REQUEST ['id'];
		$gids = implode ( ',', $gid ); // 批量获取gid
		$id = is_array ( $gid ) ? $gids : $gid;		
		if (! $gid) {
			$this->error ( "请勾选记录！" );
		}
		
		$mysql = $_REQUEST['bsql'];		
		switch($mysql)
		{
			case 'gift':
			$map ['id'] = array ('in',$id);
			break; 
			case 'article':
			$map ['aid'] = array ('in',$id);
			break;
			case 'PayView':
			$map ['id'] = array ('in',$id);
			break;
			case 'server':
			$map ['sid'] = array ('in',$id);
			break;
		}
		
		
		
		if($mysql =='PayView')	
		{
			$gift = M ('pay_ok'); // 实例化game对象	
		}
		else
		{
			$gift = M ($mysql); // 实例化game对象	
		}			
		$flag = $gift->where ( $map  )->delete (); // 删除数据
		if ($flag!==false) {
			$this->success ( "彻底删除成功!", U ( 'Other/recycler?Bisql='.$mysql  ) );
		} else {
			$this->error ( "发生错误:" . mysql_error (), U ( 'Other/recycler' ) );
		}
		
	}
	



}
?>