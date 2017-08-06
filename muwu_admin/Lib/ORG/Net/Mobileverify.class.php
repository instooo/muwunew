<?php
header("Content-Type: text/html; charset=utf-8");
/**
 * 验证码操作类
 * @author chenzhouyu
 *
 */
 
/*测试短信*/
/*		
import ( "@.ORG.Net.Mobileverify" );
$mobileverify = new Mobileverify ();	
$a = $mobileverify->fs_yz('15162235394');
exit($a);
*/
/*测试短信*/
class mobileverify {	
	function curl_get($url='', $options=array()){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		if (!empty($options)){
			curl_setopt_array($ch, $options);
		}
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	function curl_post($url='', $postdata='', $options=array()){
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
		return $data;
	}
	
	/**
	* POST 请求
	* @param  $url	请求地址
	* @param  $params	请求参数数组
	* @param  $header	HTTP头信息
	*/
	function post($url , $params_array = array(), $header = array()){
		$ch = curl_init();	// 初始化CURL句柄
		curl_setopt($ch, CURLOPT_URL, $url);	//设置请求的URL
		curl_setopt($ch, CURLOPT_POST, 1);	//启用POST提交
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);		// 设为TRUE把curl_exec()结果转化为字串，而不是直接输出
		$postdata = '';		//请求参数数组转化为以'&'分隔的字符串
		if(!empty($params_array)) {
			foreach($params_array as $k=>$v) {
				$postdata .= $k.'='.rawurlencode($v).'&';		//注意，此处统一对传入参数做urlencode处理，请勿重复encdoe参数
			}
			$postdata = substr($postdata, 0, -1);
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);	//设置POST提交的请求参数
		curl_setopt($ch,CURLOPT_HTTPHEADER,$header);	//设置HTTP头信息
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);	//设置超时时间15秒
		$response = curl_exec($ch);	//执行预定义的CURL
		curl_close($ch);	//关闭CURL
		return $response;
	}
	
	/*获取信任密码*/
	public function huyzm($app_id,$app_secret,$access_token,$dataurl,$timestamp)
	{	$url = "http://api.189.cn/v2/dm/randcode/token?";
        $param['app_id']= "app_id=".$app_id;
        $param['access_token'] = "access_token=".$access_token;
        $param['timestamp'] = "timestamp=".$timestamp;
        ksort($param);
        $plaintext = implode("&",$param);
        $param['sign'] = "sign=".rawurlencode(base64_encode(hash_hmac("sha1", $plaintext, $app_secret, $raw_output=True)));
        ksort($param);
        $url .= implode("&",$param);
        $result = $this->curl_get($url);
        $resultArray = json_decode($result,true);
		$token = $resultArray['token'];
		return $token;
	}
	//发送验证码并且json
    public function fs_yz($phone,$app_id,$app_secret,$access_token,$dataurl,$timestamp)
    {		
        $url = "http://api.189.cn/v2/dm/randcode/send";			
        $token = $this->huyzm($app_id,$app_secret,$access_token,$dataurl,$timestamp);	
        $exp_time = '30';//验证时间   
        $param['app_id']= "app_id=".$app_id;
        $param['access_token'] = "access_token=".$access_token;
        $param['timestamp'] = "timestamp=".$timestamp;
        $param['token'] = "token=".$token;
        $param['phone'] = "phone=".$phone;
        $param['url'] = "url=".$dataurl;
		//exit($phone.' '.$app_id.' '.$app_secret.' '.$access_token.' '.$dataurl.' '.$timestamp);
        if(isset($exp_time))
            $param['exp_time'] = "exp_time=".$exp_time;
        ksort($param);
        $plaintext = implode("&",$param);
        $param['sign'] = "sign=".rawurlencode(base64_encode(hash_hmac("sha1", $plaintext, $app_secret, $raw_output=True)));
        ksort($param);
        $str = implode("&",$param);
        $result = $this->curl_post($url,$str);//返回的json
		sleep(1);
		return $result;        
    }
	
	public function fs_shdx($app_id,$access_token,$phone,$dx_id,$template_param,$timestamp,$app_secret)
	{
		$params_array = array(
			'app_id'			=> $app_id,//应用ID
			'access_token'		=> $access_token,//令牌
			'acceptor_tel'		=> $phone,//手机号
			'template_id'       => $dx_id,//模版短信
			'template_param'    => $template_param,//参数
			'timestamp'			=> $timestamp//时间
		);
		
		ksort($params_array);	//按照key进行字典升序

		$params_str = "";	//请求参数间以'&'字符拼接成的字符串
		foreach ($params_array as $k=>$v){
			$params_str .= '&'.$k.'='.$v;
		}
		$params_str = substr($params_str, 1);
		
		/*sign参数签名获取*/
		$hmac = hash_hmac("sha1", $params_str, $app_secret, true);
		$sign = base64_encode($hmac);	//非必选参数，参数签名
		$params_array['sign'] = $sign;
		
		$url = 'http://api.189.cn/v2/emp/templateSms/sendSms';	//模板短信请求地址		
	
		/*发送POST请求*/
		$result = $this->post($url, $params_array);	
		return $result;
	}



}