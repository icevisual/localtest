<?php

use Ser\Redpacket\RedpacketService;
use Lib\Fun\Fun;
use Redpacket\RedpacketCode;
use Area\AreaMo;
use User\Cheat;
use User\Account;
use Redpacket\RedpacketWithdraw;
use Redpacket\Redpacket;
use Ser\Lend\LendService;
use Lib\Fun\Post;
use User\PhoneHome;
class GeneralTestController extends \BaseController
{
	
	public function getCode(){
		$phone = Input::get('phone');
		$redis = LRedis::connection ();
		$userInfo = array ();
		$userInfo = json_decode ( $redis->get ( $phone ), 1 );
		Fun::msg(200,'',$userInfo);
	}
	
	/**
	 * 生成订单号
	 * @param number $num
	 * @return string
	 */
	protected function createRepBusNo($num = 17)
	{
		list($usec, $sec) = explode(" ", microtime());
		$usec = (int)($usec * 10000);
		$str = $sec . $usec . mt_rand(100000, 999999);
		$str = substr($str, 0, $num);
		return $str;
	}
	
	
	/**
	 * 分割Query
	 * @param unknown $query
	 * @return multitype:Ambigous <>
	 */
	public function getQueryParams($query){
		$params = explode('&', $query);
		$result = [];
		array_walk($params, function ($v,$k) use (&$result){
			$param = explode('=', $v);
			$result[$param[0]] = $param[1] ;
		});
		return $result;
	}
	
	/**
	 * 获取URL的query
	 * @param unknown $reqUrl
	 * @return string|multitype:Ambigous
	 */
	public function getUrlQuery($reqUrl){
		$reqUrl = urldecode($reqUrl);
		if(!parse_url($reqUrl)['query']) return  '';
		$query  = parse_url($reqUrl)['query'];
		return $this->getQueryParams($query);
	}
	
	/**
	 * 生成江浙沪的所有区ID
	 * @param unknown $uid
	 * @return boolean
	 */
	public static function outer_province($uid){
		echo 'Generate outer_province_ids...<br/>';
		$created_at = '2014-07-10 00:00:00';
		$provices_id = ['5','2739','1770'];//浙江、广东、上海、江苏
		$city_id = [];
		$country_id = [];
		$areas_p = AreaMo::whereIn('cid',$provices_id)->get()->toArray();
		foreach ($areas_p as $value){
			$temp = AreaMo::where('cup',$value['cid'])->get()->toArray();
			foreach ($temp as $v){
				$city_id[] = $v['cid'];
			}
		}
		$areas_c = AreaMo::whereIn('cid',$city_id)->get()->toArray();
		foreach ($areas_c as $value){
			$temp = AreaMo::where('cup',$value['cid'])->get()->toArray();
			foreach ($temp as $v){
				$country_id[] = $v['cid'];
			}
		}
	
		$data = [
				'provices_id'	=> implode(',', $provices_id),
				'city_id'		=> implode(',', $city_id),
				'country_id'	=> implode(',', $country_id)
		];
		$jiang_zhe_hu_ids = $data['provices_id'].','.$data['city_id'].','.$data['country_id'];
		$jiang_zhe_hu_ids = $data['country_id'];
		$content =<<<EOF
<?php
	
	return [
		$jiang_zhe_hu_ids
];
	
EOF;
		file_put_contents('jiang_zhe_hu_ids.php', $content);
	
		return count($data);
	}
	
	
	/**
	 * 获取江浙沪内有订单才OK的人数
	 */
	public function getAllValidOldUid(){
		$created_at = '2015-07-11 00:00:00';
		$jiang_zhe_hu_ids = '26,27,28,29,30,31,32,3234,3235,3236,3237,3238,3239,33,34,37,39,41,43,47,3240,3241,3242,3243,48,49,50,51,52,53,54,3244,3245,3246,3247,55,56,57,58,59,60,3248,61,62,63,64,3249,65,66,67,68,3250,3251,69,70,71,72,3252,3253,3254,3255,3256,73,74,75,76,77,78,84,85,86,87,91,92,93,94,95,96,97,98,3257,99,100,101,102,103,104,105,106,3258,1772,1773,1774,1775,1776,1777,1778,1779,1780,1781,1782,1783,1784,1786,1787,1788,1789,1790,1791,1792,1793,1795,1796,1797,1798,1799,1800,1801,1802,1803,1804,1805,1807,1808,1809,1810,1811,1812,1813,1815,1816,1817,1818,1819,1820,1821,1822,1823,1824,1825,1827,1828,1829,1830,1831,1832,1833,1834,1836,1837,1838,1839,1840,1841,1842,1844,1845,1846,1847,1848,1849,1850,1851,1853,1854,1855,1856,1857,1858,1859,1860,1861,1863,1864,1865,1866,1867,1868,1869,1871,1872,1873,1874,1875,1876,1878,1879,1880,1881,1882,1883,1885,1886,1887,1888,1889,2741,2742,2743,2744,2745,2746,2747,2748,2749,2750,2751,2752,2753,2754,2755,2756,2757,2758,2759';
		$sql = 'SELECT a.uid
			FROM gzb_user_account a
			INNER JOIN gzb_order_main m on a.uid = m.uid
			INNER JOIN gzb_user_address addr on addr.uid= a.uid
			WHERE a.created_at <\''.$created_at.'\'
			AND addr.type= 1
			AND addr.area_id in ('.$jiang_zhe_hu_ids.')
			GROUP BY a.uid';
		$result = DB::select($sql);
		$uids = [];
		foreach($result as $v){
			$uids[$v->uid] = true;
		}
		//3290/30451
		dump(count($uids));
	}
	
	
	/**
	 * uid计数
	 * @param unknown $sql
	 * @return number
	 */
	public function countUid($sql){
		$result = DB::select($sql);
// 		$uids = [];
// //		dump('Result Count:'.count($result));
// 		foreach($result as $v){
// 			$uids[] = $v->auid;
// 		}
		//26276/30451
		
		$count = count($result);
//		dump('Uids Count:'.$count);
		//$count = count(array_flip($uids));
	//	dump('FLIP Count:'.$count);
		return $count;
	}
	
