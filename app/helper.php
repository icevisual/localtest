<?php


class AESTool{

	/**
	 * 设置默认的加密key
	 * @var str
	 */
	public static $defaultKey = 'e10adc3949ba59abbe56e057f20f883e';
	
	public $secretKey = '';

	/**
	 * 设置默认加密向量
	 * @var str
	 */
	private $iv = 'e10adc3949ba59a1';
	 
	/**
	 * 设置加密算法
	 * @var str
	 */
	private $cipher;
	 
	/**
	 * 设置加密模式
	 * @var str
	 */
	private $mode;
	 
	public function __construct($cipher = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_CBC){
		$this->cipher = $cipher;
		$this->mode = $mode;
	}
	
	public function setSecretKey($key){
//16, 24 or 32 
		$this->secretKey = $key ;
	}
	
	public function getSecretKey(){
		return $this->secretKey ? $this->secretKey : static::$defaultKey;
	}
	
	 
	/**
	 * 对内容加密，注意此加密方法中先对内容使用padding pkcs7，然后再加密。
	 * @param str $content    需要加密的内容
	 * @return str 加密后的密文
	 */
	public function encrypt($content){
		if(empty($content)){
			return false;
		}
		$srcdata = $content;
		$block_size = mcrypt_get_block_size($this->cipher, $this->mode);
		$padding_char = $block_size - (strlen($content) % $block_size);
		$srcdata .= str_repeat(chr($padding_char),$padding_char);
		return mcrypt_encrypt($this->cipher, $this->getSecretKey(), $srcdata, $this->mode, $this->iv);
	}

	/**
	 * 对内容解密，注意此加密方法中先对内容解密。再对解密的内容使用padding pkcs7去除特殊字符。
	 * @param String $content    需要解密的内容
	 * @return String 解密后的内容
	 */
	public function decrypt($content){
		if(empty($content)){
			return false;
		}

		$content = mcrypt_decrypt($this->cipher, $this->getSecretKey(), $content, $this->mode, $this->iv);
		$block = mcrypt_get_block_size($this->cipher, $this->mode);
		$pad = ord($content[($len = strlen($content)) - 1]);
		return substr($content, 0, strlen($content) - $pad);
	}
}



/*
 * |--------------------------------------------------------------------------
 * | Application Helpers
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register all of the Helpers for an application.
 * |
 */
class RsaTool {
	private $_privKey;
	private $_pubKey;
	private $_keyPath;
	public function __construct($path) {
		if (empty ( $path ) || ! is_dir ( $path )) {
			throw new Exception ( 'Must set the keys save path' );
		}
		$this->_keyPath = $path;
	}
	public function createKey() {
		$r = openssl_pkey_new ([
				'private_key_bits' => 1024,
				'private_key_type' => OPENSSL_KEYTYPE_RSA,
		]);
		openssl_pkey_export ( $r, $privKey );
		file_put_contents ( $this->_keyPath . DIRECTORY_SEPARATOR . 'key.pem', $privKey );
		$this->_privKey = openssl_pkey_get_public ( $privKey );
		$rp = openssl_pkey_get_details ( $r );
		$pubKey = $rp ['key'];
		file_put_contents ( $this->_keyPath . DIRECTORY_SEPARATOR . 'pub.pem', $pubKey );
		$this->_pubKey = openssl_pkey_get_public ( $pubKey );
	}
	
	/**
	 * setup the private key
	 */
	public function setupPrivKey() {
		if (is_resource ( $this->_privKey )) {
			return true;
		}
		$file = $this->_keyPath . DIRECTORY_SEPARATOR . 'key.pem';
		$prk = file_get_contents ( $file );
		$this->_privKey = openssl_pkey_get_private ( $prk );
		return true;
	}
	/**
	 * setup the public key
	 */
	public function setupPubKey() {
		if (is_resource ( $this->_pubKey )) {
			return true;
		}
		$file = $this->_keyPath . DIRECTORY_SEPARATOR . 'pub.pem';
		$puk = file_get_contents ( $file );
		$this->_pubKey = openssl_pkey_get_public ( $puk );
		return true;
	}
	
	/**
	 * encrypt with the private key
	 */
	public function privEncrypt($data) {
		if (! is_string ( $data )) {
			return null;
		}
		$this->setupPrivKey ();
		$r = openssl_private_encrypt ( $data, $encrypted, $this->_privKey );
		if ($r) {
			return base64_encode ( $encrypted );
		} else {
			throw new \Exception ( openssl_error_string () );
		}
	}
	/**
	 * decrypt with the private key
	 */
	public function privDecrypt($encrypted) {
		if (! is_string ( $encrypted )) {
			return null;
		}
		$this->setupPrivKey ();
		$encrypted = base64_decode ( $encrypted );
		$r = openssl_private_decrypt ( $encrypted, $decrypted, $this->_privKey );
		if ($r) {
			return $decrypted;
		} else {
			throw new \Exception ( openssl_error_string () );
		}
	}
	/**
	 * encrypt with public key
	 */
	public function pubEncrypt($data) {
		if (! is_string ( $data )) {
			throw new \Exception ( 'String Is Needed!' );
		}
		
		$this->setupPubKey ();
		$r = openssl_public_encrypt ( $data, $encrypted, $this->_pubKey );
		if ($r) {
			return base64_encode ( $encrypted );
		} else {
			throw new \Exception ( openssl_error_string () );
		}
	}
	/**
	 * * decrypt with the public key
	 */
	public function pubDecrypt($crypted) {
		if (! is_string ( $crypted )) {
			return null;
		}
		$this->setupPubKey ();
		$crypted = base64_decode ( $crypted );
		$r = openssl_public_decrypt ( $crypted, $decrypted, $this->_pubKey );
		if ($r) {
			return $decrypted;
		} else {
			throw new \Exception ( openssl_error_string () );
		}
	}
	public function __destruct() {
		@ fclose ( $this->_privKey );
		@ fclose ( $this->_pubKey );
	}
}

