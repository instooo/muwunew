<?php
/**
* @name 前台访问权限控制类
* @access private
* @todo 所有访问权限为private 都要继承自它
* @author shf_l@163.com $ (shf_l@163.com)
*/
class CommonAction extends Action {	
	//登陆检测
	public function _initialize(){
	
	}
	//获取文章
	public function article_list($typeid,$limit){
		$article = M('article');
		$map['typeid'] = array('in',$typeid);
		$map['status'] =0;
		//新闻
		$news = $article->where($map)->limit("0,$limit")->select();
		return $news;
	}
	//获取案例列表
	public function case_list($typeid,$limit){
		$case = M('case');
		$map['typeid'] = array('in',$typeid);		
		//新闻
		$case = $case->where($map)->limit("0,$limit")->select();
		foreach($case as $key=>$val){
			$case[$key]['article_imgar']=json_decode($val['article_img']);
			if(!$val['img_url']){
			$case[$key]['img_url']=$case[$key]['article_imgar'][0];	
			}
		}		
		return $case;
	}
	//获取案例详情
	public function casedetail($aid){
		$case = M('case');
		$map['aid'] = $aid;
		$result = $case->where($map)->find();	
		$result['article_imgar']=json_decode($result['article_img']);
		if(!$result['img_url']){
			$result['img_url']=$result['article_imgar'][0];	
		}
		return $result;
	}
	
	
}

?>