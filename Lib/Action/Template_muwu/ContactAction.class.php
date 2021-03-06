<?php

/**
 * @name 首页控制器 
 * @access private 
 * @package app 
 * @author 7477 team
 */

class ContactAction extends CommonAction{
	
	public function __construct() {
	
		parent::__construct();
		//直接设置手游的站点id 
		$this->config_list = 110;
		$this->assign ( 'pos', 'contact' );
		$this->assign ( 'smalltitle', '联系我们' );
	}
	
	//列表页
	public function index() {
	
		//公司简介
		$article = M('article');
		$typeid = I('get.id','','htmlspecialchars');
		if(!$typeid){
			$map['typeid'] =10;
			$list = $article->where($map)->limit('0,1')->select();		
		}else{
			$map['typeid'] = $typeid;
			$list = $article->where($map)->limit('0,1')->select();		
		}
		
		
		//获取子栏目	
		$type = M('category');		
		$where['fid'] = 5;
		$column = $type->where($where)->select();
		
		$this->assign ( 'cate', $column );	
		
		$this->assign ( 'list', $list );			
		$this->display ( TMPL_PATH . C ( "DEFAULT_GROUP" ) . '/contact.html' );
	}
}
