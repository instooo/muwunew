<?php
/**
	 @function  系统RBAC权限验证 所有访问权限为 private 的控制器都要继承它
	 @file CommonAction.class.php $
	 @Author shf_l@163.com 
	 @copyright (C) 2012-2013 31wan
	 @license	http://demo.31wan.cn/license
	 @lastmodify	2013-09-07
	 @version 4.0
	 @todo RBAC
*/
include dirname(dirname(__FILE__))."/ORG/Phpqrcode/qrlib.php";  
class CommonAction extends Action {

	/**
	 *
	 * @name 更新缓存
	 */
	public function clear_cache() {
		$st = deldir ($_SERVER ['DOCUMENT_ROOT'] . '/Runtime/Cache/');
		$admin = C ( 'ADMIN_PATH' );
		$this->assign ( 'jumpUrl', U ( 'Main/welcome' ) );
		$this->success ( '更新缓存成功!' );
	}		
	
	/**
	 * @@name 管理员日志记录
	 * 
	 * @param array $arr        	
	 */
	public function admin_log($arrs) {
		$model = M ( 'admin_exec_log' );
		$arr ['user'] = $_SESSION ['user'];
		$arr ['ip'] = get_client_ip ();
		$arr ['time'] = time ();
		$arr ['remark'] = $arrs;
		$model->data ( $arr )->add ();
	}
	/**
	 * 用户访问检测
	 */
	public function check_user_access() {
		$map ['username'] = $_SESSION ['user'];
		$model = M ( 'manager' );
		$cond ['role_access.role_id'] = $model->where ( $map )->getField ( 'roleid' );
		$cond ['role_access.access_fid'] = 0;
		$access = D ( 'RbacView' );
		$mylist = $access->where ( $cond )->order ( 'category_list.listorder asc' )->select ();
		
		return $mylist;
	}
	public function _initialize() {
		$map ['ip'] = get_client_ip ();
		$model = M ( 'web_not_allow_ip' );
		$st = $model->where ( $map )->find ();
		if ($st) {
			header ( 'HTTP/1.1 404 Not Found' );
			header ( "status: 404 Not Found" );
			exit ();
		}
		import ( '@.ORG.Util.Cookie' );
		// 用户权限检查
		if (C ( 'USER_AUTH_ON' ) && ! in_array ( MODULE_NAME, explode ( ',', C ( 'NOT_AUTH_MODULE' ) ) )) {
			import ( '@.ORG.Util.RBAC' );			
			if (! $_SESSION ['user']) {
				redirect ( U ( 'Public/login' ) );
				unset ( $_SESSION ['user'] );
			}
		}
	
		
	}
	/**
	 *
	 * @name 检测用户操作权限
	 */
	public function action_access($module_name, $action) {
		$model = M ( 'category_list' );
		$arr ['module_alias'] = $module_name;
		$info_1 = $model->where ( $arr )->find ();
		$map ['module_alias'] = $action;
		$map ['fid'] = $info_1 ['id'];
		$info = $model->where ( $map )->find ();
		$cond ['access_id'] = $info ['id'];
		$cond ['role_id'] = $_SESSION ['role_id'];
		$cond ['access_fid'] = $info_1 ['id'];
		$role_access = M ( 'role_access' );
		$st = $role_access->where ( $cond )->select ();
		
		if (! $st) {
			
			$this->error ( '未授权,非法访问.系统已经记录你的操作!' );
		} // 验证通过
	}
	
	
	
	/**
	*
	* @name 图片上传
	* @list_img 上传的图片
	* @sercer_string 配合sercer_type参数(sercer_type=wu 传递字符串 | sercer_type=you 通过数据库查询FTP地址)
	*/
		public function ftp_imagearticle_com($list_img,$sercer_string){		
		import ( "@.ORG.Net.Ftps" );

		//查询
		$conn = M( 'webconfig' );
		$list = $conn->where('id ='.$sercer_string)->find();
		$server_ip = $list['server_ip'];
		$server_port = $list['server_port'];
		$server_username = $list['server_username'];
		$server_password = $list['server_password'];
		$server_open = $list['server_open'];
					
		$Ftps = new Ftps ();	
		if(file_exists('..'.$list_img))
		{
/*			if($server_open ==0)//不启用附件服务器
			{
				return $destination.'/'.$img_name;
			}	*/
			$open = $Ftps->connect($server_ip,$server_username,$server_password,$server_port);		
					
			if($open==false)
			{
				return 'FTP 连接失败';
			}
			
			//exit('/public_html'.$list_img);
			$zt = $Ftps->put('/public_html'.$list_img,'..'.$list_img);
			$Ftps->close();//关闭FTP 连接
		
		}		
		else
		{
			return '没有图片';
		}	
		
		
	
		

	}
	
