<?php

use Ser\Redpacket\RedpacketService;
use Lib\Fun\Fun;
use Redpacket\RedpacketCode;
use Area\AreaMo;
use User\Cheat;
use User\Account;
class LocalTestController extends \BaseController
{
	/**
	 * 分割action
	 * @param unknown $action_name
	 * @return multitype:|boolean
	 */
	public function compileAction($action_name){
		if('Closure' != $action_name){
			if(strpos($action_name, '@')){
				$action = explode('@', $action_name);
				return  $action;
			}
		}
		return false;
	}
	/**
	 * 获取action指向方法的所需参数
	 * @param unknown $action
	 * @return multitype:boolean
	 */
	public function getInputParams($action){
		$codes = getFunctionDeclaration($action);
		return $this->filterParams($codes);
	}
	
	/**
	 * 判别所需参数，现以Input::get()判定
	 * @param unknown $codes
	 * @return multitype:boolean
	 */
	public function filterParams($codes){
		$params = array(); 
		if(!is_array($codes) ) return false;
		array_walk($codes, function($v,$k) use (&$params) {
			//Input::get ( 'uid' );Input::get ( 'service' )
			$r = preg_match('/Input::get\s*\(\s*[\'\"]([\w\d_]*)[\'\"]\s*\);/', $v,$matchs);
			if($r){
				$params[$matchs[1]] = true;
			}
		});
		return $params;
	}
	
	/**
	 * 获取filter内用到的参数
	 * @param unknown $filter
	 * @return multitype:boolean
	 */
	public function getFilterParams($filter){
		$codes = $this->getFilterCode($filter);
		return $this->filterParams($codes);
	}
	
	/**
	 * 获取filter的代码
	 * @param unknown $filter
	 * @return Ambigous <boolean, multitype:Ambigous >
	 */
	public function getFilterCode($filter){
// 		$app = app();
// 		$filterClosure = $app['events']->getListeners('router.filter: '.$filter);
// 		$code = getFunctionDeclaration($filterClosure[0]);
		$filters = array(
			'redpacket_switch' => 'Redpacket\RedpacketController@get_redpacket_status',
			'uid_token' => 'Redpacket\RedpacketController@verifyUserToken'
		);
		if(!isset($filters[$filter])) return false;
		$action = $this->compileAction($filters[$filter]);
		$codes = getFunctionDeclaration($action);
		return $codes;
	}
	
	public function generate_10w(){
		$num 	= 100000;
		$codes = [];
		for($i = 0 ; $i < $num ; $i ++){
			$codes[randStr(5)] = true;
		}
		 
		while($num - 1 > $count = count($codes)){
			$rest =  $num - $count;
			for($i = 0 ; $i < $rest ; $i ++){
				$codes[randStr(5)] = true;
			}
		}
		
		$now = date('Y-m-d H:i:s',time()) ;
		 
		$file = fopen('sql10w.sql','w') or die('Faild To Open File');
		 
		foreach ($codes as $k=> $v){
		
			$sql = createInsertSql('gzb_user_redpacket_code', ['my_code'=>$k,'created_at'=>$now]);
			fwrite($file, $sql.';'.PHP_EOL);
		}
		fclose($file);
		exit;
	}
	
	public function generate_100w(){
		set_time_limit(1000);
		$limit 	= 900000;
		$codes = [];
		for($i = 0 ; $i < $limit ; $i ++){
			while ('0100000' >($num = randStr(6)) );
			$codes[$num] = true;
		}
			
		while($limit > ($count = count($codes) ) ){
			$rest =  $limit - $count;
			for($i = 0 ; $i < $rest ; $i ++){
				while ('0100000' >($num = randStr(6)) );
				$codes[$num] = true;
			}
		}
		$now = date('Y-m-d H:i:s',time()) ;
			
		$file = fopen('sql100w.sql','w') or die('Faild To Open File');
			
		foreach ($codes as $k=> $v){
	
			$sql = createInsertSql('gzb_user_redpacket_code', ['my_code'=>$k,'created_at'=>$now]);
			//line($sql.';');
			fwrite($file, $sql.';'.PHP_EOL);
		}
		fclose($file);
		echo count($codes);
		exit;
	}
	
	
	public function getCode(){
		$phone = Input::get('phone');
		$redis = LRedis::connection ();
		$userInfo = array ();
		$userInfo = json_decode ( $redis->get ( $phone ), 1 );
		Fun::msg(200,'',$userInfo);
	}
	
	protected function createRepBusNo($num = 17)
	{
		list($usec, $sec) = explode(" ", microtime());
	
		$usec = (int)($usec * 10000);
	
		$str = $sec . $usec . mt_rand(100000, 999999);
	
		$str = substr($str, 0, $num);
		return $str;
	}
	
	
	public function getQueryParams($query){
		$params = explode('&', $query);
		$result = [];
		array_walk($params, function ($v,$k) use (&$result){
			$param = explode('=', $v);
			$result[$param[0]] = $param[1] ;
		});
		return $result;
	}
	
	public function getUrlQuery($reqUrl){
		$reqUrl = urldecode($reqUrl);
		if(!parse_url($reqUrl)['query']) return  '';
		$query  = parse_url($reqUrl)['query'];
		return $this->getQueryParams($query);
	}
	