/**
 * $str 原始中文字符串
 * $encoding 原始字符串的编码，默认GBK
 * $prefix 编码后的前缀，默认"&#"
 * $postfix 编码后的后缀，默认";"
 */
function unicode_encode($str, $encoding = 'GBK', $prefix = '&#', $postfix = ';') {
	$str = iconv ( $encoding, 'UCS-2', $str );
	$arrstr = str_split ( $str, 2 );
	$unistr = '';
	for($i = 0, $len = count ( $arrstr ); $i < $len; $i ++) {
		$dec = hexdec ( bin2hex ( $arrstr [$i] ) );
		$unistr .= $prefix . $dec . $postfix;
	}
	return $unistr;
}

/**
 * $str Unicode编码后的字符串
 * $decoding 原始字符串的编码，默认GBK
 * $prefix 编码字符串的前缀，默认"&#"
 * $postfix 编码字符串的后缀，默认";"
 */
function unicode_decode($unistr, $encoding = 'GBK', $prefix = '&#', $postfix = ';') {
	$arruni = explode ( $prefix, $unistr );
	$unistr = '';
	for($i = 1, $len = count ( $arruni ); $i < $len; $i ++) {
		if (strlen ( $postfix ) > 0) {
			$arruni [$i] = substr ( $arruni [$i], 0, strlen ( $arruni [$i] ) - strlen ( $postfix ) );
		}
		$temp = intval ( $arruni [$i] );
		$unistr .= ($temp < 256) ? chr ( 0 ) . chr ( $temp ) : chr ( $temp / 256 ) . chr ( $temp % 256 );
	}
	return iconv ( 'UCS-2', $encoding, $unistr );
}

if (! function_exists ( 'S' )) {
	function S($data) {
		$data = ( array ) $data;
		$debug_data = debug_backtrace ();
		$sdata ['file'] = pathinfo ( $debug_data [0] ['file'] )['filename'];
		if (isset ( $debug_data [1] ) && isset ( $debug_data [1] ['function'] )) {
			$sdata ['file'] .= ' F:' . $debug_data [1] ['function'];
		}
		$sdata ['file'] .= ' L:' . $debug_data [0] ['line'];
		$sdata ['data'] = $data;
		\Ser\LogService::record ( 'P', $sdata, 'logs' );
	}
}




if (! class_exists ( 'Dic' )) {
	class Dic {
		private $dics = [ ];
		private $data_key = '#DATA';
		public function scan($show = false) {
			$show && dump ( $this->dics );
			return $this->dics;
		}
		public function complete($str) {
			$alias = $this->dics;
			$mb_len = mb_strlen ( $str );
			$hit = [ ];
			for($i = 0; $i < $mb_len; $i ++) {
				$wd = mb_substr ( $str, $i, 1 );
				if (isset ( $alias [$wd] )) {
					$alias = &$alias [$wd];
				} else {
					return $hit;
				}
			}
			$rest = $alias;
			if (isset ( $rest [$this->data_key] )) {
				unset ( $rest [$this->data_key] );
			}
			$loop = $alias;
			$current = current ( $loop );
			if (key ( $current ) == $this->data_key) {
				$current = next ( $current );
			}
			if ($current) {
				$stack = $current;
				while ( $stack ) {
					$end = end ( $stack );
					if (! (isset ( $end [$this->data_key] ) && count ( $end ) == 1)) {
					} else {
					}
				}
			}
			
			dump ( $loop );
			
			foreach ( $rest as $key => $value ) {
				if ($value && isset ( $value [$this->data_key] ) && count ( $value ) == 1) {
					// End Point
				}
				$hit [] = $key;
			}
			return $hit;
		}
		public function add($key, $value) {
			$mb_len = mb_strlen ( $key );
			$alias = &$this->dics;
			for($i = 0; $i < $mb_len; $i ++) {
				$wd = mb_substr ( $key, $i, 1 );
				if (isset ( $alias [$wd] )) {
					$alias = &$alias [$wd];
					if ($i == $mb_len - 1) { // End
						$alias [$this->data_key] [] = $value;
					}
				} else {
					if ($i == $mb_len - 1) { // End
						$alias [$wd] [$this->data_key] [] = $value;
					} else { // middle
						$alias [$wd] = [ ];
						$alias = &$alias [$wd];
					}
				}
			}
		}
		
		/**
		 * 查找
		 * 
		 * @param unknown $dics        	
		 * @param unknown $str        	
		 * @return multitype:string
		 */
		public function find($str) {
			$alias = $this->dics;
			$mb_len = mb_strlen ( $str );
			$last = false;
			$ls = [ ];
			$match = '';
			for($i = 0; $i < $mb_len; $i ++) {
				$wd = mb_substr ( $str, $i, 1 );
				if (isset ( $alias [$wd] )) {
					// dump($wd);
					$last = isset ( $alias [$wd] [$this->data_key] ) ? $alias [$wd] [$this->data_key] : $last;
					$match .= $wd;
					$alias = &$alias [$wd];
				} else {
					if ($last === false) {
						break;
					} else {
						$ls [$match] = $last;
						$last = false;
						$match = '';
						$alias = &$dics;
						$i -= 1;
					}
				}
			}
			if ($last !== false) {
				$ls [$match] = $last;
			}
			return $ls;
		}
	}
	
	/**
	 * 添加一条字典
	 * 
	 * @param unknown $dics        	
	 * @param unknown $key        	
	 * @param unknown $value        	
	 */
	function addDic(&$dics, $key, $value) {
		$mb_len = mb_strlen ( $key );
		$alias = &$dics;
		for($i = 0; $i < $mb_len; $i ++) {
			$wd = mb_substr ( $key, $i, 1 );
			if (isset ( $alias [$wd] )) {
				$alias = &$alias [$wd];
				if ($i == $mb_len - 1) { // End
					$alias [] = $value;
				}
			} else {
				if ($i == $mb_len - 1) { // End
					$alias [$wd] [0] = $value;
				} else { // middle
					$alias [$wd] = [ ];
					$alias = &$alias [$wd];
				}
			}
		}
	}
	
	/**
	 * 查找
	 * 
	 * @param unknown $dics        	
	 * @param unknown $str        	
	 * @return multitype:string
	 */
	function find($dics, $str) {
		$alias = $dics;
		$mb_len = mb_strlen ( $str );
		$last = false;
		$ls = [ ];
		$match = '';
		for($i = 0; $i < $mb_len; $i ++) {
			$wd = mb_substr ( $str, $i, 1 );
			if (isset ( $alias [$wd] )) {
				// dump($wd);
				$last = isset ( $alias [$wd] [0] ) ? $alias [$wd] [0] : $last;
				$match .= $wd;
				$alias = &$alias [$wd];
			} else {
				if ($last === false) {
					break;
				} else {
					$ls [$last] = $match;
					$last = false;
					$match = '';
					$alias = &$dics;
					$i -= 1;
				}
			}
		}
		if ($last !== false) {
			$ls [$last] = $match;
		}
		return $ls;
	}
}