	/*
	*@发送短信(前台用)
	*@phone 手机号
	*webid 站点ip
	*/
	public function mobile_yz($phone,$webid)
	{		
		import ( "@.ORG.Net.Mobileverify" );
		$mobileverify = new Mobileverify ();
		//查询
		$conn = M( 'webconfig' );
		$list = $conn->where('id ='.$webid)->find();
		$app_id = $list['app_id'];
		$app_secret = $list['app_secret'];
		$access_token = $list['access_token'];
		$dataurl = $list['dx_url'];		
		$timestamp = date('Y-m-d H:i:s');
		
/*		$app_id = '332088680000033037';	
		$app_secret = 'c55b3608b3a505e46797a6ae442023f6';
		$access_token = '821e7f64837b65b6cf247c5242ee054c1380280123051';
		$dataurl='http://demo4.31wan.cn/ceshi2/proxy.php';	*/		
		$json = $mobileverify->fs_yz($phone,$app_id,$app_secret,$access_token,$dataurl,$timestamp);
		$str = json_decode($json);
		$type = D ( 'yzdx' );
		$map = $type->create ();
		$map ['data'] = $str->create_at;
		$map ['phone'] = $phone;
		$whr ['identifier'] = array ('in',$str->identifier);
		$result = $type->where ($whr)->save ($map);
		if ($result !== false) 
		{			
			return '发送成功';
		}
		else
		{
			return '发送失败';
		}
	}
	
	
	/**
	*
	* @name 测试TIP是否开通
	* @server_ip  ip
	* @$server_port ftp端口
	* @server_username ftp帐号
	* @server_password ftp密码	
	* @slt 传任意值表示产生缩略图
	*/
	public function open_ftp($server_ip,$server_port,$server_username,$server_password)
	{
		import ( "@.ORG.Net.Ftps" );
		$Ftps = new Ftps ();
		$open = $Ftps->connect($server_ip,$server_username,$server_password,$server_port);	
		if($open ==false)
		{
			return '0';
		}
		else
		{
			return '1';
		}
			
	}
	
	
	/**
	*
	* @name 图片上传
	* @list_img 上传的图片
	* @tlx 上传的类型(Logo-Logo图片 Article-Article文章 Advert-Advert 广告)
	* @sercer_type 调用改方法的类型判断(wu-需要拼接附件服务器字符串 you-有直接从数据库查询)
	* @sercer_string 配合sercer_type参数(sercer_type=wu 传递字符串 | sercer_type=you 通过数据库查询FTP地址)
	* @slt 传任意值表示产生缩略图
	*/
	public function ftp_image_com($list_img,$tlx,$sercer_type,$sercer_string,$slt='',$k='',$g=''){	
		import ( "@.ORG.Net.Ftps" );
		if($sercer_type =='wu')
		{
			$serverlist = explode('#',$sercer_string);
			$server_ip = $serverlist[0];
			$server_port = $serverlist[1];
			$server_username = $serverlist[2];
			$server_password = $serverlist[3];
			$server_open = $serverlist[4];
						
		}
		else
		{
			//查询
			$conn = M( 'webconfig' );
			$list = $conn->where('id ='.$sercer_string)->find();
			$server_ip = $list['server_ip'];
			$server_port = $list['server_port'];
			$server_username = $list['server_username'];
			$server_password = $list['server_password'];
			$server_open = $list['server_open'];
			
		}
		$ext_arr = array("image/gif", "image/jpg", "image/jpeg", "image/png","image/x-png","image/pjpeg","image/bmp");		
		$Ftps = new Ftps ();	
		if($list_img["size"] > 2097152)
		{
			return  "1" ;
		}
		
		if (in_array($list_img["type"], $ext_arr) === false) 
		{
			
			return "2" ;
		}
		
		$img_date=date('Ymd',time());
		switch($tlx)
		{
			case "Logo":
				$wenj='Logo';
			break;
			case "Article":
				$wenj='Article';
			break;
			case "Advert":
				$wenj='Advert';
			break;
			case "Game":
				$wenj='Game';
			break;
			case "Gamesinimages":
				$wenj='Gamesinimages';
			break;
			case "Terrace":
				$wenj='Terrace';
			break;
			case "Gift":
				$wenj='Gift';
			break;
			case "Task":
				$wenj='Task';
			break;
			case "Luckdraw":
				$wenj='Luckdraw';
			break;
			
		}
		$dir = ROOT_PATH;		
		$destination = $dir.'/Public/'.$wenj.'/'.$img_date;
		$destination1 = 'Public/'.$wenj.'/'.$img_date;
		//根据上传格式来生成文件名对jpg有效
		if($list_img["type"]=="image/jpeg")
			$img_name = $img_date.mt_rand(1,999999).'.jpg';
		else
			$img_name = $img_date.mt_rand(1,999999).'.png';		
		
		
		/*文件夹是否存在*/
		if (!file_exists($dir.'/Public/'.$wenj)) 		
		{
			mkdir($dir.'/Public/'.$wenj);
		} 
			
		if(!file_exists($destination))
		{
			mkdir($destination);				
		}			
		
		if(!move_uploaded_file ($list_img['tmp_name'], $destination.'/'.$img_name))  
		{  
			$this->success ( "上传图片失败!", U ( 'Global/add_web' ) );
		}
		
		if($slt !='')
		{
			$slt_img = $destination.'/s_'.$img_name;
			$slt_img1 = $destination1.'/s_'.$img_name;
			$this->img2thumb($destination.'/'.$img_name,$slt_img,$k,$g);//缩略图
		}
		
		
		if($server_open ==0)//不启用附件服务器
		{
			return $destination1.'/'.$img_name;
		}
		$open = $Ftps->connect($server_ip,$server_username,$server_password,$server_port);		

		if($open==false)
		{	
			return $destination1.'/'.$img_name;
		}
			
		$zt = $Ftps->put('/public_html/'.$destination1.'/'.$img_name,$destination.'/'.$img_name);//远程图片,本地图片
		if($slt !='')
		{
			$Ftps->put('/public_html/'.$slt_img1,$slt_img);
		}
		
		$Ftps->close();//关闭FTP 连接
		if($zt==true)
		{
			return $destination1.'/'.$img_name;
		}
		else
		{		
			return false;
		}
	}
	
	
	function fileext($file)
	{
		return pathinfo($file, PATHINFO_EXTENSION);
	}
	
