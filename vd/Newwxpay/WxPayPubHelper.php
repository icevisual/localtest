<?php
/**
 * 微信支付帮助库
 * ====================================================
 * 接口分三种类型：
 * 【请求型接口】--Wxpay_client_
 * 		统一支付接口类--UnifiedOrder
 * 		订单查询接口--OrderQuery
 * 		退款申请接口--Refund
 * 		退款查询接口--RefundQuery
 * 		对账单接口--DownloadBill
 * 		短链接转换接口--ShortUrl
 * 【响应型接口】--Wxpay_server_
 * 		通用通知接口--Notify
 * 		Native支付——请求商家获取商品信息接口--NativeCall
 * 【其他】
 * 		静态链接二维码--NativeLink
 * 		JSAPI支付--JsApi
 * =====================================================
 * 【CommonUtil】常用工具：
 * 		trimString()，设置参数时需要用到的字符处理函数
 * 		createNoncestr()，产生随机字符串，不长于32位
 * 		formatBizQueryParaMap(),格式化参数，签名过程需要用到
 * 		getSign(),生成签名
 * 		arrayToXml(),array转xml
 * 		xmlToArray(),xml转 array
 * 		postXmlCurl(),以post方式提交xml到对应的接口url
 * 		postXmlSSLCurl(),使用证书，以post方式提交xml到对应的接口url
*/
	include_once("SDKRuntimeException.php");
	include_once("WxPay.pub.config.php");

/**
 * 所有接口的基类
 */
class Common_util_pub
{
	
	function __construct() {
	}

	function trimString($value)
	{
		$ret = null;
		if (null != $value) 
		{
			$ret = $value;
			if (strlen($ret) == 0) 
			{
				$ret = null;
			}
		}
		return $ret;
	}
	
	/**
	 * 	作用：产生随机字符串，不长于32位
	 */
	public function createNoncestr( $length = 32 ) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		}  
		return $str;
	}
	
	/**
	 * 	作用：格式化参数，签名过程需要使用
	 */
	function formatBizQueryParaMap($paraMap, $urlencode)
	{
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v)
		{
		    if($urlencode)
		    {
			   $v = urlencode($v);
			}
			//$buff .= strtolower($k) . "=" . $v . "&";
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar;
		if (strlen($buff) > 0) 
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
	}
	
	/**
	 * 	作用：生成签名
	 */
	public function getSign($Obj)
	{
		foreach ($Obj as $k => $v)
		{
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//echo '【string1】'.$String.'</br>';
		//签名步骤二：在string后加入KEY
		$String = $String."&key=".$this->user['key'];
		//echo "【string2】".$String."</br>";
		//签名步骤三：MD5加密
		$String = md5($String);
		//echo "【string3】 ".$String."</br>";
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		//echo "【result】 ".$result_."</br>";
		return $result_;
	}
	
	/**
	 * 	作用：生成签名,已经传入商户信息
	 */
	public function getSignwithuser($Obj,$tconf)
	{
		foreach ($Obj as $k => $v)
		{
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//echo '【string1】'.$String.'</br>';
		//签名步骤二：在string后加入KEY
		$String = $String."&key=".$tconf['key'];
		//echo "【string2】".$String."</br>";
		//签名步骤三：MD5加密
		$String = md5($String);
		//echo "【string3】 ".$String."</br>";
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		//echo "【result】 ".$result_."</br>";
		return $result_;
	}
	
	/**
	 * 	作用：array转xml
	 */
	function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
        	 if (is_numeric($val))
        	 {
        	 	$xml.="<".$key.">".$val."</".$key.">"; 

        	 }
        	 else
        	 	$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
        }
        $xml.="</xml>";
        return $xml; 
    }
	
	/**
	 * 	作用：将xml转为array
	 */
	public function xmlToArray($xml)
	{		
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $array_data;
	}

	/**
	 * 	作用：以post方式提交xml到对应的接口url
	 */
	public function postXmlCurl($xml,$url,$second=30)
	{		
        //初始化curl        
       	$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
        $data = curl_exec($ch);
		//curl_close($ch);
		//返回结果
		if($data)
		{
			curl_close($ch);
			return $data;
		}
		else 
		{ 
			$error = curl_errno($ch);
			echo "curl出错，错误码:$error"."<br>"; 
			echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
			curl_close($ch);
			return false;
		}
	}

	/**
	 * 	作用：使用证书，以post方式提交xml到对应的接口url
	 */
	function postXmlSSLCurl($xml,$url,$second=30)
	{
		$ch = curl_init();
		//超时时间
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		//这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		//设置header
		curl_setopt($ch,CURLOPT_HEADER,FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		//设置证书
		//使用证书：cert 与 key 分别属于两个.pem文件
		//默认格式为PEM，可以注释
		//curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT, $this->user['sslcert_path']);
		//默认格式为PEM，可以注释
		//curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY, $this->user['sslkey_path']);
		//post提交方式
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		}
		else { 
			$error = curl_errno($ch);
			echo "curl出错，错误码:$error"."<br>"; 
			echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
			curl_close($ch);
			return false;
		}
	}
	
	/**
	 * 	作用：打印数组
	 */
	function printErr($wording='',$err='')
	{
		print_r('<pre>');
		echo $wording."</br>";
		var_dump($err);
		print_r('</pre>');
	}
}