if (! function_exists ( 'getReturnInLogFile' )) {
	
	/**
	 * Applies the callback to the elements of the given arrays
	 * 
	 * @link http://www.php.net/manual/en/function.array-map.php
	 * @param
	 *        	callback callable <p>
	 *        	Callback function to run for each element in each array.
	 *        	</p>
	 * @param
	 *        	_ array[optional]
	 * @return array an array containing all the elements of array1
	 *         after applying the callback function to each one.
	 */
	function array_map_recursive($callback, array $array1) {
		return array_map ( function ($v) use($callback) {
			if (is_array ( $v )) {
				return array_map_recursive ( $callback, $v );
			} else {
				return call_user_func_array ( $callback, array (
						$v 
				) );
			}
		}, $array1 );
	}
	
	/**
	 * 减除过长连续数组
	 * 
	 * @param array $array1        	
	 * @return multitype:|multitype:Ambigous <> Ambigous <Ambigous <>>
	 */
	function array_clear(array $array1, $limit = 5) {
		return array_map ( function ($v) use($limit) {
			if (is_array ( $v )) {
				if (count ( $v ) > $limit) {
					$keyys = array_keys ( $v );
					if (isset ( $keyys [$limit] ) && $keyys [$limit] == $limit) {
						$v = [ 
								$v [0],
								$v [1] 
						];
					}
				}
				// $v = array_filter($v);
				return array_clear ( $v );
			} else {
				return $v; // call_user_func_array($callback, array($v));
			}
		}, $array1 );
	}
	
	/**
	 * Decode Json String recursively
	 */
	function json_decode_recursive($ret) {
		return array_map_recursive ( function ($rt) {
			if (strpos ( $rt, '[object]' ) === 0) {
				preg_match ( '/\{.*\}/', $rt, $mt );
				if ($mt) {
					$mtr = json_decode ( ($mt [0]), true );
					if (json_last_error () == JSON_ERROR_NONE) {
						return json_decode_recursive ( $mtr );
					}
				}
			}
			$len = strlen ( $rt );
			if ($len && $rt {0} == '{' && $rt {$len - 1} == '}') {
				$mt = json_decode ( $rt, true );
				if (json_last_error () == JSON_ERROR_NONE) {
					return json_decode_recursive ( $mt );
				}
			}
			return $rt;
		}, $ret );
	}
	
	/**
	 * 读取日志内的接口调用记录
	 * 
	 * @param unknown $fileRealPath        	
	 * @param string $url        	
	 * @return multitype:Ambigous <> |boolean|multitype:|multitype:Ambigous <Ambigous> |Ambigous <number, multitype:, multitype:Ambigous , multitype:multitype:unknown string >
	 */
	function readMonoLogFile($fileRealPath, $url = '') {
		static $returns = [ ];
		static $loaded = [ ];
		
		if ($url) {
			if (isset ( $returns [$url] )) {
				return [ 
						$url => $returns [$url] 
				];
			}
			if (isset ( $loaded [$fileRealPath] )) {
				return false;
			}
		} else {
			if (isset ( $loaded [$fileRealPath] )) {
				return $returns;
			} else {
				$loaded [$fileRealPath] = true;
			}
		}
		
		$filelines = [ ];
		file_exists ( $fileRealPath ) && $filelines = file ( $fileRealPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
		$HTTP_HOST = '';
		$keys = [ 
				'status',
				'message',
				'data' 
		];
		foreach ( $filelines as $key => $line ) {
			preg_match ( '/\{.*\}/', $line, $matchs );
			
			if ($matchs) {
				$matchs = json_decode ( $matchs [0], true );
				
				if (json_last_error () == JSON_ERROR_NONE && isset ( $matchs ['Url'] ) && isset ( $matchs ['func_num_args'] ) && (! $url || endsWith ( $matchs ['Url'], $url ))) {
					if (! $HTTP_HOST) { // Get Host Name
						preg_match ( '/^http(:?s)?:\/\/[^\/]*/', $matchs ['Url'], $mh );
						if ($mh) {
							$HTTP_HOST = $mh [0];
						}
					}
					
					$matchs ['Url'] = substr ( $matchs ['Url'], stripos ( $matchs ['Url'], $HTTP_HOST ) + strlen ( $HTTP_HOST ) );
					
					$matchs ['Url'] = '/' . ltrim ( $matchs ['Url'], '/' );
					
					$ret = $matchs ['func_num_args'];
					count ( $ret ) == 2 && $ret [] = [ ];
					$ret = array_combine ( $keys, $ret );
					// /Filter When Return Data Contains [object]
					// [object] (User\Account: {"uid":159007,"password":"","salt":"","account_status":0,"my_code":"031077"})
					// array_map_recursive
					$ret = json_decode_recursive ( $ret );
					
					$ret = array_clear ( $ret );
					
					// TODO :Remove Large Return
					if (isset ( $returns [$matchs ['Url']] )) {
						$returns [$matchs ['Url']] ['Times'] ++;
						/**
						 * 补全返回信息
						 */
						if (isset ( $ret ['status'] ) && ! isset ( $returns [$matchs ['Url']] ['Return'] [$ret ['status']] )) {
							$returns [$matchs ['Url']] ['Return'] [$ret ['status']] = $ret;
						} else if (isset ( $returns [$matchs ['Url']] ['Return'] [$ret ['status']] )) {
							// TODO :Complete Return Info
							// Complete Input Data
							if (! isset ( $returns [$matchs ['Url']] ['Return'] [$ret ['status']] ['data'] )) {
								edump ( $returns [$matchs ['Url']] ['Return'] [$ret ['status']] );
							}
							
							if (is_array ( $ret ['data'] )) {
								
								$returns [$matchs ['Url']] ['Return'] [$ret ['status']] ['data'] = array_filter ( $returns [$matchs ['Url']] ['Return'] [$ret ['status']] ['data'], function ($v) {
								} ) + $ret ['data'];
								ksort ( $returns [$matchs ['Url']] ['Return'] [$ret ['status']] ['data'] );
							}
							
							// isset($returns[$matchs['Url']]['Return'][$ret['status']]['data'] ) &&
						}
						$returns [$matchs ['Url']] ['Params'] = array_filter ( $returns [$matchs ['Url']] ['Params'] ) + $matchs ['Input'];
					} else {
						if (isset ( $matchs ['Input'] )) {
							// Add Input And Return
							$returns [$matchs ['Url']] = [ 
									'Times' => 1,
									'Url' => $matchs ['Url'],
									'Params' => $matchs ['Input'],
									'Method' => $matchs ['Method'] 
							];
							if (isset ( $ret ['status'] )) {
								$returns [$matchs ['Url']] ['Return'] [$ret ['status']] = $ret;
							}
						} else {
							// TODO :Error Handler
							// echo 'Input Field Not Found<br/>';
							// return false;
						}
					}
				} else {
					// echo 'Line '.$key.' Can\'t Be Json Or Can\'t Find Url<br/>';
					// return false;
				}
			}
		}
		
		if ($url) {
			if (isset ( $returns [$url] )) {
				return [ 
						$url => $returns [$url] 
				];
			} else
				return false;
		}
		return $returns;
	}
	
	/**
	 * Analysis Log File In laravel (MonoLog)
	 */
	function getApiInvokingLog($api) {
		$dir = 'logs';
		$fileName = 'ReqLogs';
		
		$filePath = storage_path () . "/{$dir}/" . $fileName;
		$t = 0;
		
		$result = false;
		for($i = 0; $i < 10; $i ++) {
			$fileRealPath = $filePath . date ( 'Y-m-d', strtotime ( "-{$i} days" ) );
			// if(mt_rand(0,10) > 7 &&
			file_exists ( $fileRealPath ) && readMonoLogFile ( $fileRealPath );
			if (file_exists ( $fileRealPath )) {
				$res = readMonoLogFile ( $fileRealPath, $api );
				if ($res !== false) {
					$t ++;
					$result = $res;
				}
				if ($t >= 2) {
					break;
				}
			}
		}
		
		return $result;
	}
	
	/**
	 * Analysis Log File In laravel (MonoLog)
	 */
	function getReturnInLogFile($dir, $fileName, $last = 0, $url = '') {
		$filePath = storage_path () . "/{$dir}/" . $fileName;
		$i = $last;
		while ( ! file_exists ( $fileRealPath = $filePath . date ( 'Y-m-d', strtotime ( "-{$i} days" ) ) ) && ++ $i && $i < 5 )
			;
			// echo $fileRealPath;
		if (file_exists ( $fileRealPath )) {
			return readMonoLogFile ( $fileRealPath, $url );
		}
	}
	function endsWith($haystack, $needles) {
		foreach ( ( array ) $needles as $needle ) {
			if (( string ) $needle === substr ( $haystack, - strlen ( $needle ) ))
				return true;
		}
		
		return false;
	}
}

if (! function_exists ( 'is_json' )) {
	
	/**
	 * 判断JSON是否合法
	 * 
	 * @param null $string        	
	 * @return bool
	 */
	function is_json($string = null) {
		json_decode ( $string );
		return (json_last_error () == JSON_ERROR_NONE);
	}
}

if (! function_exists ( 'mark' )) {
	
	/**
	 * Calculates the time difference between two marked points.
	 *
	 * @param unknown $point1        	
	 * @param string $point2        	
	 * @param number $decimals        	
	 * @return string|multitype:NULL
	 */
	function mark($point1, $point2 = '', $decimals = 4) {
		static $marker = [ ];
		
		if ($point2 && $point1) {
			if (! isset ( $marker [$point1] ))
				return false;
			if (! isset ( $marker [$point2] )) {
				$marker [$point2] = microtime ();
			}
			
			list ( $sm, $ss ) = explode ( ' ', $marker [$point1] );
			list ( $em, $es ) = explode ( ' ', $marker [$point2] );
			
			return number_format ( ($em + $es) - ($sm + $ss), $decimals );
		} else if ($point1) {
			if ($point1 == '[clear]') {
				$marker = [ ];
			} else {
				$marker [$point1] = microtime ();
			}
		} else {
			return $marker;
		}
	}
	
	/**
	 * Calculates the Memory difference between two marked points.
	 *
	 * @param unknown $point1        	
	 * @param string $point2        	
	 * @param number $decimals        	
	 * @return string|multitype:NULL
	 */
	function memory_mark($point1 = '', $point2 = '', $unit = 'KB', $decimals = 2) {
		static $marker = [ ];
		
		$units = [ 
				'B' => 1,
				'KB' => 1024,
				'MB' => 1048576,
				'GB' => 1073741824 
		];
		$unit = isset ( $units [$unit] ) ? $unit : 'KB';
		if ($point2 && $point1) {
			// 取件间隔
			if (! isset ( $marker [$point1] ))
				return false;
			if (! isset ( $marker [$point2] )) {
				$marker [$point2] = memory_get_usage ();
			}
			
			return number_format ( ($marker [$point2] - $marker [$point1]) / $units [$unit], $decimals ); // .' '.$unit;
		} else if ($point1) {
			// 设记录点
			if ($point1 == '[clear]') {
				$marker = [ ];
			} else {
				$marker [$point1] = memory_get_usage ();
			}
		} else {
			// 返回所有
			return $marker;
		}
	}
	
	/**
	 * Calculates the Memory & Time difference between two marked points.
	 *
	 * @param unknown $point1        	
	 * @param string $point2        	
	 * @param number $decimals        	
	 * @return string|multitype:NULL
	 */
	function mt_mark($point1 = '', $point2 = '', $unit = 'KB', $decimals = 4) {
		static $marker = [ ];
		
		$units = [ 
				'B' => 1,
				'KB' => 1024,
				'MB' => 1048576,
				'GB' => 1073741824 
		];
		$unit = isset ( $units [$unit] ) ? $unit : 'KB';
		if ($point2 && $point1) {
			// 取件间隔
			if (! isset ( $marker [$point1] ))
				return false;
			if (! isset ( $marker [$point2] )) {
				$marker [$point2] = [ 
						'm' => memory_get_usage (),
						't' => microtime () 
				];
			}
			
			list ( $sm, $ss ) = explode ( ' ', $marker [$point1] ['t'] );
			list ( $em, $es ) = explode ( ' ', $marker [$point2] ['t'] );
			
			return [ 
					't' => number_format ( ($em + $es) - ($sm + $ss), $decimals ),
					'm' => number_format ( ($marker [$point2] ['m'] - $marker [$point1] ['m']) / $units [$unit], $decimals ) 
			];
		} else if ($point1) {
			// 设记录点
			if ($point1 == '[clear]') {
				$marker = [ ];
			} else {
				$marker [$point1] = [ 
						'm' => memory_get_usage (),
						't' => microtime () 
				];
			}
		} else {
			// 返回所有
			return $marker;
		}
	}
	function dmt_mark($point1 = '', $point2 = '', $unit = 'MB', $decimals = 4) {
		redline ( $point1 . ' - ' . $point2 );
		$res = mt_mark ( $point1, $point2, $unit, $decimals );
		dump ( $res );
	}
	
	/**
	 *
	 * @param array $xAxis
	 *        	['categories' => range(1,20,1)];
	 * @param array $series
	 *        	['name' => '','data' =>[]];
	 * @param string $yAxis_title        	
	 * @param string $title        	
	 * @param string $subtitle        	
	 * @return \Illuminate\View\$this
	 */
	function chart(array $xAxis, array $series, $title = 'title', $subtitle = 'subtitle', $yAxis_title = 'yAxis_title') {
		$chartData = [ 
				'title' => $title,
				'subtitle' => $subtitle,
				'xAxis' => json_encode ( $xAxis ),
				'yAxis_title' => $yAxis_title,
				'series' => json_encode ( $series ) 
		];
		return \View::make ( 'localtest.chart' )->with ( 'chartData', $chartData );
	}
	function statisticsExecTime($func, array $params, $xAxis) {
		set_time_limit ( 170 );
		$func_name = '';
		if (is_array ( $func )) {
			if (! method_exists ( $func [0], $func [1] )) {
				return false;
			}
			$func_name = object_name ( $func [0] ) . '->' . $func [1];
		} else if (is_string ( $func )) {
			if (! function_exists ( $func )) {
				return false;
			}
			$func_name = $func;
		} else if (is_callable ( $func )) {
			// if(! function_exists($func)){
			// return false;
			// }
			$func_name = 'Closure';
		} else {
			return false;
		}
		
		$mem = [ ];
		$time = [ ];
		foreach ( $params as $v ) {
			mark ( 'start' );
			
			$result = call_user_func_array ( $func, ( array ) $v );
			
			$time [] = floatval ( mark ( 'start', 'end' ) );
			$memory = memory_mark ();
			if (isset ( $memory ['start'] ) && isset ( $memory ['end'] )) {
				$mem [] = floatval ( memory_mark ( 'start', 'end' ) );
			}
			mark ( '[clear]' );
			memory_mark ( '[clear]' );
		}
		$data = [ 
				[ 
						'name' => 'Exec Time',
						'data' => $time 
				] 
		];
		$mem && $data [] = [ 
				'name' => 'Exec Memory',
				'data' => $mem 
		];
		$xAxis = [ 
				'categories' => $xAxis 
		];
		return chart ( $xAxis, $data, 'Function [' . htmlentities ( $func_name ) . '] Execute Time Statistics', 'At ' . date ( 'Y-m-d H:i:s' ), 'Number' );
	}
}

if (! function_exists ( 'curl' )) {
	function curl_get($api) {
		// $api = 'http://v.showji.com/Locating/showji.com20150416273007.aspx?output=json&m='.$phone;
		$ch = curl_init ();
		
		curl_setopt ( $ch, CURLOPT_URL, $api );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
		$User_Agen = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36';
		curl_setopt ( $ch, CURLOPT_USERAGENT, $User_Agen );
		
		$result = curl_exec ( $ch );
		return $result;
		$result = json_decode ( $result, true );
	}
	function curl_post($url, $data, $method = 'POST') {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url ); // url
		curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, $method );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		$User_Agen = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36';
		curl_setopt ( $ch, CURLOPT_USERAGENT, $User_Agen );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
		if (! empty ( $data )) {
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data ); // 数据
		}
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$info = curl_exec ( $ch );
		
		curl_close ( $ch );
		edump ( $info );
		$json = json_decode ( $info, 1 );
		if ($json) {
			return $json;
		} else {
			return false;
		}
	}
	function curl_multi_request($query_arr, $data, $method = 'POST') {
		$ch = curl_multi_init ();
		$count = count ( $query_arr );
		$ch_arr = array ();
		for($i = 0; $i < $count; $i ++) {
			$query_string = $query_arr [$i];
			$ch_arr [$i] = curl_init ( $query_string );
			curl_setopt ( $ch_arr [$i], CURLOPT_RETURNTRANSFER, true );
			
			curl_setopt ( $ch_arr [$i], CURLOPT_POST, 1 );
			curl_setopt ( $ch_arr [$i], CURLOPT_POSTFIELDS, $data ); // post 提交方式
			
			curl_multi_add_handle ( $ch, $ch_arr [$i] );
		}
		$running = null;
		do {
			curl_multi_exec ( $ch, $running );
		} while ( $running > 0 );
		for($i = 0; $i < $count; $i ++) {
			$results [$i] = curl_multi_getcontent ( $ch_arr [$i] );
			curl_multi_remove_handle ( $ch, $ch_arr [$i] );
		}
		curl_multi_close ( $ch );
		return $results;
	}
}

