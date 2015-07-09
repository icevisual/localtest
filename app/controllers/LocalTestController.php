<?php

use Ser\Redpacket\RedpacketService;
use Lib\Fun\Fun;
use Redpacket\RedpacketCode;
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
			//Input::get ( 'uid' );
			$r = preg_match('/Input::get\s*\(\s*\'([\w\d_]*)\'\s*\);/', $v,$matchs);
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
	
	public function test(){
		$this->generate_100w();
	}
	
    public function index(){
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