/**
 * 请求型接口的基类
 */
class Wxpay_client_pub extends Common_util_pub 
{
	var $parameters;//请求参数，类型为关联数组
	public $response;//微信返回的响应
	public $result;//返回参数，类型为关联数组
	var $url;//接口链接
	var $curl_timeout;//curl超时时间
	
	/**
	 * 	作用：设置请求参数
	 */
	function setParameter($parameter, $parameterValue)
	{
		$this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
	}
	
	/**
	 * 	作用：设置标配的请求参数，生成签名，生成接口参数xml
	 */
	function createXml()
	{
	   	$this->parameters["appid"] = $this->user['appid'];//公众账号ID
	   	$this->parameters["mch_id"] = $this->user['mchid'];//商户号
	    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
	    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
	    return  $this->arrayToXml($this->parameters);
	}
	
	/**
	 * 	作用：post请求xml
	 */
	function postXml()
	{
	    $xml = $this->createXml();
		$this->response = $this->postXmlCurl($xml,$this->url,$this->curl_timeout);
		return $this->response;
	}
	
	/**
	 * 	作用：使用证书post请求xml
	 */
	function postXmlSSL()
	{	
	    $xml = $this->createXml();
		$this->response = $this->postXmlSSLCurl($xml,$this->url,$this->curl_timeout);
		return $this->response;
	}

	/**
	 * 	作用：获取结果，默认不使用证书
	 */
	function getResult() 
	{		
		$this->postXml();
		$this->result = $this->xmlToArray($this->response);
		return $this->result;
	}
}


/**
 * 统一支付接口类
 */
class UnifiedOrder_pub extends Wxpay_client_pub
{	
	protected $user;
	
	function __construct($tconf) 
	{
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		//设置curl超时时间
		$this->curl_timeout = WxPayConf_pub::CURL_TIMEOUT;
		//支付账户信息
		$this->user = $tconf;
	}
	
	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{
		try
		{
			//检测必填参数
			if($this->parameters["out_trade_no"] == null) 
			{
				throw new SDKRuntimeException("缺少统一支付接口必填参数out_trade_no！"."<br>");
			}elseif($this->parameters["body"] == null){
				throw new SDKRuntimeException("缺少统一支付接口必填参数body！"."<br>");
			}elseif ($this->parameters["total_fee"] == null ) {
				throw new SDKRuntimeException("缺少统一支付接口必填参数total_fee！"."<br>");
			}elseif ($this->parameters["notify_url"] == null) {
				throw new SDKRuntimeException("缺少统一支付接口必填参数notify_url！"."<br>");
			}elseif ($this->parameters["trade_type"] == null) {
				throw new SDKRuntimeException("缺少统一支付接口必填参数trade_type！"."<br>");
			}elseif ($this->parameters["trade_type"] == "JSAPI" &&
				$this->parameters["openid"] == NULL){
				throw new SDKRuntimeException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！"."<br>");
			}
		   	$this->parameters["appid"] = $this->user['appid'];//公众账号ID
		   	$this->parameters["mch_id"] = $this->user['mchid'];//商户号
		   	$this->parameters["spbill_create_ip"] = $_SERVER['REMOTE_ADDR'];//终端ip	    
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
		    return  $this->arrayToXml($this->parameters);
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}
	
	/**
	 * 获取prepay_id
	 */
	function getPrepayId()
	{
		$this->postXml();
		$this->result = $this->xmlToArray($this->response);
		$prepay_id = $this->result["prepay_id"];
		return $prepay_id;
	}
	
}

/**
 * 订单查询接口
 */
class OrderQuery_pub extends Wxpay_client_pub
{
	protected $user;
	
