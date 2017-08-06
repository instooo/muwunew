<?php
    /**
     * @name 自定义URL函数
     * @example {$vo.typeid|url=user,###}
     * @param str $model
     * @param int $id
     * @return string
     */
	function url($model,$id)
	{
		return U($model.'/'.$id);
	}
	/**
	 * @name SQL注入检测
	 */
	//防止sql注入
	function inject_check($str)
	{
		$tmp=eregi('select|insert|update|and|or|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $str);
		if($tmp)
		{
			die('SQL_inject warning!!!');

		}
		else
		{
			return $str;
		}
	}
	/**
	 * @name 中文检测
	 */
	function isChineseName($Argv){
	          $RegExp = "/^[\x{4E00}-\x{9FA5}]+$/u";
	          if(preg_match($RegExp,$Argv))
		      {
			     return true;
			  }
			  else
		      {
				 return false;
			  }
	}
	/**
	 * @name 身份证检测
	 */
	function idcard_checksum18($idcard) {
		if (strlen ( $idcard ) != 18) {
			return false;
		}
		$idcard_base = substr ( $idcard, 0, 17 );
		$firstnum = substr($idcard_base, 0,1);
		if(!is_numeric($firstnum))
			return false;
		if (idcard_verify_number ( $idcard_base ) != strtoupper ( substr ( $idcard, 17, 1 ) )) {
			return false;
		} else {
			return true;
		}
	}
	function idcard_verify_number($idcard_base) {
		if (strlen ( $idcard_base ) != 17) {
			return false;
		}
		//加权因子
		$factor = array (7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2 );
		//校验码对应值
		$verify_number_list = array ('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2' );
		$checksum = 0;
		for($i = 0; $i < strlen ( $idcard_base ); $i ++) {
			$checksum += substr ( $idcard_base, $i, 1 ) * $factor [$i];
		}
		$mod = $checksum % 11;
		$verify_number = $verify_number_list [$mod];
		return $verify_number;
	}
	function toiconv($num,$charset,$str){
		if($charset == 'utf8'){
			$str = iconv_substr($str,0,$num,"utf-8");
		}else{
			$str = iconv_substr($str,0,$num,"gb2312");
		}
		return $str;
	}

	/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string */
function think_encrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time():0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 */
function think_decrypt($data, $key = ''){
    $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;
    if ($mod4) {
       $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data,0,10);
    $data   = substr($data,10);

    if($expire > 0 && $expire < time()) {
        return '';
    }
    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}


// 检测文章是否跳转
function art_redirect($aid){
	$art 		= array();
	$art['aid'] = $aid;
	$model 		= M('article');
	$art_url 	= $model->where($art)->find();
	unset($art);	
	if(strstr($art_url['isshouye'], 'e') !== FALSE){
		redirect($art_url['redirect']);exit;
	}
}
//下载量转换
function _dataFormat($num,$int=FALSE){
	$num = intval($num);
	if($num>10000){
		$num = $num/10000;
		$spice = '万';
	}else if($num>100000000){
		$num = $num/100000000;
		$spice = '亿';
	}
	//取兩位小數
	$num = sprintf("%1.2f",$num);
	return ($int?intval($num):$num).$spice;	
	
}