	/*生成二维码*/
	public function scewm($android_url,$logo=false)	
	{

		$dir = ROOT_PATH.'/www';
		$PNG_TEMP_DIR = $dir.'/Public/ewm'.DIRECTORY_SEPARATOR; 
		if (!file_exists($PNG_TEMP_DIR))
		mkdir($PNG_TEMP_DIR);
		$imagename =md5($android_url).'.png';//图片名称
		$errorCorrectionLevel = 'H';//'L','M','Q','H' 错误处理级别
		$matrixPointSize = 6;//1-10 每个黑点的像素	
		QRcode::png($android_url, $PNG_TEMP_DIR.$imagename, $errorCorrectionLevel, $matrixPointSize, 2);    
 
		if ($logo !== FALSE) { 
		 $_imagename = imagecreatefromstring(file_get_contents($PNG_TEMP_DIR.$imagename)); 
		 $logo = imagecreatefromstring(file_get_contents($logo)); 
		 $QR_width = imagesx($_imagename);//二维码图片宽度 
		 $QR_height = imagesy($_imagename);//二维码图片高度 
		 $logo_width = imagesx($logo);//logo图片宽度 
		 $logo_height = imagesy($logo);//logo图片高度 
		 $logo_qr_width = $QR_width / 5; 
		 $scale = $logo_width/$logo_qr_width; 
		 $logo_qr_height = $logo_height/$scale; 
		 $from_width = ($QR_width - $logo_qr_width) / 2; 
		 //重新组合图片并调整大小 
		 imagecopyresampled($_imagename, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height); 
		 imagepng($_imagename,$PNG_TEMP_DIR.$imagename);
		} 
		
		return 'Public/ewm/'.$imagename;
	}
	
	
	function curl_post($url='', $postdata='', $options=array())
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		if (!empty($options)){
			curl_setopt_array($ch, $options);
		}
		$data = curl_exec($ch);
		curl_close($ch);
		//return $data;
	}
	/**
 * 生成缩略图
 * @author yangzhiguo0903@163.com
 * @param string     源图绝对完整地址{带文件名及后缀名}
 * @param string     目标图绝对完整地址{带文件名及后缀名}
 * @param int        缩略图宽{0:此时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
 * @param int        缩略图高{0:此时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
 * @param int        是否裁切{宽,高必须非0}
 * @param int/float  缩放{0:不缩放, 0<this<1:缩放到相应比例(此时宽高限制和裁切均失效)}
 * @return boolean
 */