if (! function_exists ( 'randStr' )) {
	function randStr($len = 6, $format = 'NUMBER') {
		switch ($format) {
			case 'ALL' :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
				break;
			case 'CHAR' :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~';
				break;
			case 'NUMBER' :
				$chars = '0123456789';
				break;
			default :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
				break;
		}
		// mt_srand ( ( double ) microtime () * 1000000 * getmypid () );
		$password = "";
		while ( strlen ( $password ) < $len )
			$password .= substr ( $chars, (mt_rand () % strlen ( $chars )), 1 );
		return $password;
	}
}

if (! function_exists ( 'dump' )) {
	
	/**
	 * 浏览器友好的变量输出
	 *
	 * @param mixed $var
	 *        	变量
	 * @param boolean $echo
	 *        	是否输出 默认为True 如果为false 则返回输出字符串
	 * @param string $label
	 *        	标签 默认为空
	 * @param boolean $strict
	 *        	是否严谨 默认为true
	 * @return void|string
	 */
	function dump($var, $echo = true, $label = null, $strict = true) {
		$label = ($label === null) ? '' : rtrim ( $label ) . ' ';
		if (! $strict) {
			if (ini_get ( 'html_errors' )) {
				$output = print_r ( $var, true );
				$output = '<pre>' . $label . htmlspecialchars ( $output, ENT_QUOTES ) . '</pre>';
			} else {
				$output = $label . print_r ( $var, true );
			}
		} else {
			ob_start ();
			var_dump ( $var );
			$output = ob_get_clean ();
			if (! extension_loaded ( 'xdebug' )) {
				$output = preg_replace ( '/\]\=\>\n(\s+)/m', '] => ', $output );
				$output = '<pre>' . $label . htmlspecialchars ( $output, ENT_QUOTES ) . '</pre>';
			}
		}
		if ($echo) {
			echo ($output);
			return null;
		} else
			return $output;
	}
	
	if (! function_exists ( 'export' )) {
		function export($var) {
			echo '<pre>';
			var_export ( $var );
			echo '</pre>';
		}
		function eexport($var) {
			export ( $var );
			exit ();
		}
	}
	
	if (! function_exists ( 'line' )) {
		function line($var, $eof = PHP_EOL) {
			echo $var . $eof;
		}
		function redline($var) {
			echo '<p style="color:red;">' . $var . '</p>';
		}
		function lp($var) {
			echo '<p>' . $var . '</p>';
		}
	}
}

