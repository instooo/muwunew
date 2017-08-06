<?php

/**
 * @name 首页控制器 
 * @access private 
 * @package app 
 * @author 7477 team
 */

class CaseAction extends CommonAction{
	
	public function __construct() {
	
		parent::__construct();
		//直接设置手游的站点id 
		$this->config_list = 110;
	}
	
	//列表页
	public function index() {
	
		//产品列表	
		$article = M('article');
		$typeid = I('get.id','','htmlspecialchars');
		if(!$typeid){
			$type = M('category');			
			$where['fid'] = 4;
			$typeid = $type->field('typeid')->where($where)->select();
			$strtypeid='4,';
			foreach($typeid as $key=>$val){
			$strtypeid.=$val['typeid'].",";
			}
			$strtypeid = rtrim($strtypeid, ','); 		
			$map['typeid'] = array('in',$strtypeid);
		}else{
			$map['typeid'] = $typeid;
		}
		
		import('ORG.Util.Page');// 导入分页类
		$count      = $article->where($map)->count();
		$Page       = new Page($count,9);
		$show       = $Page->show();		
		
		$list = $article->where($map)->order('aid desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		//获取子栏目	
		$type = M('category');		
		$where['fid'] = 2;
		$column = $type->where($where)->select();
		
		$this->assign ( 'cate', $column );	
		$this->assign ( 'page', $show );
		$this->assign ( 'product', $list );			
		$this->display ( TMPL_PATH . C ( "DEFAULT_GROUP" ) . '/case.html' );
	}
	//产品详细
	public function detail() {
		//查找文章
		$article = M('article');
		$id = I("get.id",'','intval'); 		
		$map['aid'] = $id;
		$result = $article->where($map)->find();
		//获取左侧栏目
		$category = M('category');
		$where['typeid'] = $result['typeid'];
		$tmpcate = $category->where($where)->find();		
		$data = array();
		if($tmpcate['fid']==0){
			$data['typeid'] = $tmpcate['typeid'];
			$data['typename'] = $tmpcate['typename'];
			$chiwhere['fid'] = $tmpcate['typeid'];
			$data['child'] = $category->where($chiwhere)->select();
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
		$this->display ( TMPL_PATH . C ( "DEFAULT_GROUP" ) . '/case-arcticle.html' );
	}
}
