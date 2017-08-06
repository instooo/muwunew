<?php

/**
 * @name 首页控制器 
 * @access private 
 * @package app 
 * @author 7477 team
 */

class IndexAction extends CommonAction{
	
	public function __construct() {
	
		parent::__construct();
		//直接设置手游的站点id 
		$this->config_list = 110;
	}
	
	public function index() {		
		$article = M('article');
		$map['typeid'] = array('in',array(1,3,4,11));
		//新闻
		$news = $article->where($map)->limit('0,8')->select();
		unset($map);
		$type = M('category');
		//产品
		$where['fid'] = 2;
		$typeid = $type->field('typeid')->where($where)->select();
		$strtypeid='';
		foreach($typeid as $key=>$val){
			$strtypeid.=$val['typeid'].",";
		}
		$strtypeid = rtrim($strtypeid, ','); 		
		$map['typeid'] = array('in',$strtypeid);
		$map['isshouye'] = 'a';
		$product = $article->where($map)->limit('0,4')->select();	
	
		$this->assign ( 'news', $news );	
		$this->assign ( 'product', $product );			
		$this->display ( TMPL_PATH . C ( "DEFAULT_GROUP" ) . '/index.html' );
	}	
}