if (! function_exists ( 'edump' )) {
	
	/**
	 * Dump And Exit
	 * 
	 * @param mix $var        	
	 * @param string $echo        	
	 * @param string $label        	
	 * @param string $strict        	
	 */
	function edump($var) {
		// echo '<pre>';
		dump ( $var );
		// echo '</pre>';
		// dump($var);
		// call_user_func_array('dump', func_get_args());
		exit ();
	}
	function edumpLastSql() {
		edump ( lastSql () );
	}
	function dumpLastSql() {
		dump ( lastSql () );
	}
}

if (! function_exists ( 'counter' )) {
	
	/**
	 * A Counter Achieve By Static Function Var
	 * 
	 * @return number
	 */
	function counter() {
		static $c = 0;
		
		return $c ++;
	}
}

if (! function_exists ( 'sql' )) {
	
	/**
	 * Echo An Sql Statment Friendly
	 * 
	 * @param string $subject
	 *        	Sql Statment
	 * @param array $binds
	 *        	The Bind Params
	 * @return unknown
	 */
	function sql($subject, array $binds = []) {
		$pattern = '/(select\s+|from\s+|where\s+|and\s+|or\s+|\s+limit|,|(?:left|right|inner)\s+join)/i';
		
		$var = preg_replace ( $pattern, '<br/>\\1', $subject );
		
		$i = 0;
		
		$binds && $var = preg_replace_callback ( '/\?/', function ($matchs) use(&$i, $binds) {
			return '\'' . $binds [$i ++] . '\'';
		}, $var );
		
		echo $var . '<br/>';
	}
	
	/**
	 * Echo Last Sql
	 */
	function sqlLastSql() {
		$query = lastSql ();
		sql ( $query ['query'], $query ['bindings'] );
	}
	
	/**
	 * Echo Last Sql And Exit
	 */
	function esqlLastSql() {
		$query = lastSql ();
		sql ( $query ['query'], $query ['bindings'] );
		exit ();
	}
}

