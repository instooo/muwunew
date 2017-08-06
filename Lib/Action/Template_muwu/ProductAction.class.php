<?php

/**
 * @name 首页控制器 
 * @access private 
 * @package app 
 * @author 7477 team
 */

class ProductAction extends CommonAction{
	
	public function __construct() {
	
		parent::__construct();
		//直接设置手游的站点id 
		$this->config_list = 110;
	}
	
	//列表页
	public function index() {
	
		//产品列表	
		$case = M('case');
		$typeid = I('get.id','','htmlspecialchars');
		$typeida=($typeid=='')?16:$typeid;		
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
		$count      = $case->where($map)->count();
		$Page       = new Page($count,9);
		$show       = $Page->show();		
		
		$case = $case->where($map)->order('aid desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($case as $key=>$val){
			$case[$key]['article_imgar']=json_decode($val['article_img']);
			if(!$val['img_url']){
			$case[$key]['img_url']=$case[$key]['article_imgar'][0];	
			}
		}				
		$this->assign ( 'case', $case);
		$this->assign ( 'result', $result);			
		$this->assign ( 'list', $list );
		$this->assign ( 'page', $show );		
		$this->display ( TMPL_PATH . C ( "DEFAULT_GROUP" ) . '/product.html' );
	}
	//产品详细
	public function detail() {		//查找文章
		
		$id = I("get.id",'','intval'); 		
		$result = $this->casedetail($id);	
		//print_R($result);die;
		$this->assign ( 'result', $result );		
		$this->display ( TMPL_PATH . C ( "DEFAULT_GROUP" ) . '/case.html' );
	}
}
