<?php

/**
 * 通用类
 *
 */
namespace Lib\Fun;

use Illuminate\Mail\Message;

class Fun {
	
	
	/**
	 * 将金额平分，保留2位小数，返回数组，最后一位补差额
	 * @param unknown $price
	 * @param unknown $period
	 * @return array
	 */
	public static function  divide_equally($price,$period){
		$each = bcmul ( $price / $period, 1, 2 );
		$result = array_fill(0, $period, floatval($each));
		if($period > 1 ){
			$result[$period - 1 ] =  $price - $each * ($period - 1);
		}
		return  $result;
	}
	
	
	
	/**
	 * 生成等比缩略图
	 * @param unknown $imgPath
	 * 	图片全路径
	 * @param number $maxSize
	 * 	最大尺寸
	 * @param string $savePath
	 * 	缩略图保存全路径
	 * @param string $cover
	 * 	是否覆盖
	 * @return boolean|Ambigous <string, unknown>
	 */
	public static function thumb($imgPath,$maxSize = 80,$savePath = '',$cover = false){
		if(file_exists($imgPath)){
			try {
				$pathinfo = pathinfo($imgPath);
				$path = $pathinfo['dirname'].DIRECTORY_SEPARATOR.
				$pathinfo['filename']."_{$maxSize}.".$pathinfo['extension'];
				$path = $savePath ? $savePath : $path;
				if(!$cover && file_exists($path)){
					return $path;
				}
				self::resize($imgPath, $path, $maxSize);
			}catch (\Exception $e){
				\Log::error($e);
				return false;
			}
			return $path;
		}
		return false;
	}
	
	/**
	 * 等比缩放图片
	 * @param unknown $path
	 * @param unknown $dst_path
	 * @param unknown $max
	 * @throws \Exception
	 */
	public static function resize($path ,$dst_path,$max)
	{
		$info = @getimagesize($path);
	
		if ($info === false) {
			throw new \Exception(
					"Unable to read image from file ({$path})."
			);
		}
	
		// define core
		switch ($info[2]) {
			case IMAGETYPE_PNG:
				$core = imagecreatefrompng($path);
				break;
			case IMAGETYPE_JPEG:
				$core = imagecreatefromjpeg($path);
				break;
			default:
				throw new \Exception(
				"Unable to read image type. GD driver is only able to decode JPG, PNG or GIF files."
						);
		}
		$resource = & $core;
		
		$width = imagesx($resource);
		$height = imagesy($resource);
		if($width > $height){//等比尺寸
			$dst_h = intval($height * $max / $width ) ;
			$dst_w  = $max;
		}else{
			$dst_w = intval($width * $max / $height ) ;
			$dst_h  = $max;
		}
		// new canvas
		$canvas = imagecreatetruecolor($width, $height);
		
		// fill with transparent color
		imagealphablending($canvas, false);
		$transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
		imagefilledrectangle($canvas, 0, 0, $width, $height, $transparent);
		imagecolortransparent($canvas, $transparent);
		imagealphablending($canvas, true);
		
		// copy original
		imagecopy($canvas, $resource, 0, 0, 0, 0, $width, $height);
		imagedestroy($resource);
		
		$resource = $canvas;
	
		// create new image
		$modified = imagecreatetruecolor($dst_w, $dst_h);
		
		// preserve transparency
		$transIndex = imagecolortransparent($resource);
		
		if ($transIndex != -1) {
			$rgba = imagecolorsforindex($modified, $transIndex);
			$transColor = imagecolorallocatealpha($modified, $rgba['red'], $rgba['green'], $rgba['blue'], 127);
			imagefill($modified, 0, 0, $transColor);
			imagecolortransparent($modified, $transColor);
		} else {
			imagealphablending($modified, false);
			imagesavealpha($modified, true);
		}
		
		// copy content from resource
		$result = imagecopyresampled($modified,$resource,0,0,0,0,$dst_w,$dst_h,$width,$height);
		$resource = $modified;
		
		switch (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
			case 'png':
			case 'image/png':
			case 'image/x-png':
				ob_start();
				imagealphablending($resource, false);
				imagesavealpha($resource, true);
				imagepng($resource, null, -1);
				$mime = image_type_to_mime_type(IMAGETYPE_PNG);
				$buffer = ob_get_contents();
				ob_end_clean();
				break;
		
			case 'jpg':
			case 'jpeg':
			case 'image/jpg':
			case 'image/jpeg':
			case 'image/pjpeg':
				ob_start();
				imagejpeg($resource, null,100);
				$mime = image_type_to_mime_type(IMAGETYPE_JPEG);
				$buffer = ob_get_contents();
				ob_end_clean();
				break;
			default:
				throw new \Exception("Encoding format is not supported.");
		}
		$saved = @file_put_contents($dst_path, $buffer);
		if ($saved === false) {
			throw new \Exception(
					"Can't write image data to path ({$dst_path})"
			);
		}
	}