if (! function_exists ( 'object_name' )) {
	
	/**
	 * 获取对象的类名
	 * 
	 * @param unknown $name        	
	 */
	function object_name($name) {
		return (new \ReflectionObject ( $name ))->name;
	}
	
	/**
	 * Dump The Class Name Of An Given Object
	 * 
	 * @param String $obj
	 *        	The Given Object
	 */
	function dump_object_name($obj) {
		dump ( object_name ( $obj ) );
	}
	function edump_object_name($obj) {
		edump ( object_name ( $obj ) );
	}
	
	/**
	 * 获取文件指定行的内容
	 * 
	 * @param string $filename
	 *        	文件名
	 * @param integer $start
	 *        	开始行>=1
	 * @param integer $offset
	 *        	偏移量
	 * @return array 所请求行的数组
	 */
	function getRows($filename, $start, $offset = 0) {
		$rows = file ( $filename );
		$rowsNum = count ( $rows );
		if ($offset == 0 || (($start + $offset) > $rowsNum)) {
			$offset = $rowsNum - $start;
		}
		$fileList = array ();
		for($i = $start; $max = $start + $offset, $i < $max; $i ++) {
			$fileList [] = substr ( $rows [$i], 0, - 2 );
		}
		return $fileList;
	}
	
	/**
	 * Get The Anntation Array Of Given Function
	 * 
	 * @param unknown $function        	
	 * @return boolean|multitype:multitype:multitype:string $data = [
	 *         '@return' => ['name' => '','type' => '','note' => ''],
	 *         '@param' => ['name' => '','type' => '','note' => ''],
	 *         'function' => ['note' => ''],
	 *         ];
	 */
	function getAnnotation($function) {
		$reflect = getFunctionReflection ( $function );
		if ($reflect === false)
			return false;
		$start = $reflect->getStartLine () - 1;
		$end = $reflect->getEndLine ();
		$file = $reflect->getFileName ();
		$offset = $end - $start;
		$rows = file ( $file );
		$rowsNum = count ( $rows );
		$annotation = [ ];
		$i = $start - 1;
		
		while ( ($ann = trim ( $rows [$i --] )) && (strpos ( $ann, '//' ) === 0 || strpos ( $ann, '*' ) === 0 || strpos ( $ann, '/*' ) === 0) ) {
			($ann = trim ( $ann, "/* \t" )) && $annotation [] = $ann;
		}
		
		$annData = [ ];
		$tmp = [ ];
		foreach ( $annotation as $value ) {
			if (stripos ( $value, '@' ) === 0) {
				// TODO::Process @Return
				$exp = explode ( ' ', $value );
				$count = count ( $exp );
				$attr = [ ];
				if ($count == 2) {
					$attr = [ 
							'type' => $exp [1] 
					];
				} else if ($count >= 3) {
					$attr = [ 
							'type' => $exp [1],
							'name' => $exp [2] 
					];
					for($i = 3; $i < $count; $i ++) {
						$tmp [] = $exp [$i];
					}
				} else {
					continue;
				}
				if ($tmp) {
					$tmp = array_reverse ( $tmp );
					$tmp = implode ( ' ', $tmp );
					$attr [$exp [0]] ['note'] = $tmp;
				}
				$annData [$exp [0]] [] = $attr;
				$tmp = [ ];
			} else {
				$tmp [] = $value;
			}
		}
		if ($tmp) {
			$tmp = array_reverse ( $tmp );
			$tmp = implode ( ' ', $tmp );
			$annData ['function'] [] = [ 
					'note' => $tmp 
			];
		}
		return $annData;
	}
	
	/**
	 * Get The Paramaters Of Given Function
	 * 
	 * @param unknown $function        	
	 * @return boolean|multitype:NULL
	 */
	function getFunctionParamaters($function) {
		$reflect = getFunctionReflection ( $function );
		if ($reflect === false)
			return false;
		$parameters = $reflect->getParameters ();
		$params = array ();
		foreach ( $parameters as $value ) {
			$params [] = $value->getName ();
		}
		return $params;
	}
	
	/**
	 * 获取方法的反射
	 * 
	 * @param string|array $function
	 *        	方法名
	 * @return boolean|ReflectionFunction
	 */
	function getFunctionReflection($name) {
		if (is_array ( $name )) {
			if (method_exists ( $name [0], $name [1] )) {
				$reflect = new ReflectionMethod ( $name [0], $name [1] );
			} else {
				return false;
			}
		} else {
			try {
				$reflect = new ReflectionFunction ( $name );
			} catch ( \Exception $e ) {
				return false;
			}
		}
		return $reflect;
	}
	
	/**
	 * 获取方法的代码
	 * 
	 * @param unknown $name        	
	 * @return boolean|multitype:Ambigous
	 */
	function getFunctionDeclaration($name, $show = false) {
		$reflect = getFunctionReflection ( $name );
		if ($reflect === false)
			return false;
		$start = $reflect->getStartLine ();
		$end = $reflect->getEndLine ();
		$file = $reflect->getFileName ();
		if ($show) {
			dump ( $file . ":$start - $end" );
		}
		$res = getRows ( $file, $start - 1, $end - $start + 1 );
		return $res;
	}
}