	function __construct($wid) 
	{
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/pay/orderquery";
		//设置curl超时时间
		$this->curl_timeout = WxPayConf_pub::CURL_TIMEOUT;		
		//获取用户信息
		$payment = M('payment')->where(array('pid'=>$wid,'pc_type'=>'weipaynew'))->find();
		$this->user = json_decode($payment['pc_config'],true);
	}

	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{
		try
		{
			//检测必填参数
			if($this->parameters["out_trade_no"] == null && 
				$this->parameters["transaction_id"] == null) 
			{
				throw new SDKRuntimeException("订单查询接口中，out_trade_no、transaction_id至少填一个！"."<br>");
			}
		   	$this->parameters["appid"] = $this->user['appid'];//公众账号ID
		   	$this->parameters["mch_id"] = $this->user['mchid'];//商户号
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
		    return  $this->arrayToXml($this->parameters);
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}

}

/**
 * 退款申请接口
 */
class Refund_pub extends Wxpay_client_pub
{
	protected $user;
	
	function __construct($wid) {
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
		//设置curl超时时间
		$this->curl_timeout = WxPayConf_pub::CURL_TIMEOUT;		
		//获取用户信息
		$payment = M('payment')->where(array('pid'=>$wid,'pc_type'=>'weipaynew'))->find();
		$this->user = json_decode($payment['pc_config'],true);
	}
	
	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{
		try
		{
			$this->parameters["op_user_id"] = $this->user['mchid'];//商户号
			//检测必填参数
			if($this->parameters["out_trade_no"] == null && $this->parameters["transaction_id"] == null) {
				throw new SDKRuntimeException("退款申请接口中，out_trade_no、transaction_id至少填一个！"."<br>");
			}elseif($this->parameters["out_refund_no"] == null){
				throw new SDKRuntimeException("退款申请接口中，缺少必填参数out_refund_no！"."<br>");
			}elseif($this->parameters["total_fee"] == null){
				throw new SDKRuntimeException("退款申请接口中，缺少必填参数total_fee！"."<br>");
			}elseif($this->parameters["refund_fee"] == null){
				throw new SDKRuntimeException("退款申请接口中，缺少必填参数refund_fee！"."<br>");
			}elseif($this->parameters["op_user_id"] == null){
				throw new SDKRuntimeException("退款申请接口中，缺少必填参数op_user_id！"."<br>");
			}
		   	$this->parameters["appid"] = $this->user['appid'];//公众账号ID
		   	$this->parameters["mch_id"] = $this->user['mchid'];//商户号
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
		    
		    dump($this->parameters);
		    return  $this->arrayToXml($this->parameters);
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}
	/**
	 * 	作用：获取结果，使用证书通信
	 */
	function getResult() 
	{		
		$this->postXmlSSL();
		$this->result = $this->xmlToArray($this->response);
		return $this->result;
	}
	
}


/**
 * 退款查询接口
 */
class RefundQuery_pub extends Wxpay_client_pub
{
	var $parameters;
	protected $user;
	
	function __construct($wid) {
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/pay/refundquery";
		//设置curl超时时间
		$this->curl_timeout = WxPayConf_pub::CURL_TIMEOUT;	
		//获取用户信息
		$payment = M('payment')->where(array('pid'=>$wid,'pc_type'=>'weipaynew'))->find();
		$this->user = json_decode($payment['pc_config'],true);
	}
	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{		
		try 
		{
			if($this->parameters["out_trade_no"] == null &&
				$this->parameters["out_refund_no"] == null &&
				$this->parameters["transaction_id"] == null &&
				$this->parameters["refund_id"] == null) 
			{
				throw new SDKRuntimeException("退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！"."<br>");
			}
		   	$this->parameters["appid"] = $this->user['appid'];//公众账号ID
		   	$this->parameters["mch_id"] = $this->user['mchid'];//商户号
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}
		
	/**
	 * 	作用：获取结果，使用证书通信
	 */
	function getResult() 
	{		
		$this->postXmlSSL();
		$this->result = $this->xmlToArray($this->response);
		return $this->result;
	}

}

/**
 * 对账单接口
 */
class DownloadBill_pub extends Wxpay_client_pub
{

	protected $user;
	
	function __construct($wid) 
	{
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/pay/downloadbill";
		//设置curl超时时间
		$this->curl_timeout = WxPayConf_pub::CURL_TIMEOUT;		
		//获取用户信息
		$payment = M('payment')->where(array('pid'=>$wid,'pc_type'=>'weipaynew'))->find();
		$this->user = json_decode($payment['pc_config'],true);
	}

	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{		
		try 
		{
			if($this->parameters["bill_date"] == null ) 
			{
				throw new SDKRuntimeException("对账单接口中，缺少必填参数bill_date！"."<br>");
			}
		   	$this->parameters["appid"] = $this->user['appid'];//公众账号ID
		   	$this->parameters["mch_id"] = $this->user['mchid'];//商户号
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
		    return  $this->arrayToXml($this->parameters);
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}
	
	/**
	 * 	作用：获取结果，默认不使用证书
	 */
	function getResult() 
	{		
		$this->postXml();
		$this->result = $this->xmlToArray($this->result_xml);
		return $this->result;
	}
	
	

}

/**
 * 短链接转换接口
 */
class ShortUrl_pub extends Wxpay_client_pub
{
	protected $user;
	
