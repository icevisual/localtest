<?php

use Ser\Redpacket\RedpacketService;
use Lib\Fun\Fun;
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
		$app = app();
		$filterClosure = $app['events']->getListeners('router.filter: '.$filter);
		$code = getFunctionDeclaration($filterClosure[0]);
		return $code;
	}
	
	
    public function index(){
//     	dump(( double ) microtime () * 1000000 * getmypid ());
//     	edump(( double ) microtime () * 1000000 * getmypid ());
//     	$code = (new RedpacketService())->get_new_exchange_code();
    	
//     	dump($code);
//     	exit;
    	
//     	$codes = [];
//     	for($i = 0 ; $i < 1000 ; $i ++){
//     		$codes[Fun::randStr(5)] = true;
//     	}
    	
//     	foreach ($codes as $k=> $v){
    		
//     		$sql = createInsertSql('gzb_user_redpacket_code', ['my_code'=>$k]);
//     		line($sql.';');
//     	}
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