if (! function_exists ( 'to_array' )) {
	
	/**
	 * Convert Object Array To Array Recursively
	 * 
	 * @param unknown $arr        	
	 */
	function to_array(&$arr) {
		$arr = ( array ) $arr;
		$arr && array_walk ( $arr, function (&$v, $k) {
			$v = ( array ) $v;
		} );
	}
}

if (! function_exists ( 'lode' )) {
	/**
	 * 分割数组或字符串处理
	 *
	 * @param string $type
	 *        	: , | @
	 * @param type $data
	 *        	: array|string
	 * @internal string $type ->a=array ->explode || $type ->s=string ->implode
	 * @return array string
	 */
	function lode($type, $data) {
		if (is_string ( $data )) {
			return explode ( $type, $data );
		} elseif (is_array ( $data )) {
			return implode ( $type, $data );
		}
	}
}

if (! function_exists ( 'createInsertSql' )) {
	
	/**
	 * Create An Insert Sql Statement
	 * 
	 * @param string $tbname        	
	 * @param array $data        	
	 * @return string
	 */
	function createInsertSql($tbname, array $data) {
		$fields = implode ( '`,`', array_keys ( $data ) );
		$values = implode ( '\',\'', array_values ( $data ) );
		$sql = 'insert into `' . $tbname . '`(`' . $fields . '`)values(\'' . $values . '\')';
		return $sql;
	}
	
	/**
	 * Create An Insert Sql Statement With Param Placeholder
	 * 
	 * @param string $tbname        	
	 * @param array $data        	
	 * @return multitype:string multitype:
	 */
	function createInsertSqlBind($tbname, array $data) {
		$keys = array_keys ( $data );
		$values = array_values ( $data );
		$fields = implode ( '`,`', $keys );
		$places = array_fill ( 0, count ( $keys ), '?' );
		$places = implode ( ',', $places );
		$sql = 'insert into `' . $tbname . '`(`' . $fields . '`)values(' . $places . ')';
		return [ 
				'sql' => $sql,
				'data' => $values 
		];
	}
}