	public static function outer_province($uid){
	
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
	
		edump($data);
	
		$user_company_addr = \User\Address::where('uid',$uid)
		->where('user_address.type',1)
		->where('craeted_at','<',$created_at)
		->first();
		if($user_company_addr){
				
		}
		return true;
	}
	
	
	public function validOldUser($uid){
		$created_at = '2015-07-10 00:00:00';
		$jiang_zhe_hu_ids = '26,27,28,29,30,31,32,3234,3235,3236,3237,3238,3239,33,34,37,39,41,43,47,3240,3241,3242,3243,48,49,50,51,52,53,54,3244,3245,3246,3247,55,56,57,58,59,60,3248,61,62,63,64,3249,65,66,67,68,3250,3251,69,70,71,72,3252,3253,3254,3255,3256,73,74,75,76,77,78,84,85,86,87,91,92,93,94,95,96,97,98,3257,99,100,101,102,103,104,105,106,3258,1772,1773,1774,1775,1776,1777,1778,1779,1780,1781,1782,1783,1784,1786,1787,1788,1789,1790,1791,1792,1793,1795,1796,1797,1798,1799,1800,1801,1802,1803,1804,1805,1807,1808,1809,1810,1811,1812,1813,1815,1816,1817,1818,1819,1820,1821,1822,1823,1824,1825,1827,1828,1829,1830,1831,1832,1833,1834,1836,1837,1838,1839,1840,1841,1842,1844,1845,1846,1847,1848,1849,1850,1851,1853,1854,1855,1856,1857,1858,1859,1860,1861,1863,1864,1865,1866,1867,1868,1869,1871,1872,1873,1874,1875,1876,1878,1879,1880,1881,1882,1883,1885,1886,1887,1888,1889,2741,2742,2743,2744,2745,2746,2747,2748,2749,2750,2751,2752,2753,2754,2755,2756,2757,2758,2759';
		$account = Account::where('uid',$uid)->first();
		if($account && $account->created_at < $created_at){
			$sql = 'SELECT a.uid,a.phone,m.orderid,addr.*
				FROM gzb_user_account a
				INNER JOIN gzb_order_main m on a.uid = m.uid
				INNER JOIN gzb_user_address addr on addr.uid= a.uid
				WHERE a.created_at < '.$created_at.'
				AND addr.type= 1
				AND addr.area_id in ('.$jiang_zhe_hu_ids.')
				AND a.uid = ?
				GROUP BY a.uid';
			$result = DB::select($sql,array('uid'=>$uid));
			if($result){
				return true;
			}
			return false;
		}
		return true;
	}
	
	public function test(){
		
		
		
		//static ::outer_province(111);
		
		$jian_zhe_hu_ids = include ('jiang_zhe_hu_ids.php');
		edump(count($jian_zhe_hu_ids));
		DB::delete('delete from gzb_user_cheat;');
		//
		$sql = 'SELECT a.uid as auid,a.phone,i.* FROM gzb_user_account a 
				LEFT JOIN gzb_order_main m on a.uid = m.uid
				LEFT JOIN gzb_user_info i on i.uid= a.uid
				WHERE m.orderid is NULL';
		$result = DB::select($sql);
		
		foreach ($result as $value){
			
			$data = (array)$value;
			$data['uid'] =$data['auid'];
			unset($data['auid']);
			
			$insertSql = createInsertSql('gzb_user_cheat', $data);
			DB::select($sql);
			
			
		}
		
		
		edump($result);
		edump($jian_zhe_hu_ids);
		
		
		//     	$filterClosure = app()['events']->getListeners('router.filter: uid_token');
		//     	$code = getFunctionDeclaration($filterClosure[0],true);
		//     	edump($code);
		//     	dump(( double ) microtime () * 1000000 * getmypid ());
		//     	edump(( double ) microtime () * 1000000 * getmypid ());
		//     	$code = (new RedpacketService())->get_new_exchange_code();
		 
		//     	dump($code);
		//     	exit;
		 
		//edump($codes);
		//(new RedpacketService())->get_new_exchange_code();
		
	}
	
    public function index(){
    	$routes = Route::getRoutes();
    	$routes_select = array();
    	foreach ($routes as $v){
    		$data 	 = array();
    		$method  = array();
    		$methods = $v->getMethods();
    		$uri	 = $v->getPath();
    		$action	 = $v->getActionName();
    		//获取filters
    		$filter  = $v->beforeFilters();
    		//分割action
    		$action  = $this->compileAction($action);
    		in_array('GET',$methods)  and $method[] = 'GET';
    		in_array('POST',$methods) and $method[] = 'POST';
    		//生成method和uri
    		!empty($method) and $data = array('method'=>'['.implode('/', $method).']','uri'=>'/'.ltrim($uri,'/') );
    		//获取action指向的方法内的参数
    		$data and $action and $data['params'] = $this->getInputParams($action);
    		//获取filter内部所需参数
    		if($data && $filter){
    			$params = array();
    			foreach($filter as $key => $value){
    				$p = $this->getFilterParams($key);
    				$p and $params += $p;
    			}
    			$params and $data['params'] += $params;
    		}
    		isset($data['params']) and $data['params'] = $data['params'];
    		$data and $routes_select[] = $data;
    	}
    	return View::make('localtest.index')->with('route',$routes_select);
    }
}