	function __construct($wid) 
	{
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/tools/shorturl";
		//设置curl超时时间
		$this->curl_timeout = WxPayConf_pub::CURL_TIMEOUT;		
		//获取用户信息
		$payment = M('payment')->where(array('pid'=>$wid,'pc_type'=>'weipaynew'))->find();
		$this->user = json_decode($payment['pc_config'],true);
	}
	
	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{		
		try 
		{
			if($this->parameters["long_url"] == null ) 
			{
				throw new SDKRuntimeException("短链接转换接口中，缺少必填参数long_url！"."<br>");
			}
		   	$this->parameters["appid"] = $this->user['appid'];//公众账号ID
		   	$this->parameters["mch_id"] = $this->user['mchid'];//商户号
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
		    return  $this->arrayToXml($this->parameters);
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}
	
	/**
	 * 获取prepay_id
	 */
	function getShortUrl()
	{
		$this->postXml();
		$prepay_id = $this->result["short_url"];
		return $prepay_id;
	}
	
}

/**
 * 响应型接口基类
 */
class Wxpay_server_pub extends Common_util_pub 
{
	public $data;//接收到的数据，类型为关联数组
	var $returnParameters;//返回参数，类型为关联数组
	
	/**
	 * 将微信的请求xml转换成关联数组，以方便数据处理
	 */
	function saveData($xml)
	{
		$this->data = $this->xmlToArray($xml);
	}
	
	function checkSign($tconf)
	{
		$tmpData = $this->data;
		unset($tmpData['sign']);
		$sign = $this->getSignwithuser($tmpData,$tconf);//本地签名
		if ($this->data['sign'] == $sign) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * 获取微信的请求数据
	 */
	function getData()
	{		
		return $this->data;
	}
	
	/**
	 * 设置返回微信的xml数据
	 */
	function setReturnParameter($parameter, $parameterValue)
	{
		$this->returnParameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
	}
	
	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{
		return $this->arrayToXml($this->returnParameters);
	}
	
	/**
	 * 将xml数据返回微信
	 */
	function returnXml()
	{
		$returnXml = $this->createXml();
		return $returnXml;
	}
}


/**
 * 通用通知接口
 */
class Notify_pub extends Wxpay_server_pub 
{

}




/**
 * 请求商家获取商品信息接口
 */
class NativeCall_pub extends Wxpay_server_pub
{
	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{
		if($this->returnParameters["return_code"] == "SUCCESS"){
		   	$this->returnParameters["appid"] = $this->user['appid'];//公众账号ID
		   	$this->returnParameters["mch_id"] = $this->user['mchid'];//商户号
		    $this->returnParameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->returnParameters["sign"] = $this->getSign($this->returnParameters);//签名
		}
		return $this->arrayToXml($this->returnParameters);
	}
	
	/**
	 * 获取product_id
	 */
	function getProductId()
	{
		$product_id = $this->data["product_id"];
		return $product_id;
	}
	
}

/**
 * 静态链接二维码
 */
class NativeLink_pub  extends Common_util_pub
{
	var $parameters;//静态链接参数
	var $url;//静态链接

	function __construct() 
	{
	}
	
	/**
	 * 设置参数
	 */
	function setParameter($parameter, $parameterValue) 
	{
		$this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
	}
	
	/**
	 * 生成Native支付链接二维码
	 */
	function createLink()
	{
		try 
		{		
			if($this->parameters["product_id"] == null) 
			{
				throw new SDKRuntimeException("缺少Native支付二维码链接必填参数product_id！"."<br>");
			}			
		   	$this->parameters["appid"] = $this->user['appid'];//公众账号ID
		   	$this->parameters["mch_id"] = $this->user['mchid'];//商户号
		   	$time_stamp = time();
		   	$this->parameters["time_stamp"] = "$time_stamp";//时间戳
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名    		
			$bizString = $this->formatBizQueryParaMap($this->parameters, false);
		    $this->url = "weixin://wxpay/bizpayurl?".$bizString;
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}
	
	/**
	 * 返回链接
	 */
	function getUrl() 
	{		
		$this->createLink();
		return $this->url;
	}
}




class User_Notify_pub extends Common_util_pub{
	var $code;//code码，用以获取openid
	var $openid;//用户的openid
	var $curl_timeout;//curl超时时间
	var $access_token;
	var $wid;
	var $ticket;//jsapi_ticket
	protected $user;

