<?php

class LocalTest1Controller extends \BaseController
{

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
	
	function createInsertSql($tbname, array $data) {
		$fields = implode ( '`,`', array_keys ( $data ) );
		$values = implode ( '\',\'', array_values ( $data ) );
		$sql = 'insert into `' . $tbname . '`(`' . $fields . '`)values(\'' . $values . '\')';
		return $sql;
	}
	
	/**
	 * 生成Code SQL
	 * @param unknown $len
	 * 	code长度
	 * @param unknown $min
	 * 	最小值
	 * @param unknown $limit
	 * 	数量
	 * @param unknown $filename
	 * 	保存的文件名
	 */
	public function generate($len,$min,$limit,$filename){
		$t 		= microtime(true);
		$codes 	= array();
		
		while($limit - 10 > ($count = count($codes)) ){
			$rest =  $limit - $count;
			for($i = 0 ; $i < $rest ; $i ++){
				$codes[$this->randStr($len)] = true;
			}
		}
		
		$now = date('Y-m-d H:i:s',time()) ;
			
		$file = fopen($filename,'w') or die('Faild To Open File');
		$sqlCount = 0;
		foreach ($codes as $k=> $v){
			if($k > $min){
				$sqlCount++;
				$sql = $this->createInsertSql('gzb_user_redpacket_code', ['my_code'=>$k,'created_at'=>$now]);
				fwrite($file, $sql.';'.PHP_EOL);
			}
		}
		fclose($file);
		
		echo '<p>Codes:'.$sqlCount.'</p>';
		$time =  (microtime(true) - $t);
		echo '<p>Time:'.$time.'s</p>';
	}
	
	public function generate_10w(){
		echo '<h1>generate_10w</h1>';
		echo '<p>File: /public/sql10w.sql</p>';
		$this->generate(5, '01000', 100000, 'sql10w.sql');
	}
	
	public function generate_100w(){
		echo '<h1>generate_100w</h1>';
		echo '<p>File: /public/sql100w.sql</p>';
		$this->generate(6, '010000', 900000, 'sql100w.sql');
	}
	
	
	/**
	 * 先导入10w的，再导入100w的，系统是从头开始取Code的
	 */
	public function run(){
		$this->generate_10w();
		$this->generate_100w();
	}
}