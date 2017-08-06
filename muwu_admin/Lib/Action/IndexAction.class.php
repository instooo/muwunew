<?php
/**
     @function  后台首页
	 @file IndexAction.class.php $
	 @Author shf_l@163.com 
	 @copyright (C) 2012-2013 31wan
	 @license	http://demo.31wan.cn/license
	  @lastmodify	2013-09-07
	  @version 4.0

*/
class IndexAction extends CommonAction {
	public function index($id='') {
		$conn1 = M ( "webconfig" );
		$data = $conn1->find ();
		$list = parent::check_user_access ();//top 栏目
		$slist ['admin_path'] = C ( "ADMIN_PATH" );
		
		if($_SESSION ['fruit'] !='')
		{
			if($id =='')
			{
				$ary = explode(',',$_SESSION ['fruit']);
				$inde_id = $ary[0];
				
			}
			else
			{
				$inde_id = $id;
			}
			
		
			$map ['id'] = array ('in',$_SESSION ['fruit']);
			$conn = M ( "webconfig" );
			$rop_list = $conn->where ( $map )->select ();  
			$str =$_GET['key']; 
			$top_key = "http://";
			if($str){
				$rop_list1=array();
					foreach ($rop_list as $key => $val) {
						$top = substr($val['domain'],0,7);  
						if($top==$top_key){
							$frist = substr($val['domain'],7,1);								
						}else {
							$frist = substr($val['domain'],0,1);
						}						
						$pos = strpos($str, $frist);
						if($pos !== false){
							$rop_list1[] = $val; 
						}
					}
				if(count($list) >0)
				{	
					$bm='<select onchange="cfsjla();" id="sel_name">';
					$bm .= '<option value=\'0\'>请选择</option>';
					for($i=0;$i<count($rop_list1);$i++)
					{
						$zt='';
						if($rop_list1[$i]['id'] == $inde_id)
						{
							$zt='selected';
							$_SESSION ['qzpf_zd'] = $inde_id;//全站匹配
						}
						$bm.= '<option value="'.$rop_list1[$i]['id'].'" '.$zt.'>'.$rop_list1[$i]['sitename'].'</option>';
					}
					$bm.='</select>';
					//return $bm;
					$this->assign ( 'xial', $bm );    
				}
			}else{
				if(count($list) >0)
				{
					$bm='<select onchange="cfsjla();" id="sel_name">';
					for($i=0;$i<count($rop_list);$i++)
					{
					$zt='';
						if($rop_list[$i]['id'] == $inde_id)
						{
						$zt='selected';
							$_SESSION ['qzpf_zd'] = $inde_id;//全站匹配
						}
						$bm.= '<option value="'.$rop_list[$i]['id'].'" '.$zt.'>'.$rop_list[$i]['sitename'].'</option>';
					}
					$bm.='</select>';
					$this->assign ( 'xial', $bm );
				}
			}
			
		}
		//exit($conn->getLastSql());
		//exit('SL:'.count($list));
		$this->assign ( 'list', $list );  
		$this->assign ( 'data', $data );
		$this->assign ( 'slist', $slist );
		$this->display ( TMPL_PATH . C ( "ADMIN_THEME" ) . '/index.html' );
	}
}