	function __construct($tconf) 
	{
		if(is_array($tconf)){
			$this->user = $tconf;
		}else if(intval($tconf)){
			$this->wid  = $tconf;
			$this->user = $this->getThisOpenidConf($tconf);
			if ($this->user === false){
				exit('获取 appid 和 appsecret 失败！请检查相关配置。');
			}
		}
		//设置curl超时时间
		$this->curl_timeout = WxPayConf_pub::CURL_TIMEOUT;
	}
	function getThisOpenidConf($wid){
		$tconf 				= M('payment')->where(array('pid'=>$wid,'pc_type="weipaynew"'))->find();
		if($tconf){
			$tconf 			= (array)json_decode($tconf['pc_config']);
			if($tconf['appid'] && $tconf['appsecret'] ){
				return $tconf;
			}
		}
		return false;
	}
	/**
	 * 	作用：生成可以获得code的url
	 */
	function createOauthUrlForCode($redirectUrl)
	{
		$urlObj["appid"] = $this->user['appid'];
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_base";//
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$bizString = $this->formatBizQueryParaMap($urlObj, false);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}
	/**
	 * 	作用：生成可以获得code的url,scope=snsapi_userinfo
	 */
	function createOauthUrlForCodeUserInfo($redirectUrl)
	{
		$urlObj["appid"] = $this->user['appid'];
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_userinfo";//"snsapi_base";//
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$bizString = $this->formatBizQueryParaMap($urlObj, false);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}

	/**
	 * 	作用：生成可以获得openid的url
	 */
	function createOauthUrlForOpenid()
	{
		$urlObj["appid"] = $this->user['appid'];
		$urlObj["secret"] = $this->user['appsecret'];
		$urlObj["code"] = $this->code;
		$urlObj["grant_type"] = "authorization_code";
		$bizString = $this->formatBizQueryParaMap($urlObj, false);
		return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
	}
	
	function createOauthUrlForUserInfo()
	{
		$urlObj["access_token"] = $this->access_token;
		$urlObj["openid"] = $this->openid;
		$bizString = $this->formatBizQueryParaMap($urlObj, false);
		return "https://api.weixin.qq.com/sns/userinfo?".$bizString;
	}
	
	function vpost1($url, $data='', $header=1){ // 模拟提交数据函数
		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		//curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		//curl_setopt($curl, CURLOPT_REFERER, "mp.weixin.qq.com");
		//curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie);
		if($data) {
			curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
		}
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$tmpInfo = curl_exec($curl); // 执行操作
		if (curl_errno($curl)) {
			echo 'Errno'.curl_error($curl);//捕抓异常
		}
		curl_close($curl); // 关闭CURL会话
		return $tmpInfo; // 返回数据
	}
	
	/**
	 * Success => 0,ok
	 * @param unknown_type $errcode
	 * @param unknown_type $errmsg
	 * @param unknown_type $errdata
	 */
	function returnArray($errcode,$errmsg,$errdata = ''){
		$errdata = is_array($errdata) ? var_export($errdata,true):$errdata;
		return array('errcode'=>$errcode,
					 'errmsg'=>$errmsg,
					 'errdata'=>$errdata);
	}
	function jsapiTicketSignature($url) {
		$param['noncestr'] 		= $this->createNoncestr();
		$param['jsapi_ticket'] 	= $this->getJsapiTicket();
		if(! is_string($param['jsapi_ticket'])) return $param['jsapi_ticket'];
		$param['timestamp'] 	= time();
		$param['url'] 			= str_replace(':80', "", $url)  ;
		$String 				= $this->formatBizQueryParaMap($param, false);
		
		$param['signature']		= sha1($String);
		//unset($param['url']);
		$param['appId']			= $this->user['appid'];
		return $param;
	}
	/**
	 * 	作用：产生随机字符串，不长于32位
	 */
	public function createNoncestr( $length = 16 ) 
	{
		$chars = "QWERTYUIOPASDFGHJKLZXCVBNMabcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		}  
		return $str;
	}
	
