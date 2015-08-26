<?php

/**
 * 通用类
 *
 */
namespace Lib\Fun;

use Illuminate\Mail\Message;

class Fun {
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
	
	public static function getIP() {
		if (getenv ( "HTTP_CLIENT_IP" ))
			$ip = getenv ( "HTTP_CLIENT_IP" );
		else if (getenv ( "HTTP_X_FORWARDED_FOR" ))
			$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
		else if (getenv ( "REMOTE_ADDR" ))
			$ip = getenv ( "REMOTE_ADDR" );
		else
			$ip = "Unknow";
		return $ip;
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
		$array = array (
				'status' => $status,
				'message' => $message,
				'data' => $data 
		);
		header ( "Content-type: application/json" );
		exit ( json_encode ( $array ) );
	}
	
	/**
	 * 检查参数是否为空
	 */
	public static function isEmpty($k, $kname) {
		if (empty ( $k ) && $k != "0" && $k != "") {
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