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
			$r = preg_match('/Input::get\s*\(\s*[\'\"]([\w\d_]*)[\'\"]\s*(:?,\s*[\'\"]*[\d\w]*[\'\"]*)*\);/', $v,$matchs);
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
			'redpacket_switch' 	=> 'Redpacket\RedpacketController@get_redpacket_status',
			'uid_token' 		=> 'Redpacket\RedpacketController@verifyUserToken'
		);
		if(!isset($filters[$filter])) return false;
		$action = $this->compileAction($filters[$filter]);
		$codes = getFunctionDeclaration($action);
		return $codes;
	}
	
	
	
	public function generate_api_doc(){
		
		dump($_SERVER);
		exit;
		return View::make('localtest.doc');
		
		
// 		$Route = new \Illuminate\Routing\Route();
// 		$Route->getPrefix()
		
		
		$routes = Route::getRoutes();
		$returns =  getReturnInLogFile('logs','Return');
		
		$routes_select = array();
		$all_params = array();
		foreach ($routes as  $v){
			$data 	 = array();
			$method  = array();
			$methods = $v->getMethods();
			$uri	 = $v->getPath();
			$action	 = $v->getActionName();
			//获取filters
			$filter  = $v->beforeFilters();
			//分割action
			$action  = $this->compileAction($action);
			
			if(!method_exists($action[0], $action[1])){
				continue;
			}
			
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
					$p && $params += $p;
				}
				$params && $data['params']&& ( is_array($params) && $data['params'] += $params);
			}
			
			if(isset($data['params']) && !is_array($data['params'])){
				edump($data['params']);
			}
			
			isset($data['params']) && is_array($data['params']) && $all_params += $data['params'];
			
			
// 			$rrr =  getAnnotation($action);
				
// 			if(isset($returns['/'.$uri]) && $rrr ){
// 				dump($rrr);
// 				dump($returns['/'.$uri]);
			
			
// 			}
				
			
			
			$data && $routes_select[] = $data;
		}
		
		
		
		
		
		edump($routes_select);
	}
	
	
	
	
    public function index(){
    	
    	$routes = Route::getRoutes();
    	
    	$baseUrls = array(
    			'Localhost'	=>'http://'.$_SERVER['HTTP_HOST'],
    			'Test Api'	=>'http://api.gzb.renrenfenqi.com',
    			'Api'		=>'http://api.guozhongbao.com',
    	);
    	
    	$routes_select = array();
    	$all_params = array();
    	foreach ($routes as  $v){
    		$data 	 = array();
    		$method  = array();
    		$methods = $v->getMethods();
    		$uri	 = $v->getPath();
    		$action	 = $v->getActionName();
    		
    		//获取filters
    		$filter  = $v->beforeFilters();
    		//分割action
    		$action  = $this->compileAction($action);
    		
    		if(!method_exists($action[0], $action[1])){
    			continue;
    		}
    		
    		
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
    				$p && $params += $p;
    			}
    			$params && $data['params']&& ( is_array($params) && $data['params'] += $params);
    		}
    		isset($data['params']) && is_array($data['params']) && $all_params += $data['params'];
    		$data && $routes_select[] = $data;
    	}
    	return View::make('localtest.index')
    				->with('route',$routes_select)
    				->with('baseUrls',$baseUrls)
    				->with('all_params',$all_params);
    }
    
    
    /**
     * 生成接口文档
     * 参数源
     */
    public function generate_document(){
    	
    	
    }
    
    
    
}