	function getJsapiTicket() {
		$wid	 	= func_num_args() ? intval(func_get_arg(0)) : intval($this->wid) ;
		$appid 		= func_num_args() >= 2 ? func_get_arg(1) : $this->user['appid'];
		$appsecret 	= func_num_args() >= 3 ? func_get_arg(2) : $this->user['appsecret'];
		if($wid <= 0 || ! $appid || !$appsecret) return $this->returnArray('10005','Param Lose,check wid,appid and appsecret');
		$where['pid'] 		= $wid;
		$where['pc_type'] 	= 'weipaynew';
		$result 			= M('payment')->where($where)->find();
		if($result['ticket'] &&( time() - $result['ticket_time'] ) < $result['ticket_expires_in'] - 240 ){
			$this->ticket =  $result['ticket'];
			return  $result['ticket'];
		}
		$access_token = $this->getAccessToken();
		if(! is_string($access_token)) return $access_token;
		$ticket_url='https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
		$result = $this->vpost1($ticket_url);
		$json = json_decode($result,true);
		if(!$json['errcode']){
			$data['ticket'] 				= $json['ticket'];
			$data['ticket_time'] 			= time();
			$data['ticket_expires_in'] 		= $json['expires_in'];
			M('payment')->where($where)->save($data);
			$this->ticket = $json['ticket'];
			return  $json['ticket'];
		}
		return $json;
	}
	/**
	 * 获取access_token,需要appid和appsecret
	 * 	存数据库，过时刷新
	 */
	function getAccessToken(){
		$wid	 	= func_num_args() ? intval(func_get_arg(0)) : intval($this->wid) ;
		$appid 		= func_num_args() >= 2 ? func_get_arg(1) : $this->user['appid'];
		$appsecret 	= func_num_args() >= 3 ? func_get_arg(2) : $this->user['appsecret'];
		if($wid <= 0 || ! $appid || !$appsecret) return $this->returnArray('10005','Param Lose,check wid,appid and appsecret');
		$where['pid'] 		= $wid;
		$where['pc_type'] 	= 'weipaynew';
		$result 			= M('payment')->where($where)->find();
		if($result['access_token'] &&( time() - $result['access_token_time'] ) < $result['expires_in'] - 240 ){
			$this->access_token =  $result['access_token'];
			return  $result['access_token'];
		}
		$access_token_url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
		$result = $this->vpost1($access_token_url);
		$json = json_decode($result,true);
		if(!$json['errcode']){
			$data['access_token'] 		= $json['access_token'];
			$data['access_token_time'] 	= time();
			$data['expires_in'] 		= $json['expires_in'];
			M('payment')->where($where)->save($data);
			$this->access_token = $json['access_token'];
			return  $json['access_token'];
		}
		return $json;
	}
	
	function add_template($short_id = 'OPENTM203246698'){
		if(!$this->wid) return  $this->returnArray('10001','Wid missing');
		$wid						= $this->wid;
		$where['wid'] 				= $wid;
		$where['short_id'] 			= $short_id;
		$template =  M('message_template')->where($where)->find();
		if($template){
			return $template['template_id'];
		}
		$post['template_id_short'] 	= $short_id;
		$post						= json_encode($post);
		$access_token 				= $this->access_token;
		$url 						= 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token='.$access_token;
		$ret 						= $this->vpost1($url,$post);
		$json = json_decode($ret,true);
		if($json['errcode'] == 0 && $json['errmsg'] == 'ok'){
			$where['template_id'] 	= $json['template_id'];
			$where['ctime']			= time();
			M('message_template')->add($where);
			return $json['template_id'];	
		}
		return $json;
	}
	
	
	
	/**
	 * 发送新订单模板消息
	 * @param unknown_type $wid
	 * @param unknown_type $openid
	 */
	public function sendNotify($openid,$data,$url,$template_short_id){
		$list = is_array($openid) ? $openid : array(0 => array('openid'=>$openid));
		foreach ($list as $v) {
			$this->openid = $v['openid'];
			$json = $this->sendTemplateMessage($data,$url,$template_short_id);
			if($json['errcode'] != 0){
				if($json['errcode'] == 40037){
					//invalid template_id
					$where['wid'] 				= $this->wid;;
					$where['short_id'] 			= $template_short_id;
					$template =  M('message_template')->where($where)->delete();
					$json = $this->sendTemplateMessage($data,$url,$template_short_id);
					if($json['errcode'] != 0){
						$json['errmsgzn'] = '模板ID错误！';
					}else {
						return true;
					}
				}else if($json['errcode'] == 45026){
					//template num exceeds limit
					//jsAlert('模板数量超出限制！');
					$json['errmsgzn'] = '模板数量超出限制！';
				}else{
					//jsAlert('请先开通模板消息！');
					$json['errmsgzn'] = '请先开通模板消息！';
				}
				return  $json;
			}
		}
		return true;
	}
	