function img2thumb($src_img, $dst_img, $width='', $height='', $cut = 0, $proportion = 0)
{
	if($width=='')
	{
		$width =75;
	}
	if($height=='')
	{
		$height =75;
	}
    if(!is_file($src_img))
    {
        return false;
    }
	
    $ot = $this->fileext($dst_img);
    $otfunc = 'image' . ($ot == 'jpg' ? 'jpeg' : $ot);
    $srcinfo = getimagesize($src_img);
    $src_w = $srcinfo[0];
    $src_h = $srcinfo[1];
    $type  = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
    $createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);

    $dst_h = $height;
    $dst_w = $width;
    $x = $y = 0;

    /**
     * 缩略图不超过源图尺寸（前提是宽或高只有一个）
     */
    if(($width> $src_w && $height> $src_h) || ($height> $src_h && $width == 0) || ($width> $src_w && $height == 0))
    {
        $proportion = 1;
    }
	
    if($width> $src_w)
    {
        $dst_w = $width = $src_w;
    }
	
    if($height> $src_h)
    {
        $dst_h = $height = $src_h;
    }

    if(!$width && !$height && !$proportion)
    {
        return false;
    }
    if(!$proportion)
    {
        if($cut == 0)
        {
            if($dst_w && $dst_h)
            {
                if($dst_w/$src_w> $dst_h/$src_h)
                {
                    $dst_w = $src_w * ($dst_h / $src_h);
                    $x = 0 - ($dst_w - $width) / 2;
                }
                else
                {
                    $dst_h = $src_h * ($dst_w / $src_w);
                    $y = 0 - ($dst_h - $height) / 2;
                }
            }
            else if($dst_w xor $dst_h)
            {
                if($dst_w && !$dst_h)  //有宽无高
                {
                    $propor = $dst_w / $src_w;
                    $height = $dst_h  = $src_h * $propor;
                }
                else if(!$dst_w && $dst_h)  //有高无宽
                {
                    $propor = $dst_h / $src_h;
                    $width  = $dst_w = $src_w * $propor;
                }
            }
        }
        else
        {
            if(!$dst_h)  //裁剪时无高
            {
                $height = $dst_h = $dst_w;
            }
            if(!$dst_w)  //裁剪时无宽
            {
                $width = $dst_w = $dst_h;
            }
            $propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
            $dst_w = (int)round($src_w * $propor);
            $dst_h = (int)round($src_h * $propor);
            $x = ($width - $dst_w) / 2;
            $y = ($height - $dst_h) / 2;
        }
    }
    else
    {
        $proportion = min($proportion, 1);
        $height = $dst_h = $src_h * $proportion;
        $width  = $dst_w = $src_w * $proportion;
    }

    $src = $createfun($src_img);
    $dst = imagecreatetruecolor($width ? $width : $dst_w, $height ? $height : $dst_h);
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $white);

    if(function_exists('imagecopyresampled'))
    {
        imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    }
    else
    {
        imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    }
    $otfunc($dst, $dst_img);
    imagedestroy($dst);
    imagedestroy($src);
    return true;
}

	/*获取首字母*/
	function getfirstchar($s0){   
		$fchar = ord($s0{0});
		if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
		$s1 = iconv("UTF-8","gb2312", $s0);
		$s2 = iconv("gb2312","UTF-8", $s1);
		if($s2 == $s0){$s = $s1;}else{$s = $s0;}
		$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
		if($asc >= -20319 and $asc <= -20284) return "A";
		if($asc >= -20283 and $asc <= -19776) return "B";
		if($asc >= -19775 and $asc <= -19219) return "C";
		if($asc >= -19218 and $asc <= -18711) return "D";
		if($asc >= -18710 and $asc <= -18527) return "E";
		if($asc >= -18526 and $asc <= -18240) return "F";
		if($asc >= -18239 and $asc <= -17923) return "G";
		if($asc >= -17922 and $asc <= -17418) return "H";
		if($asc >= -17417 and $asc <= -16475) return "J";
		if($asc >= -16474 and $asc <= -16213) return "K";
		if($asc >= -16212 and $asc <= -15641) return "L";
		if($asc >= -15640 and $asc <= -15166) return "M";
		if($asc >= -15165 and $asc <= -14923) return "N";
		if($asc >= -14922 and $asc <= -14915) return "O";
		if($asc >= -14914 and $asc <= -14631) return "P";
		if($asc >= -14630 and $asc <= -14150) return "Q";
		if($asc >= -14149 and $asc <= -14091) return "R";
		if($asc >= -14090 and $asc <= -13319) return "S";
		if($asc >= -13318 and $asc <= -12839) return "T";
		if($asc >= -12838 and $asc <= -12557) return "W";
		if($asc >= -12556 and $asc <= -11848) return "X";
		if($asc >= -11847 and $asc <= -11056) return "Y";
		if($asc >= -11055 and $asc <= -10247) return "Z";
		return null;
	}
	
	
	/**
	*
	* @name 图片上传
	* @list_img 上传的图片
	* @tlx 上传的类型(Logo-Logo图片 Article-Article文章 Advert-Advert 广告)
	* @sercer_type 调用改方法的类型判断(wu-需要拼接附件服务器字符串 you-有直接从数据库查询)
	* @sercer_string 配合sercer_type参数(sercer_type=wu 传递字符串 | sercer_type=you 通过数据库查询FTP地址)
	* @slt 传任意值表示产生缩略图
	*/
	public function ftp_image_com_multiple($list_img,$tlx,$sercer_type,$sercer_string,$slt='',$k='',$g=''){			
		import ( "@.ORG.Net.Ftps" );
		if($sercer_type =='wu')
		{
			$serverlist = explode('#',$sercer_string);
			$server_ip = $serverlist[0];
			$server_port = $serverlist[1];
			$server_username = $serverlist[2];
			$server_password = $serverlist[3];
			$server_open = $serverlist[4];
						
		}else{
			//查询
			$conn = M( 'webconfig' );
			$list = $conn->where('id ='.$sercer_string)->find();
			$server_ip = $list['server_ip'];
			$server_port = $list['server_port'];
			$server_username = $list['server_username'];
			$server_password = $list['server_password'];
			$server_open = $list['server_open'];			
		}
		$ext_arr = array("image/gif", "image/jpg", "image/jpeg", "image/png","image/x-png","image/pjpeg","image/bmp");		
		$Ftps = new Ftps ();
		foreach($list_img["name"] as $key=>$val){
			if($list_img["size"][$key] > 2097152)
			{
				return  "1" ;
			}
			
			if (in_array($list_img["type"][$key], $ext_arr) === false) 
			{				
				return "2" ;
			}	
			$img_date=date('Ymd',time());
			switch($tlx)
			{
				case "Logo":
					$wenj='Logo';
				break;
				case "Article":
					$wenj='Article';
				break;
				case "Advert":
					$wenj='Advert';
				break;
				case "Game":
					$wenj='Game';
				break;
				case "Gamesinimages":
					$wenj='Gamesinimages';
				break;
				case "Terrace":
					$wenj='Terrace';
				break;
				case "Gift":
					$wenj='Gift';
				break;
				case "Task":
					$wenj='Task';
				break;
				case "Luckdraw":
					$wenj='Luckdraw';
				break;
				
			}
			
			$dir = ROOT_PATH;		
			$destination = $dir.'/Public/'.$wenj.'/'.$img_date;
			$destination1 = 'Public/'.$wenj.'/'.$img_date;
			//根据上传格式来生成文件名对jpg有效
			if($list_img["type"]=="image/jpeg")
				$img_name = $img_date.mt_rand(1,999999).'.jpg';
			else
				$img_name = $img_date.mt_rand(1,999999).'.png';	
			
			
			/*文件夹是否存在*/
			if (!file_exists($dir.'/Public/'.$wenj)) 		
			{
				mkdir($dir.'/Public/'.$wenj);
			} 
				
			if(!file_exists($destination))
			{
				mkdir($destination);				
			}			
			
			if(!move_uploaded_file ($list_img['tmp_name'][$key], $destination.'/'.$img_name))  
			{  
				$this->success ( "上传图片失败!", U ( 'Global/add_web' ) );
			}			
			if($slt !='')
			{
				$slt_img = $destination.'/s_'.$img_name;
				$slt_img1 = $destination1.'/s_'.$img_name;
				$this->img2thumb($destination.'/'.$img_name,$slt_img,$k,$g);//缩略图
			}
			
			
			if($server_open ==0)//不启用附件服务器
			{				
				$file_path[$key]=$destination1.'/'.$img_name;				
			}
			$open = $Ftps->connect($server_ip,$server_username,$server_password,$server_port);		
				
			if($open==false)
			{	
				$file_path[$key]=$destination1.'/'.$img_name;				
			}	
			$zt = $Ftps->put('/public_html/'.$destination1.'/'.$img_name,$destination.'/'.$img_name);
			
			//远程图片,本地图片
			if($slt !='')
			{
				$Ftps->put('/public_html/'.$slt_img1,$slt_img);
			}
			
			$Ftps->close();//关闭FTP 连接
			if($zt==true)
			{
				$file_path[$key]=$destination1.'/'.$img_name;				
			}
			else
			{		
				//return false;
			}	
		}	
		return ($file_path);	
	}
	
}
?>