	/**
	 * 获取文件类型
	 *  @param unknown $filename
	 * @return string
	 */
	public static function file_type($filename) {
		$file = fopen ( $filename, "rb" );
		$bin = fread ( $file, 2 ); // 只读2字节
		fclose ( $file );
		$strInfo = @unpack ( "C2chars", $bin );
		$typeCode = intval ( $strInfo ['chars1'] . $strInfo ['chars2'] );
		$fileType = '';
		switch ($typeCode) {
			case 6063 :
				$fileType = 'txt';
				break;
			case 7790 :
				$fileType = 'exe';
				break;
			case 7784 :
				$fileType = 'midi';
				break;
			case 8297 :
				$fileType = 'rar';
				break;
			case 8075 :
				$fileType = 'zip';
				break;
			case 255216 :
				$fileType = 'jpg';
				break;
			case 7173 :
				$fileType = 'gif';
				break;
			case 6677 :
				$fileType = 'bmp';
				break;
			case 13780 :
				$fileType = 'png';
				break;
			default :
				$fileType = 'unknown: ' . $typeCode;
		}
		// Fix
		if ($strInfo ['chars1'] == '-1' and $strInfo ['chars2'] == '-40')
			return 'jpg';
		if ($strInfo ['chars1'] == '-119' and $strInfo ['chars2'] == '80')
			return 'png';
		
		return $fileType;
	}
	
	
	
	
	/**
	 * 生成随机字符串
	 *
	 * @param unknown_type $len
	 *        	长度
	 * @param unknown_type $format
	 *        	内容类别，ALL,CHAR,NUMBER
	 */
	public static function randStr($len = 6, $format = 'NUMBER') {
		switch ($format) {
			case 'ALL' :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@~';
				break;
			case 'CHAR' :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@~';
				break;
			case 'NUMBER' :
				$chars = '0123456789';
				break;
			default :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
				break;
		}
		mt_srand ( ( double ) microtime () * 1000000 * getmypid () );
		$password = "";
		while ( strlen ( $password ) < $len )
			$password .= substr ( $chars, (mt_rand () % strlen ( $chars )), 1 );
		return $password;
	}
	public static function returnLang($key) {
	}
	
	/**
	 * 检测返回数据状态
	 * 
	 * @param unknown $returnArray        	
	 * @return boolean
	 */
	public static function returnOK($returnArray) {
		return $returnArray ['status'] == 200;
	}
	public static function returnArray($status = 200, $message = 'ok', $data = array()) {
		if (is_string ( $data )) {
			// app/service/Redpacket/
			$DS = DIRECTORY_SEPARATOR;
			$langfile = app_path () . "{$DS}lang{$DS}zh_cn{$DS}Redpacket-Lang.php";
			
			if (file_exists ( $langfile )) {
				$lang = include ($langfile);
				
				$message = isset ( $lang [$data] ) ? $lang [$data] : $message;
			}
			$data = array ();
		}
		
		return $array = array (
				'status' => $status,
				'message' => $message,
				'data' => $data 
		);
	}
	public static function exitMsg($data) {
		if (! isset ( $data ['status'] ))
			return false;
		extract ( $data );
		static::msg ( $status, $message, $data );
	}
	
	/**
	 * 返回json
	 *
	 * @param string $data
	 */
	public static function msg($status, $message = null, $data = array()) {
		\Ser\LogService::record ( "ReqLogs", array (
				'Method' => \Request::method (),
				'Input' => \Request::all (),
				'Url' => \Request::url (),
				'func_num_args' => func_get_args ()
		), 'logs' );
		self::fireCallback(get_defined_vars());
		$array = array (
				'status' => $status,
				'message' => $message,
				'data' => $data
		); 
		header ( "Content-type: application/json" );
		exit ( json_encode ( $array ) );
	}
	
	/**
	 * 设置msg回调
	 * @param string $callback
	 * 	回调方法
	 * @param array $params
	 * 	回调参数
	 * @return multitype:multitype:string unknown
	 */
	public static function callback($callback = NULL,array $params = array()) {
		static $_callback  = [];
		if(is_callable($callback)){
			$_callback[] =['func'=>$callback,'param'=>$params] ;
		}
		return $_callback;
	}
	
	/**
	 * 触发msg回调
	 * @param unknown $context
	 */
	public static function fireCallback($context) {
		$_callback = self::callback();
		foreach ($_callback as $callback){
			$result = call_user_func_array($callback['func'], array($context,$callback['param']));
			if($result === false) break;
		}
	}
	
	/**
	 * 检查参数是否为空
	 */
	public static function isEmpty($k, $kname) {
		if (empty ( $k ) && $k != "0") {
			self::msg ( 202, '嘿嘿，' . $kname . '没填' );
		} else {
			return true;
		}
	}
	
	/**
	 * 设置加密规则
	 *
	 * @param String $password        	
	 * @param String $salt        	
	 */
	public static function md5_salt($password, $salt) {
		$password = MD5 ( $password );
		$password = MD5 ( $password . $salt );
		return $password;
	}
	
	/**
	 * 用户上传照片类型
	 */
	public static function identity_type() {
		$data = array (
				0 => '身份证正面',
				1 => '身份证反面',
				2 => '身份证正反照',
				3 => '工牌照片',
				4 => '流水情况',
				5 => '行驶证照片',
				6 => '房产证照片' 
		);
		return isset ( $id ) ? $data [$id] : $data;
	}
	
	/**
	 * 借贷额度等级匹配表
	 */
	public static function grade() {
		$data = array (
				0 => '3000',
				1 => '5000',
				2 => '10000' 
		);
		return isset ( $id ) ? $data [$id] : $data;
	}
	
	/**
	 * 计算两个时间相差天数
	 *
	 * @param unknown $day1        	
	 * @param unknown $day2        	
	 * @return number
	 */
	public static function diffBetweenTwoDays($day1, $day2) {
		$second1 = strtotime ( $day1 );
		$second2 = strtotime ( $day2 );
		
		if ($second1 < $second2) {
			$tmp = $second2;
			$second2 = $second1;
			$second1 = $tmp;
		}
		return ($second1 - $second2) / 86400;
	}
	
	/**
	 * PHP 删前后空格
	 *
	 * @param unknown $str        	
	 */
	public static function trimall($str) {
		$qian = array (
				" ",
				"　",
				"\t",
				"\n",
				"\r" 
		);
		$hou = array (
				"",
				"",
				"",
				"",
				"" 
		);
		return str_replace ( $qian, $hou, $str );
	}
}