	/**
	 * 需要
	 * 	wid			
	 * 	access_token 请求或者读取数据库
	 * 	openid	session('user_openid')
	 * 	$template_id 	消息模板
	 * 	$url			详情链接
	 *  $data			模板数据
	 */
	function sendTemplateMessage($data,$url,$template='OPENTM203246698'){
		$token 					= $this->getAccessToken();
		$openid 				= $this->openid ;
		$template_id 			= $this->add_template($template);
		if(is_array($token) && $token['errcode'] !=0 ) {
			return $token;
		}
		if( !$openid ) {
			return $this->returnArray('10004','User openid missing',$json);;
		}
		if( is_array ($template_id)  && $template_id['errcode'] !=0 ) {
			return $template_id;
		}
		$post['touser'] 		= $openid;
		$post['template_id'] 	= $template_id;
		$post['url'] 			= $url;
		$post['topcolor'] 		= '#FF0000';
		$post['data'] 			= $data;
		$post					= json_encode($post);
		$url 					= "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$token;
		$ret					= $this->vpost1($url,$post,0);
		$json = json_decode($ret,true);
		return $json;
	}
	
	
	/**
	 * 	作用：通过curl向微信提交code，以获取用户头像
	 */
	function getUserInfo(){
		//判断access_token时效性
		$url = $this->createOauthUrlForUserInfo();
        //初始化curl
       	$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOP_TIMEOUT, $this->curl_timeout);
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//运行curl，结果以jason形式返回
        $res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		return $data;
	}
	
	/**
	 * 	作用：通过curl向微信提交code，以获取openid
	 */
	function getOpenid(){
		//判断access_token时效性
	
		$url = $this->createOauthUrlForOpenid();
        //初始化curl
       	$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOP_TIMEOUT, $this->curl_timeout);
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//运行curl，结果以jason形式返回
        $res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		$this->openid = $data['openid'];
		$this->access_token = $data['access_token'];
		return $this->openid;
	}

	/**
	 * 	作用：设置code
	 */
	function setCode($code_){
		$this->code = $code_;
	}
	
}


/**
* JSAPI支付——H5网页端调起支付接口
*/
class JsApi_pub extends Common_util_pub
{
	var $code;//code码，用以获取openid
	var $openid;//用户的openid
	var $parameters;//jsapi参数，格式为json
	var $prepay_id;//使用统一支付接口得到的预支付id
	var $curl_timeout;//curl超时时间
	var $access_token;
	var $wid;
	protected $user;

	function __construct($tconf) 
	{
		//设置curl超时时间
		$this->curl_timeout = WxPayConf_pub::CURL_TIMEOUT;
		//获取用户信息
		$this->user = $tconf;
	}
	
	/**
	 * 	作用：生成可以获得code的url
	 */
	function createOauthUrlForCode($redirectUrl)
	{
		$urlObj["appid"] = $this->user['appid'];
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_base";//
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$bizString = $this->formatBizQueryParaMap($urlObj, false);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}
	/**
	 * 	作用：生成可以获得code的url,scope=snsapi_userinfo
	 */
	function createOauthUrlForCodeUserInfo($redirectUrl)
	{
		$urlObj["appid"] = $this->user['appid'];
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_userinfo";//"snsapi_base";//
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$bizString = $this->formatBizQueryParaMap($urlObj, false);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}

	/**
	 * 	作用：生成可以获得openid的url
	 */
	function createOauthUrlForOpenid()
	{
		$urlObj["appid"] = $this->user['appid'];
		$urlObj["secret"] = $this->user['appsecret'];
		$urlObj["code"] = $this->code;
		$urlObj["grant_type"] = "authorization_code";
		$bizString = $this->formatBizQueryParaMap($urlObj, false);
		return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
	}
	
	function createOauthUrlForUserInfo()
	{
		$urlObj["access_token"] = $this->access_token;
		$urlObj["openid"] = $this->openid;
		$bizString = $this->formatBizQueryParaMap($urlObj, false);
		return "https://api.weixin.qq.com/sns/userinfo?".$bizString;
	}
	
	function vpost1($url, $data='', $header=1){ // 模拟提交数据函数
		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		//curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		//curl_setopt($curl, CURLOPT_REFERER, "mp.weixin.qq.com");
		//curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie);
		if($data) {
			curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
		}
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$tmpInfo = curl_exec($curl); // 执行操作
		if (curl_errno($curl)) {
			echo 'Errno'.curl_error($curl);//捕抓异常
		}
		curl_close($curl); // 关闭CURL会话
		return $tmpInfo; // 返回数据
	}
	
	/**
	 * Success => 0,ok
	 * @param unknown_type $errcode
	 * @param unknown_type $errmsg
	 * @param unknown_type $errdata
	 */
	function returnArray($errcode,$errmsg,$errdata = ''){
		$errdata = is_array($errdata) ? var_export($errdata,true):$errdata;
		return array('errcode'=>$errcode,
					 'errmsg'=>$errmsg,
					 'errdata'=>$errdata);
	}
	