	var $created_at = '';
	public function __construct(){
		$this->created_at = '2015-07-13 23:59:59';
	}
	
	/**
	 * 生成无效用户的SQL脚本
	 */
	public function invalidUidInsertSql(){
		
		$created_at = $this->created_at;
		
		$null_ok_at = '2015-07-01 00:00:00';
		
		echo 'Before '.$created_at.'<br/>';
		$jiang_zhe_hu_ids = '26,27,28,29,30,31,32,3234,3235,3236,3237,3238,3239,33,34,37,39,41,43,47,3240,3241,3242,3243,48,49,50,51,52,53,54,3244,3245,3246,3247,55,56,57,58,59,60,3248,61,62,63,64,3249,65,66,67,68,3250,3251,69,70,71,72,3252,3253,3254,3255,3256,73,74,75,76,77,78,84,85,86,87,91,92,93,94,95,96,97,98,3257,99,100,101,102,103,104,105,106,3258,1772,1773,1774,1775,1776,1777,1778,1779,1780,1781,1782,1783,1784,1786,1787,1788,1789,1790,1791,1792,1793,1795,1796,1797,1798,1799,1800,1801,1802,1803,1804,1805,1807,1808,1809,1810,1811,1812,1813,1815,1816,1817,1818,1819,1820,1821,1822,1823,1824,1825,1827,1828,1829,1830,1831,1832,1833,1834,1836,1837,1838,1839,1840,1841,1842,1844,1845,1846,1847,1848,1849,1850,1851,1853,1854,1855,1856,1857,1858,1859,1860,1861,1863,1864,1865,1866,1867,1868,1869,1871,1872,1873,1874,1875,1876,1878,1879,1880,1881,1882,1883,1885,1886,1887,1888,1889,2741,2742,2743,2744,2745,2746,2747,2748,2749,2750,2751,2752,2753,2754,2755,2756,2757,2758,2759';
	//addr.area_id not in ($jiang_zhe_hu_ids) 
		$sql = "SELECT a.phone,a.created_at registered_at,addr.area_id,addr.address,addr.tel,i.identity
			FROM gzb_user_account a
			LEFT JOIN gzb_order_main m on a.uid = m.uid
			LEFT JOIN gzb_user_address addr on addr.uid= a.uid
			LEFT JOIN gzb_user_info i on i.uid = a.uid
			WHERE a.created_at <'$created_at'
			AND ( 
					(m.uid is null)  
					or  (
						addr.uid is not null 
						and addr.type= 1 
						and
							( 
								addr.area_id not in ($jiang_zhe_hu_ids) 
							)
						) 
					or  (
					 	addr.uid is null
					)
					or (
						addr.uid is not null 
						and addr.type= 1 
						and addr.area_id is null
					)
				)
			GROUP BY a.uid";
		$result = DB::select($sql);
		$file = 'D:\sqlLog\cheat_ol.sql';
		$fp = fopen($file,'w') or die('Faild To Open File');
		

		$create_sql =<<<EOF


DROP TABLE IF EXISTS `gzb_user_cheat`;
CREATE TABLE `gzb_user_cheat` (
   	`phone` varchar(50) DEFAULT NULL COMMENT '手机',
   	`registered_at` timestamp NULL DEFAULT NULL,			  
	`area_id` int(11) DEFAULT NULL COMMENT '所在地址',
  	`address` varchar(100) DEFAULT NULL COMMENT '详细地址',
  	`tel` varchar(20) DEFAULT NULL COMMENT '固定电话',
  	`identity` varchar(20) DEFAULT NULL COMMENT '身份证',
  PRIMARY KEY (`phone`),
  UNIQUE KEY `identity` (`identity`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户疑似骗子表';

EOF;
		fwrite($fp, $create_sql.PHP_EOL);
		foreach ($result as $key => $value) {
			$data = (array)$value;
			$data = array_filter($data);
			$insertSql = createInsertSql ( 'gzb_user_cheat', $data );
			fwrite ( $fp, $insertSql . ';' . PHP_EOL );
		}
		
		fclose ( $fp );
		dump ('Insert Sql Count: '. count ( $result ) );
		dump('File :'.$file);
	}
	public function tongji() {
		$created_at = $this->created_at;
		$jiang_zhe_hu_ids = '26,27,28,29,30,31,32,3234,3235,3236,3237,3238,3239,33,34,37,39,41,43,47,3240,3241,3242,3243,48,49,50,51,52,53,54,3244,3245,3246,3247,55,56,57,58,59,60,3248,61,62,63,64,3249,65,66,67,68,3250,3251,69,70,71,72,3252,3253,3254,3255,3256,73,74,75,76,77,78,84,85,86,87,91,92,93,94,95,96,97,98,3257,99,100,101,102,103,104,105,106,3258,1772,1773,1774,1775,1776,1777,1778,1779,1780,1781,1782,1783,1784,1786,1787,1788,1789,1790,1791,1792,1793,1795,1796,1797,1798,1799,1800,1801,1802,1803,1804,1805,1807,1808,1809,1810,1811,1812,1813,1815,1816,1817,1818,1819,1820,1821,1822,1823,1824,1825,1827,1828,1829,1830,1831,1832,1833,1834,1836,1837,1838,1839,1840,1841,1842,1844,1845,1846,1847,1848,1849,1850,1851,1853,1854,1855,1856,1857,1858,1859,1860,1861,1863,1864,1865,1866,1867,1868,1869,1871,1872,1873,1874,1875,1876,1878,1879,1880,1881,1882,1883,1885,1886,1887,1888,1889,2741,2742,2743,2744,2745,2746,2747,2748,2749,2750,2751,2752,2753,2754,2755,2756,2757,2758,2759';
		
		$all = Account::count ();
		$all_before = Account::where ( 'created_at', '<', $created_at )->count ();

		dump ( 'All : ' . $all );
		dump ( 'All before ' .$created_at.' : '.$all_before );
		
		
		$sql = 'SELECT a.uid
			FROM gzb_user_account a
			INNER JOIN gzb_order_main m on a.uid = m.uid
			INNER JOIN gzb_user_address addr on addr.uid= a.uid
			WHERE a.created_at <\''.$created_at.'\'
			AND addr.type= 1
			AND addr.area_id in ('.$jiang_zhe_hu_ids.')
			GROUP BY a.uid';
		$count = $this->countUid ( $sql );
		dump('All Valid : '.$count);
		
		
		$sql = "SELECT a.uid auid,a.phone,addr.area_id,addr.address,addr.tel,i.*
		FROM gzb_user_account a
		LEFT JOIN gzb_order_main m on a.uid = m.uid
		LEFT JOIN gzb_user_address addr on addr.uid= a.uid
		LEFT JOIN gzb_user_info i on i.uid = a.uid
		WHERE a.created_at <'$created_at'
		AND (
				(m.uid is null)
			or  (
				addr.uid is not null
				and addr.type= 1
				and addr.area_id not in ($jiang_zhe_hu_ids)
			)
			or  (
				m.uid is not null  and
				addr.uid is null
			)
			or (
				addr.uid is not null
				and addr.type= 1
				and addr.area_id is null
			)
		)
		GROUP BY a.uid";
		$count = $this->countUid ( $sql );
		dump('All Invalid : '.$count);
		
		$sql = "SELECT a.uid auid,a.phone
		FROM gzb_user_account a
		LEFT JOIN gzb_order_main m on a.uid = m.uid
		WHERE a.created_at <'$created_at'
		AND (
				m.uid is null
		)
		GROUP BY a.uid";
		$count_no_order = $this->countUid ( $sql );
		dump('All No order :'.$count_no_order);
		// 20463
		$sql = "SELECT a.uid auid,a.phone,m.orderid,addr.*
				FROM gzb_user_account a
				INNER JOIN gzb_order_main m on a.uid = m.uid
				INNER JOIN gzb_user_address addr on addr.uid= a.uid
				WHERE a.created_at <'$created_at'
				AND (
					addr.type = 1 and
					(
						addr.area_id not in ($jiang_zhe_hu_ids) 
						or addr.area_id is null
					)
				)
				GROUP BY a.uid";
		$count_outer = $this->countUid ( $sql );
		dump('All Has order Out :'.$count_outer);
		
		$sql = "SELECT a.uid auid,a.phone,m.orderid,addr.*
				FROM gzb_user_account a
				INNER JOIN gzb_order_main m on a.uid = m.uid
				LEFT JOIN gzb_user_address addr on addr.uid= a.uid
				WHERE a.created_at < '$created_at'
				AND addr.uid is null
				GROUP BY a.uid";
		$count_Has_order_no_addr = $this->countUid ( $sql );
		
		dump('All Has order No Addr :'.$count_Has_order_no_addr);
		//exit;
	}
	

	public  function __call($method,$params){
		$prefix = explode('_', $method);
		$method = substr($method, 1 + strlen($prefix[0]));
		
		if($prefix[0] == 'time' && method_exists($this, $method)){
			$t = microtime(true);
			echo '<p>Call Method :'.$method.'</p>';
			
			register_shutdown_function(function($t){
				$time =  (microtime(true) - $t);
				//dump(func_get_args());
				echo '<p>Time :'.$time.'s</p>';
			},$t);
			
			call_user_func_array(array($this,$method),$params);
			
		}else{
			throw new \Exception("Error Processing Request", 1);
		}
	}
	
	
	public static function mark($tag){
		static $marks = [];
		if($tag){
			if(isset($marks[$tag])){
				$t = $marks[$tag];
				$marks[$tag.'_TIME:'] =  (microtime(true) - $t);
			}else{
				$marks[$tag] = microtime(true);
			}
		}else{
			dump($marks);
		}
		
	} 
	
	public function getRandUid(){
		return mt_rand(10,1000000);
	}
	
	public function getRandWithId(){
		if(mt_rand(10,100000) > 50000){
			return mt_rand(10,100000);
		}
		return null;
	}
	
	public function generate_large_sql(){
		set_time_limit(30);
		$fp = fopen('D:\sqlLog\Redpacket.sql','w');
		$fp1 = fopen('D:\sqlLog\RedpacketWithdraw.sql','w');
		$arr = range(0, 1000000);
		foreach ($arr as $k => $v){
			$uid 			= $this->getRandUid();
			while( $uid == ($relation_id = $this->getRandUid()) );
			//$withdraw_num = mt_rand(0,15);
				
			$Redpacket = array (
					'uid' 			=> $uid,
					'amount' 		=> 500,
					'type' 			=> mt_rand(1,2),
					'relation_id' 	=> $relation_id,
					'status' 		=> mt_rand(0,2),
					'created_at' 	=> '2015-07-08 09:18:27',
					'withdraw_id' 	=> $this->getRandWithId(),
			);
			$sql =  createInsertSql('gzb_user_redpacket', $Redpacket);
			fwrite($fp, $sql.';'.PHP_EOL);
			$RedpacketWithdraw = array (
					'order_id' => $this->createRepBusNo(18),
					'uid' => $this->getRandUid(),
					'status' => mt_rand(0,2),
					'amount' => 500,
					'reason' => '',
					'info' => '',
					'created_at' => '2015-07-08 09:20:25',
			);
			$sql =  createInsertSql('gzb_user_redpacket_withdraw_task', $RedpacketWithdraw);
				
				
			fwrite($fp1, $sql.';'.PHP_EOL);
			//n withdraw m redpacket k nowithdraw
				
		}
		fclose($fp);
		fclose($fp1);
		echo 'OK!';
	}
	
	
	public static function CacheUid($uid,$value = ''){
		static $redis = false;
		
		if($redis === false) {
			$redis = LRedis::connection();
			$redis->flushdb();
		}
		if($value){
			$redis->set($uid,$value);
		} else{
			return $redis->get($uid)?true:false;
		}
	}
	
	public function generate_code(){
		set_time_limit(9999);
		
		for ($i = 0 ; $i < 8 ; $i ++){
			$file = 'D:\sqlLog\RedpacketCode_'.$i.'.sql';
			$did = false;
			if(file_exists($file)) {
				
				$did = true;
			}
			
			$fp = fopen($file,'w');
			
			$result = DB::select('select * from gzb_user_redpacket_code order by uid desc limit '.($i*100000).',100000');
			$ruid = [];
			
			foreach ($result as $v){
			
				if($v->uid){
					static ::CacheUid($v->uid,1);
				}else {
					if($did) continue;
					while (	static ::CacheUid($uid = mt_rand(1,1000000)) );
			
					if(mt_rand(3,10) > 1){
						$min = '010000';
						$len = 6;
					}else{
						$min = '01000';
						$len = 5;
					}
			
					while ( $min> ($code = randStr($len)) );
					$data  = array (
							'uid' 		=> $uid,
							'from_code' => $code,
							'from_uid' 	=> $this->getRandUid(),
					);
					$where = array('my_code'=>$v->my_code);
					//	RedpacketCode::where('my_code', $v->my_code )->update($data);
					$sql =  createUpdateSql('gzb_user_redpacket_code', $data,$where);
					fwrite($fp, $sql.';'.PHP_EOL);
					static ::CacheUid($uid,1);
				}
			}
			unset($result);
			fclose($fp);
			
		}
		
		echo 'OK!';
	}
	
	
	
	public function test(){
		
		
		$res =  getReturnInLogFile('logs','Return');
		edump($res);
		$function = array(
				LocalTestController::class,'index'
		);
		$function = array(
				RedpacketService::class,'common_Validate'
		);
		$function = 'dump';
		$result =  getAnnotation($function);
		edump($result);
		

		$tables		 = DB::select('show tables;');
		$db_name	 = Config::get('database.connections.mysql.database');
		$table_field = 'Tables_in_'.$db_name;
		$hasUid		 = array();
		foreach ($tables as $value){
			//Tables_in_guozhongbao
			$tablename = $value->$table_field;
			//show columns from 
			$showtable = DB::select('show create table '.$tablename);
			$showtable = (array)$showtable[0];
			if(preg_match('/`uid`\s*int/', $showtable['Create Table'])){
				$hasUid[] = $tablename;
			}
		}
		dump($hasUid);
		edumpLastSql();
		
		$res = get_defined_vars();
		
		exit;
		
		$validator = Validator::make(
				array('name' => 'Dayle'),
				array('name' => 'required|min:11')
		);
		$message = $validator->messages();
		edump($message);
		
		exit;
		
		
		unlink('tmp.txt');
// 		$account = '6236682990000454075';
// 		$res = \Ser\Lend\LendService::get_bank ( $account );
// 		edump($res);
		
		$validator = Validator::make(
				array('name' => 'Dayle'),
				array('name' => 'required|min:11')
		);
		$message = $validator->messages();
		edump($message);
		
// 		$prefix = DB::getTablePrefix();
		
		$file = file('tmp.txt');
		
		foreach ($file as $value){
			$value = trim($value);
			$sql = "DELETE FROM gzb_user_cheat where phone = '$value';".PHP_EOL;
			echo $sql;
		}
		exit;
		//6223093310001039281
	//	$this->tongji();
		$res = Fun::returnArray(200,'ds','_RPT_WITHDRAW_NO_BALANCE');
		edump($res);
		exit;
		
		$this->invalidUidInsertSql();
		
		$res = Cheat::where('registered_at','<','2015-07-01 00:00:00')
					->whereNull('address')
					->whereNull('identity')
					->delete();
		edump($res);
		exit;
		
		
	//	(new LocalTest1Controller())->run();
		//$this->time_generate_code();
		exit;
		$this->time_generate_large_sql();
		
		$this->tongji();
		
		$this->invalidUidInsertSql();
		exit;
		
		$this->time_invalidUidInsertSql();
		exit;
		$this->invalidUidInsertSql();
		$this->getAllValidOldUid();//3326/30451
		exit;
		if(!file_exists('jiang_zhe_hu_ids.php')){
			static ::outer_province(111);
		}
	}
    
    
    /**
     * 生成接口文档
     * 参数源
     */
    public function generate_document(){
    	
    	
    }
    
    
    
}















