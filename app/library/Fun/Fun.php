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
	
	/**
	 * 检测返回数据状态
	 * @param unknown $returnArray
	 * @return boolean
	 */
	public static function returnOK($returnArray){
		return $returnArray['status'] == 200;
	}
	

	public static function parse_lang($key,$params,$default){
		static $lang 	= array();
		$message 		= $default;
		$DS 			= DIRECTORY_SEPARATOR;
		$langfile 		= app_path()."{$DS}lang{$DS}zh_cn{$DS}Redpacket-Lang.php";
		empty($lang) && file_exists($langfile) && $lang = include($langfile);
		$lang && $message = isset($lang[$key])?$lang[$key]:$message;
		if($params){
			$regs = '/\{\d*\}/';
			if (is_array($params)){
				$regs = range(0, count($params));
				$regs = array_map(function(&$v){
					return '/\{'.$v.'}/';
				}, $regs);
			}
			$message = preg_replace($regs, $params, $message);
		}
		return  $message;
	}
	
	
	/**
	 * (int , string , array )
	 * 	=> status msg data
	 * (int , string , string )
	 * 	=> status msg msg_placeholder
	 * (int , string , string , string |[] )
	 *  => status msg msg_placeholder msg_placeholder_param
	 * (int , string , array  , string )
	 *  => status msg data	msg_placeholder 
	 * (int , string , array  , string , string |[] )
	 *  => status msg data msg_placeholder msg_placeholder_param
	 * @param number $status
	 * @param string $message
	 * @param unknown $data
	 * @return multitype:number multitype: Ambigous <string, unknown>
	 */
	public static function returnArray($status = 200, $message = 'ok', $data = array() ){
		
		static $lang = array();
		$args_num	= func_num_args();
		$args		= func_get_args();
		
		$params = array(
			'status' 			=> $status,	//状态码
			'message' 			=> $message,//
			'data' 				=> array(),	//
			'msg_lang' 			=> '',		//通过它来替换message
			'lang_params'		=> '',		//meaasge lang 的填充参数
		);
		
		switch ($args_num){
			case 3:
				if(is_string($args[2])){
					$params['msg_lang'] = $args[2];
				}else if(is_array($args[2])){
					$params['data'] 	= $args[2];
				}
				break;
			case 4:
				if(is_string($args[2])){
					$params['msg_lang']		= $args[2];
					$params['lang_params'] 	= $args[3];
				}else if(is_array($args[2])){
					$params['data'] 		= $args[2];
					$params['msg_lang']		= $args[3];
				}
				break;
			case 5:
				$params['data'] 		= $args[2];
				$params['msg_lang']		= $args[3];
				$params['lang_params'] 	= $args[4];
				break;
		}
		
		$params['msg_lang'] && 
			$params['message'] = Fun ::parse_lang(
					$params['msg_lang'], 
					$params['lang_params'], 
					$params['message']
			);
		
		return $array = array (
				'status' 	=> $params['status'],
				'message' 	=> $params['message'],
				'data' 		=> $params['data']
		);
	}
	
	public static function exitMsg( $data ){
		if(!isset($data['status'])) return false;
		extract($data);
		static::msg($status, $message, $data);
	}
	
	/**
	 * 返回json
	 *
	 * @param string $data        	
	 */
	public static function msg($status, $message = null, $data = array()) {
		if ($status != 200) {
			\Ser\LogService::record ( "ReqLogs", array (
					'Method' => \Request::method (),
					'Input' => \Request::all (),
					'Url' => \Request::url (),
					'func_num_args' => func_get_args () 
			), 'logs' );
		}
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
	public static function trimall($str)
	{
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