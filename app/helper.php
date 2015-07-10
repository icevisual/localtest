<?php

/*
 * |--------------------------------------------------------------------------
 * | Application Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register all of the routes for an application.
 * | It's a breeze. Simply tell Laravel the URIs it should respond to
 * | and give it the Closure to execute when that URI is requested.
 * |
 */



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
			echo $var . PHP_EOL;
		}
		function redline($var) {
			echo '<p style="color:red;">' . $var . '</p>';
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
		echo '<pre>';
		var_dump($var);
		echo '</pre>';
		
		//dump($var);
		//call_user_func_array('dump', func_get_args());
		exit ();
		
	}
	
	function edumpLastSql() {
		edump(lastSql());
	}
	
}


if (! function_exists ( 'counter' )) {
	function counter() {
		static $c = 0;
		
		return $c ++;
	}
}

if (! function_exists ( 'sql' )) {
	function sql($subject, array $binds = []) {
		
		$pattern = '/(select\s+|from\s+|where\s+|and\s+|or\s+|\s+limit|,|(?:left|right|inner)\s+join)/i';
		
		$var = preg_replace ( $pattern, '<br/>\\1', $subject );
		
		$i = 0;
		
		$binds && $var = preg_replace_callback ( '/\?/', function ($matchs) use(&$i, $binds) {
			return $binds [$i ++];
		}, $var );
		
		echo $var.'<br/>';
	}
	
	function sqlLastSql(){
		$query = lastSql();
		sql($query['query'],$query['bindings']);
	}
	
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
	function dump_object_name($name) {
		dump ( object_name ( $name ) );
	}
	function edump_object_name($name) {
		edump ( object_name ( $name ) );
	}
	
	/**
	 * 获取文件指定行的内容
	 * @param unknown $filename
	 * @param unknown $start
	 * @param number $offset
	 * @return multitype:Ambigous <>
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
	 * 获取方法的代码
	 * @param unknown $name
	 * @return boolean|multitype:Ambigous
	 */
	function getFunctionDeclaration($name,$show = false) {
		if (is_array ( $name )) {
			if (method_exists ( $name [0], $name [1] )) {
				$reflect = new ReflectionMethod ( $name [0], $name [1] );
			} else {
				return false;
			}
		} else{ 
			//if ( function_exists ( $name ) ) {//Closure Failed
			try{
				$reflect = new ReflectionFunction ( $name );
			}catch (\Exception $e){
				return false;
			}
// 		} else {
// 			return false;
		}
		$start = $reflect->getStartLine ();
		$end = $reflect->getEndLine ();
		$file = $reflect->getFileName ();
		if($show){
			dump($file.":$start - $end");
		}
		$res = getRows ( $file, $start - 1, $end - $start + 1 );
		return $res;
	}
		
		
}

if (! function_exists ( 'to_array' )) {
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
	function old($key = null, $default = null) {
		return app ( 'request' )->old ( $key, $default );
	}
}

if (! function_exists ( 'insert' )) {
	function insert($table, array $data) {
		$result = createInsertSqlBind ( $table, $data );
		return DB::insert ( $result ['sql'], $result ['data'] );
	}
}

if (! function_exists ( 'update' )) {
	function update($table,array  $data, $where) {
		$sql = createUpdateSql ( $table, $data, $where );
		return DB::update ( $sql );
	}
}
if (! function_exists ( 'lastInsertId' )) {
	function lastInsertId() {
		return DB::getPdo ()->lastInsertId ();
	}
}
if (! function_exists ( 'lastSql' )) {
	function lastSql() {
		$sql = DB::getQueryLog ();
		$query = end ( $sql );
		return $query;
	}
}


