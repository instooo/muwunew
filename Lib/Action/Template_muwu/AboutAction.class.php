<?php

/**
 * @name 首页控制器 
 * @access private 
 * @package app 
 * @author 7477 team
 */

class AboutAction extends CommonAction{
	
	public function __construct() {
	
		parent::__construct();
		//直接设置手游的站点id 
		$this->config_list = 110;
		$this->assign ( 'pos', 'about' );
		$this->assign ( 'smalltitle', '关于我们' );
	}
	
	//列表页
	public function index() {
	
		//公司简介
		$article = M('article');
		$typeid = I('get.id','','htmlspecialchars');
		$typeid=($typeid=='')?23:$typeid;		
		
		$category = M('category');		
		$where['typeid'] = $typeid;
		$result = $category->where($where)->find();	
		
		//获取子栏目
		$fid = $result['fid']==0?$result['typeid']:	$result['fid'];			
		$map['fid'] = $fid;
		$list = $category->where($map)->select();		
		
		$this->assign ( 'result', $result);			
		$this->assign ( 'list', $list );			
		$this->display ( TMPL_PATH . C ( "DEFAULT_GROUP" ) . '/about.html' );
	}
}
