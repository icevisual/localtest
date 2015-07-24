<?php 
/*
 * |--------------------------------------------------------------------------
 * | Application Helpers
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register all of the Helpers for an application.
 * |
 */

if(! function_exists('getReturnInLogFile')){
	
	
	/**
	 * Analysis Log File In laravel (MonoLog)
	 */
	function getReturnInLogFile($dir,$fileName){
		$filePath = storage_path () . "/{$dir}/".$fileName;
		$i = 0;
		while (!file_exists($fileRealPath = $filePath . date ( 'Y-m-d',strtotime("-{$i} days") ) ) && ++ $i && $i < 5);
		if(file_exists($fileRealPath)){
			$filelines = file($fileRealPath,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			$returns = [];
			foreach ($filelines  as $key => $line){
				preg_match('/\{.*\}/', $line,$matchs);
				if($matchs){
					$matchs = json_decode($matchs[0],true);
					if (json_last_error() == JSON_ERROR_NONE
							&& isset($matchs['Url']) ){
						$HTTP_HOST 		= $_SERVER['HTTP_HOST'];
						$matchs['Url'] 	= substr($matchs['Url'], stripos($matchs['Url'], $HTTP_HOST) + strlen($HTTP_HOST));
						if(isset($returns[$matchs['Url']])){
							/**
							 * 补全返回信息
							 */
							$ret = end($matchs);
							if(isset($ret['status']) && 
									!isset($returns[$matchs['Url']] ['Return'. $ret['status']])  ){
								$returns[$matchs['Url']] ['Return'. $ret['status']] = $ret;
							}else{
								//TODO::Complete Return Info
								
							}
							
						//	$returns[$matchs['Url']] = array_merge($returns[$matchs['Url']],end($matchs)) ;
						}else{
							if(isset($matchs['Input'])){
								//Add Input And Return
								//Add Success Return ?
								$ret = end($matchs);
								$returns[$matchs['Url']] = [
										'Input' 	=> $matchs['Input'],
								];
								if(isset($ret['status'])  ){
									$returns[$matchs['Url']] ['Return'. $ret['status']] = $ret;
								}
							}else{
								//TODO::Error Handler
								echo 'Input Field Not Found<br/>';
							}
						}
					}else{
						echo 'Line '.$key.' Can\'t Be Json Or Can\'t Find Url<br/>';
					}
				}
			}
			return  $returns;
		}
	}
}



if(!function_exists('is_json')){

	/**
	 * 判断JSON是否合法
	 * @param null $string
	 * @return bool
	 */
	function is_json($string = null) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}


}


if(! function_exists('mark')){
	
	
	/**
	 * Calculates the time difference between two marked points.
	 * 
	 * @param unknown $point1
	 * @param string $point2
	 * @param number $decimals
	 * @return string|multitype:NULL
	 */
	function mark($point1 , $point2 = '', $decimals = 4)
	{
		static $marker = [];
		
		if($point2 && $point1){
			if ( ! isset($marker[$point2]))
			{
				$marker[$point2] = microtime();
			}
			
			list($sm, $ss) = explode(' ', $marker[$point1]);
			list($em, $es) = explode(' ', $marker[$point2]);
			
			return number_format(($em + $es) - ($sm + $ss), $decimals);
		}else if($point1){
			$marker[$point1] = microtime();
		}else{
			return $marker;
		}
	}
	
}

if(!function_exists('curl')) {

	function curl($url, $data, $method='POST')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);//url
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, method);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$User_Agen = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36';
		curl_setopt($ch, CURLOPT_USERAGENT, $User_Agen);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		if (!empty($data)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//数据
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$info = curl_exec($ch);
		curl_close($ch);
		$json = json_decode($info, 1);
		if ($json) {
			return $json;
		} else {
			return false;
		}
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
		// 		mt_srand ( ( double ) microtime () * 1000000 * getmypid () );
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
	
	if (! function_exists ( 'line' )) {
		function line($var, $eof = PHP_EOL) {
			echo $var . $eof;
		}
		function redline($var) {
			echo '<p style="color:red;">' . $var . '</p>';
		}
	}
}


if (! function_exists ( 'edump' )) {

	/**
	 * Dump And Exit
	 * @param mix $var
	 * @param string $echo
	 * @param string $label
	 * @param string $strict
	 */
	function edump($var) {
	//	echo '<pre>';
		dump($var);
	//	echo '</pre>';
		//dump($var);
		//call_user_func_array('dump', func_get_args());
		exit ();
		
	}
	
	function edumpLastSql() {
		edump(lastSql());
	}
	
}


if (! function_exists ( 'counter' )) {
	
	/**
	 * A Counter Achieve By Static Function Var
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
	 * @param string $subject
	 * 	Sql Statment
	 * @param array $binds
	 * 	The Bind Params
	 * @return unknown
	 */
	function sql($subject, array $binds = []) {
		
		$pattern = '/(select\s+|from\s+|where\s+|and\s+|or\s+|\s+limit|,|(?:left|right|inner)\s+join)/i';
		
		$var = preg_replace ( $pattern, '<br/>\\1', $subject );
		
		$i = 0;
		
		$binds && $var = preg_replace_callback ( '/\?/', function ($matchs) use(&$i, $binds) {
			return $binds [$i ++];
		}, $var );
		
		echo $var.'<br/>';
	}
	
	/**
	 * Echo Last Sql
	 */
	function sqlLastSql(){
		$query = lastSql();
		sql($query['query'],$query['bindings']);
	}
	
	/**
	 * Echo Last Sql And Exit 
	 */
	function esqlLastSql(){
		$query = lastSql();
		sql($query['query'],$query['bindings']);
		exit;
	}
	
}


