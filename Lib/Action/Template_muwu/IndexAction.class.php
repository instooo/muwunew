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
		//新闻
		$typeid = array(19,25,26);
		$news = $this->article_list($typeid,3);	
		//案例
		$typeid = array(21,17);
		$case = $this->case_list($typeid,6);	
		
		$this->assign ( 'news', $news );
		$this->assign ( 'case', $case );
		$this->display ( TMPL_PATH . C ( "DEFAULT_GROUP" ) . '/index.html' );
	}	
}
