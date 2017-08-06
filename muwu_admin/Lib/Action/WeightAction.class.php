<?php
/**
	@name 控制器
	@Author shf_l@163.com
	@copyright (C) 2012-2013 31wan
	@license	http://demo.31wan.cn/license
	@lastmodify	2013-09-07
	@version 4.0
*/
class WeightAction extends CommonAction {
	public $access;
	public function __construct() {
		parent::_initialize ();
		parent::__construct();
		$this->access = $_REQUEST ['ac'];
	}
	/**
	 * @刷新用户有权限的表
	 */
	public function user_auth_list() {
		$id=isset($this->access)?$this->access:"1";
		$map ['role_access.access_fid'] = $id;
		$map ['role_access.role_id'] = $_SESSION ['role_id'];
		$map ['category_list.hidden'] = "0";
		$model = D ( 'RoleView' );
		$data = $model->where ( $map )->order ( 'category_list.listorder asc,category_list.id asc' )->select ();
		if ($data == null) {
			$cond ['fid'] = $map ['access_fid'];
			//$cond ['role_id'] = $_SESSION ['role_id'];
			$cond ['hidden'] = "0";
			$model = M ( 'category_list' );
			$data = $model->where ( $cond )->order ( 'listorder asc,id asc' )->select ();
			return $data;
		}
		return $data;
	}

	public function index2() {
		$data = $this->user_auth_list ();
		//$id=isset($this->access)?$this->access:"1";
        $this->assign ( 'data', $data );
		$this->display ( TMPL_PATH . C ( 'ADMIN_THEME' ) . '/weight.html' );
	}
}
?>