if (! function_exists ( 'object_name' )) {
	
	/**
	 * 获取对象的类名
	 * @param unknown $name
	 */
	function object_name($name) {
		return (new \ReflectionObject ( $name ))->name;
	}

	/**
	 * Dump The Class Name Of An Given Object 
	 * @param String $obj
	 * 	The Given Object 
	 */
	function dump_object_name($obj) {
		dump ( object_name ( $obj ) );
	}
	function edump_object_name($obj) {
		edump ( object_name ( $obj ) );
	}
	
	
	/**
	 * 获取文件指定行的内容
	 * @param string $filename
	 * 	文件名
	 * @param integer $start
	 * 	开始行>=1
	 * @param integer $offset
	 * 	偏移量
	 * @return array
	 * 	所请求行的数组
	 */
	function getRows($filename, $start, $offset = 0) {
		$rows = file ( $filename );
		$rowsNum = count ( $rows );
		if ($offset == 0 || (($start + $offset) > $rowsNum)) {
			$offset = $rowsNum - $start;
		}
		$fileList = array ();
		for($i = $start; $max = $start + $offset, $i < $max; $i ++) {
			$fileList [] = substr($rows [$i], 0,-2) ;
		}
		return $fileList;
	}
	
			
	/**
	 * Get The Anntation Array Of Given Function
	 * @param unknown $function
	 * @return boolean|multitype:multitype:multitype:string
	 * 		$data = [
					'@return' 		=> ['name' => '','type' => '','note' => ''],
					'@param'		=> ['name' => '','type' => '','note' => ''],
					'function' 		=> ['note' => ''],
			];
	 */
	function getAnnotation($function){
	
		$reflect 	= getFunctionReflection($function);
		if($reflect === false) return false;
		$start 		= $reflect->getStartLine () - 1;
		$end 		= $reflect->getEndLine ();
		$file 		= $reflect->getFileName ();
		$offset		= $end - $start;
		$rows 		= file ( $file );
		$rowsNum 	= count ( $rows );
		$annotation = [];
		$i 			= $start  - 1;
		
		while( ( $ann = trim($rows [$i --]) ) 
					&&(	strpos($ann, '//') === 0 || 
						strpos($ann, '*') === 0 ||
						strpos($ann, '/*') === 0 )  ){
			( $ann = trim($ann,"/* \t") ) && $annotation [] = $ann;
		}
		
		$annData 	= [];
		$tmp 		= [];
		foreach ($annotation as $value){
			if(stripos($value, '@') === 0){
				//TODO::Process @Return
				$exp 	= explode(' ', $value);
				$count 	= count($exp);
				$attr	= [];
				if($count == 2){
					$attr = [
							'type' => $exp[1]
					];
				}else if ($count >= 3){
					$attr = [
							'type' => $exp[1],
							'name' => $exp[2]
					];
					for($i = 3 ; $i < $count ; $i ++){
						$tmp[] =  $exp[$i];
					}
				}else{
					continue;
				}
				if($tmp){
					$tmp = array_reverse($tmp);
					$tmp = implode(' ', $tmp);
					$attr[$exp[0]]['note'] = $tmp;
				}
				$annData [$exp[0]][] = $attr;
				$tmp 		= [];
			}else{
				$tmp[] = $value;
			}
		}
		if($tmp){
			$tmp = array_reverse($tmp);
			$tmp = implode(' ', $tmp);
			$annData['function'] [] = ['note'=>$tmp];
		}
		return $annData;
	}
	
	/**
	 * Get The Paramaters Of Given Function
	 * @param unknown $function
	 * @return boolean|multitype:NULL
	 */
	function getFunctionParamaters($function){
		$reflect 	= getFunctionReflection($function);
		if($reflect === false) return false;
		$parameters = $reflect->getParameters();
		$params 	= array();
		foreach ($parameters as $value){
			$params [] = $value->getName();
		}
		return $params;
	}
	
	/**
	 * 获取方法的反射
	 * @param string|array $function
	 * 方法名
	 * @return boolean|ReflectionFunction
	 */
	function getFunctionReflection($name){
		if (is_array ( $name )) {
			if (method_exists ( $name [0], $name [1] )) {
				$reflect = new ReflectionMethod ( $name [0], $name [1] );
			} else {
				return false;
			}
		} else{ 
			try{
				$reflect = new ReflectionFunction ( $name );
			}catch (\Exception $e){
				return false;
			}
		}
		return $reflect;
	}
	
	
	/**
	 * 获取方法的代码
	 * @param unknown $name
	 * @return boolean|multitype:Ambigous
	 */
	function getFunctionDeclaration($name,$show = false) {
		$reflect 	= getFunctionReflection($name);
		if($reflect === false) return false;
		$start 		= $reflect->getStartLine ();
		$end 		= $reflect->getEndLine ();
		$file 		= $reflect->getFileName ();
		if($show){
			dump($file.":$start - $end");
		}
		$res = getRows ( $file, $start - 1, $end - $start + 1 );
		return $res;
	}
		
		
}

if (! function_exists ( 'to_array' )) {
	
	/**
	 * Convert Object Array To Array Recursively
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
	 * @param unknown $table
	 * @param array $data
	 * @param unknown $where
	 */
	function update($table,array  $data, $where) {
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
	 * @return mixed
	 */
	function lastSql() {
		$sql = DB::getQueryLog ();
		$query = end ( $sql );
		return $query;
	}
}