if (! function_exists ( 'createUpdateSql' )) {
	
	/**
	 * Create A Update Sql Statement
	 * 
	 * @param string $tbname        	
	 * @param array $data        	
	 * @param string $where        	
	 * @return string
	 */
	function createUpdateSql($tbname, array $data, $where = '') {
		$set = '';
		$wh = '';
		foreach ( $data as $k => $v ) {
			$set .= ',`' . $k . '` = \'' . $v . '\'';
		}
		if (is_array ( $where )) {
			foreach ( $where as $k => $v ) {
				$wh .= ' and `' . $k . '` = \'' . $v . '\'';
			}
			$wh = substr ( $wh, 4 );
		} else {
			$wh = $where;
		}
		$wh = empty ( $wh ) ? $wh : ' WHERE ' . $wh;
		$set = substr ( $set, 1 );
		$sql = 'UPDATE `' . $tbname . '` SET ' . $set . $wh;
		return $sql;
	}
}

if (! function_exists ( 'old' )) {
	
	/**
	 * Get Previous Form Field Data
	 * 
	 * @param string $key        	
	 * @param string $default        	
	 */
	function old($key = null, $default = null) {
		return app ( 'request' )->old ( $key, $default );
	}
}

if (! function_exists ( 'insert' )) {
	
	/**
	 * Execute Insert Sql Statment
	 * 
	 * @param unknown $table        	
	 * @param array $data        	
	 */
	function insert($table, array $data) {
		$result = createInsertSqlBind ( $table, $data );
		return DB::insert ( $result ['sql'], $result ['data'] );
	}
}

if (! function_exists ( 'update' )) {
	
	/**
	 * Execute Update Sql Statment
	 * 
	 * @param unknown $table        	
	 * @param array $data        	
	 * @param unknown $where        	
	 */
	function update($table, array $data, $where) {
		$sql = createUpdateSql ( $table, $data, $where );
		return DB::update ( $sql );
	}
}
if (! function_exists ( 'lastInsertId' )) {
	
	/**
	 * Get Last Insert Id
	 */
	function lastInsertId() {
		return DB::getPdo ()->lastInsertId ();
	}
}
if (! function_exists ( 'lastSql' )) {
	
	/**
	 * Get Last Query
	 * 
	 * @return mixed
	 */
	function lastSql() {
		$sql = DB::getQueryLog ();
		$query = end ( $sql );
		return $query;
	}
}


