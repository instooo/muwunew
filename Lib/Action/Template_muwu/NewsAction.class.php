<?php

/**
 * @name 首页控制器 
 * @access private 
 * @package app 
 * @author 7477 team
 */

class NewsAction extends CommonAction{
	
	public function __construct() {
	
		parent::__construct();
		//直接设置手游的站点id 
		$this->config_list = 110;
		$this->assign ( 'pos', 'news' );
		$this->assign ( 'smalltitle', '新闻中心' );
	}
	
	//列表页
	public function index() {
	
		//产品列表	
		$article = M('article');
		$typeid = I('get.id','','htmlspecialchars');
		$typeida=($typeid=='')?19:$typeid;	
	
		$category = M('category');		
		$where['typeid'] = $typeida;
		$result = $category->where($where)->find();	
		
		//获取子栏目
		$fid = $result['fid']==0?$result['typeid']:	$result['fid'];			
		$map['fid'] = $fid;
		$list = $category->where($map)->select();
		
		if(!$typeid){
			$typeidarr = array_column($list,'typeid');
		}else{		
			$typeidarr = array($typeid);
		}
		$map['typeid'] = array('in',$typeidarr);
		import('ORG.Util.Page');// 导入分页类
		$count      = $article->where($map)->count();
		$Page       = new Page($count,12);
		$show       = $Page->show();
		
		$news = $article->where($map)->order('aid desc')->limit($Page->firstRow.','.$Page->listRows)->select();		
		
		$this->assign ( 'news', $news);
		$this->assign ( 'result', $result);			
		$this->assign ( 'list', $list );
$this->assign ( 'page', $show );		
		$this->display ( TMPL_PATH . C ( "DEFAULT_GROUP" ) . '/news.html' );
	}
	//产品详细
	public function detail() {
		$article = M('article');
		$id = I("get.id",'','intval'); 
		$map['aid'] = $id;
		$result = $article->where($map)->find();
		
		//获取左侧栏目
		$category = M('category');
		$where['typeid'] = $result['typeid'];
		$tmpcate = $category->where($where)->find();	
		
		if($tmpcate['fid']==0){
			$data['typeid'] = $tmpcate['typeid'];
			$data['typename'] = $tmpcate['typename'];
			$chiwhere['fid'] = $tmpcate['typeid'];
			$data['child'][] = $tmpcate;
		}else{
			$wherea['typeid'] = $tmpcate['fid'];
			$cate = $category->where($wherea)->find();
			$data['typeid'] = $cate['typeid'];
			$data['typename'] = $cate['typename'];
			$chiwhere['fid'] = $cate['typeid'];
			$data['child'] = $category->where($chiwhere)->select();
			
		}
		$this->assign ( 'result', $result );
		$this->assign ( 'cate', $data );	
		$this->assign ( 'nowposition',$tmpcate['typename']);	
		$this->display ( TMPL_PATH . C ( "DEFAULT_GROUP" ) . '/news-arcticle.html' );
	}
}