	/**
	 * 获取access_token,需要appid和appsecret
	 * 	存数据库，过时刷新
	 */
	function getAccessToken(){
		$wid = func_num_args() ? intval(func_get_arg(0)) : intval($this->wid) ;
		if($wid <= 0 ) return $this->returnArray('10005','Failed to get wid');
		$where['pid'] 		= $wid;
		$where['pc_type'] 	= 'weipaynew';
		$result 			= M('payment')->where($where)->find();
		if($result['access_token'] &&( time() - $result['access_token_time'] ) < $result['expires_in'] - 240 ){
			return  $result['access_token'];
		}
		$access_token_url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->user['appid']."&secret=".$this->user['appsecret'];
		$result = $this->vpost1($access_token_url);
		$json = json_decode($result,true);
		if(!$json['errcode']){
			$data['access_token'] 		= $json['access_token'];
			$data['access_token_time'] 	= time();
			$data['expires_in'] 		= $json['expires_in'];
			M('payment')->where($where)->save($data);
			$this->access_token = $json['access_token'];
			return  $json['access_token'];
		}
		return $this->returnArray('10003','Failed to get access_token',$json);
	}
	
	function add_template($short_id = 'TM00001'){
		if(!$this->wid) return  $this->returnArray('10001','Wid missing');
		$wid						= $this->wid;
		$where['wid'] 				= $wid;
		$where['short_id'] 			= $short_id;
		$template =  M('message_template')->where($where)->find();
		if($template){
			return $template['template_id'];
		}
		$post['template_id_short'] 	= $short_id;
		$post						= json_encode($post);
		$access_token 				= $this->access_token;
		$url 						= 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token='.$access_token;
		$ret 						= $this->vpost1($url,$post);
		$json = json_decode($ret,true);
		if($json['errcode'] == 0 && $json['errmsg'] == 'ok'){
			$where['template_id'] 	= $json['template_id'];
			$where['ctime']			= time();
			M('message_template')->add($where);
			return $json['template_id'];	
		}
		return $this->returnArray('10002','Add template failed',$json);
	}
	
	/**
	 * 需要
	 * 	wid			
	 * 	access_token 请求或者读取数据库
	 * 	openid	session('user_openid')
	 * 	$template_id 	消息模板
	 * 	$url			详情链接
	 *  $data			模板数据
	 */
	function sendTemplateMessage($data,$url,$template='TM00001'){
		$token 					= $this->getAccessToken();
		$openid 				= $this->openid ;
		$template_id 			= $this->add_template($template);
		if(is_array($token) && $token['errcode'] !=0 ) {
			return $token;
		}
		if( !$openid ) {
			return $this->returnArray('10004','User openid missing',$json);;
		}
		if( is_array ($template_id)  && $template_id['errcode'] !=0 ) {
			return $template_id;
		}
		$post['touser'] 		= $openid;
		$post['template_id'] 	= $template_id;
		$post['url'] 			= $url;
		$post['topcolor'] 		= '#FF0000';
		$post['data'] 			= $data;
		$post					= json_encode($post);
		$url 					= "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$token;
		return $this->vpost1($url,$post,0);
	}
	
	
	/**
	 * 	作用：通过curl向微信提交code，以获取用户头像
	 */
	function getUserInfo()
	{
		//判断access_token时效性
		$url = $this->createOauthUrlForUserInfo();
        //初始化curl
       	$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOP_TIMEOUT, $this->curl_timeout);
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//运行curl，结果以jason形式返回
        $res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		edump($data);
		return $data['headimgurl'];
	}
	
	/**
	 * 	作用：通过curl向微信提交code，以获取openid
	 */
	function getOpenid()
	{
		//判断access_token时效性
	
		$url = $this->createOauthUrlForOpenid();
        //初始化curl
       	$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOP_TIMEOUT, $this->curl_timeout);
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//运行curl，结果以jason形式返回
        $res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		$this->openid = $data['openid'];
		$this->access_token = $data['access_token'];
		return $this->openid;
	}

	/**
	 * 	作用：设置prepay_id
	 */
	function setPrepayId($prepayId)
	{
		$this->prepay_id = $prepayId;
	}

	/**
	 * 	作用：设置code
	 */
	function setCode($code_)
	{
		$this->code = $code_;
	}

	/**
	 * 	作用：设置jsapi的参数
	 */
	public function getParameters()
	{
		$jsApiObj["appId"] = $this->user['appid'];
		$timeStamp = time();
	    $jsApiObj["timeStamp"] = "$timeStamp";
	    $jsApiObj["nonceStr"] = $this->createNoncestr();
		$jsApiObj["package"] = "prepay_id=$this->prepay_id";
	    $jsApiObj["signType"] = "MD5";
	    $jsApiObj["paySign"] = $this->getSign($jsApiObj);
	    $this->parameters = json_encode($jsApiObj);
		
		return $this->parameters;
	}
}
?>
