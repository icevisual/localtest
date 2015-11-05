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
use Lib\Fun\PointsV2;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Intervention\Image\Facades\Image;
use Ser\Sms\SmsService;
use Lib\Fun\Calculation;
class GeneralTestController extends \BaseController {
	
	

	function af($name,$sex){
		edump(get_defined_vars());
	}
	
	
	public function  a_new_function(){
		edump(\Config::get('shield.test_uid'));
		
		dump(\Ser\Lend\P2pService::get_bank('6222620140009262906'));
		
		
		exit;
		$registerApi = 'http://localhost:86/v1.3.1/redpacket/is_user_registered';
		edump(\Lib\Fun\Post::get($registerApi, ['phone' => '18767135775']));
		
		
		$registerApi = \Config::get('p2p.cgi').\Config::get('p2p.is_registered');
		$client = new \GuzzleHttp\Client();
		
		$request = $client->createRequest('GET', $registerApi, [
				'query' => ['phone' => '18767135775']
		]);
		
		$response = $client->send($request);
		$html = $response->getBody();
		
		dump($response->getBody()->getContents());
		exit;
		
		
		$res = curl_get('http://test.h5.guozhongbao.com/api/userinfo.html?uid=12&token=sadas');
		edump($res);
		
		exit;
		$infPrs = new  \Lib\Fun\InfoProcessor();
		$dta = [
				'name' => 'ad',
				'phone' => '12122112',
				'identity' => 'asdad',
				'card' => '123123131',
		];
		$res = $infPrs->data($data)->stopWhenFailed($infPrs::INTERNALBLACKLIST)
		->stopWhenFailed($infPrs::FACTORSCHECK)
		->then($infPrs::FACTORSCHECK)->prs();
		
		exit;
		$api = 'https://api.eagleeyetech.cn/v1/black/search';
		
		$data = [
				'user_app_key' => '326fc4fde748bbf382a876f0b14b637e769d74c6',
				'user_app_sn' => '5abc1d734990b6955eeb5bc119611af2',
				'eid' 	=> '110101200608017796',
				'uname' => '张晶',
				'phone' => '18766933806',
		];
		
		$res = curl_post($api, $data);
		$res = json_decode($res,1);
		dump($res);
		
		exit;
		
		ini_set('memory_limit', '2024M');
//         set_time_limit(1000);
// 		edump(Config::get ( 'user.data_img_dir' ) );
		\DB::beginTransaction();
// 		$user =  new  \User\Info();
// 		$user =  \User\Info::where('uid',10)->first();
// 		edump($user['where']);
// 		$user->at
		$user = \User\Info::find(10);
		$user->name = 'asdasda';
		$ees = $user->update();
		dumpLastSql();
		edump($ees);
		\App::environment();
		
		
		
		//setcookie('aname1', 'avalue', time() + 60 ,'/','www.ex.com') ;
		
		//dump($_COOKIE);
		
		
// 		\Lib\Fun\CFG::autoAdjustment();
// // 		dump(\Lib\Fun\CFG::CfgAutoRejectionRules('[rules]'));
// 		dump(\Lib\Fun\CFG::CfgAutoRejectionRules('open_area'));
		exit;
	}
	
	
	
	
	public function returnDate() {
		exit ( json_encode ( [ 
				'data' => 'ok' 
		] ) );
	}
	public function get_access_token() {
		$identity = \Input::get ( 'identity' );
		$secret = \Input::get ( 'secret' );
	}
	public function keyExchange() {
		$identity = \Input::get ( 'identity' );
		$secret = \Input::get ( 'secret' );
	}
	public function identityAuthentication() {
		
	}
	public function cal() {
		$data = [ 
				'a' => 8,
				'b' => 10.4,
				'c' => 12,
				'r' => 40,
				'x' => 0.5,
				's' => 4600 
		];
		
		$m = $data ['a'] + $data ['b'] + $data ['c'];
		$base = $data ['s'] * ($data ['r'] / ($data ['r'] + $m)) * $data ['x'];
		// return $base /3 + ($data['s'] - $base) * $data['a'] / $m;
		dump ( $base / 3 + ($data ['s'] - $base) * $data ['a'] / $m );
		dump ( $base / 3 + ($data ['s'] - $base) * $data ['b'] / $m );
		dump ( $base / 3 + ($data ['s'] - $base) * $data ['c'] / $m );
		exit ();
	}
	public function multy() {
		$data = [ 
				'uid' => 117215,
				'token' => 'bXP-L3OnCI',
				'name' => 'TEST',
				'identity' => '130404201401013871',
				'card' => '6013820800106129339',
				'comname' => 'asdasda',
				'comarea_id' => '26',
				'comaddress' => 'asddaas',
				'comtel' => '12312312',
				'homearea_id' => '26',
				'homeaddress' => 'sasadf' 
		];
		$url = 'http://api.guozhongbao.com/user/create_userinfo';
		
		$reqUrls = [ ];
		foreach ( range ( 1, 50 ) as $k => $v ) {
			$reqUrls [] = $url;
		}
		$res = curl_multi_request ( $reqUrls, $data );
		
		foreach ( $res as $k => $v ) {
			echo $k . '|' . $v . '<br/>';
		}
		exit ();
	}
	
	
	public function redirect(){
		$url 		= \Input::get('url');
		$method 	= \Input::get('method');
		$param 		= \Input::get('param');
		
		if($param){
			$param = json_decode($param,true);
			if(json_last_error() != JSON_ERROR_NONE){
				Fun::msg(206,'Failed to decode Params');
			}
			if( strtolower($method) == 'get'){
				$url = $url .'?'.http_build_query($param);
			}
		}else {
			$param = [];
		}
		if( strtolower($method) == 'post'){
			return curl_post($url, $param);
		}else{
			return curl_get($url);
		}
	}
	
	public function server(){

		$myPrivKeyPath = storage_path().'/m-priv-key.pem';
		$myPubKeyPath = storage_path().'/m-pub-key.pem';
		
		$clientPrivKeyPath = storage_path().'/c-priv-key.pem';
		$clientPubKeyPath = storage_path().'/c-pub-key.pem';
		
		$RsaServer = new \RsaWorker($myPrivKeyPath, $clientPubKeyPath);
		$sendData = \Input::all();
		$res = $RsaServer->receiveData($sendData);
		edump($res);
	}
	
	
	public function nostr($length = 16){
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for($i = 0; $i < $length; $i ++) {
			$str .= $chars{mt_rand ( 0, 61 )};
		}
		dump($str); ;
	}
	public function test11(){
		function _invokeMethod($class,$method,$params){
			echo __LINE__.'Invoke<br/>';
		};
		$invokeMethod = function ($class,$method,$params){
			echo __LINE__.'Invoke<br/>';
		};
		
		edump(get_defined_functions());
		
		edump(get_defined_vars());
		
		$ReflectionObject = new ReflectionFunction($invokeMethod);
		dump($ReflectionObject->getName());
		dump($ReflectionObject->getClosure());
		$ReflectionObject->invoke(1,2,3);
		$invokeMethod(1,2,3);
		edump_object_name($invokeMethod);
		
		
		edump(getInvokeMethodArray('\CommonTool','createNonceStr1'));
		call_user_func_array(getInvokeMethodArray('\CommonTool','createNonceStr'), [16]);
		
		exit;
		
		
		// $func = 'endWith';
		$func = 'createNonceStr1';
		$xAxis = range ( 0, 100 );
		$params = [];
		foreach ( $xAxis as $v ) {
			$params [] = 99999;
		}
		return statisticsExecTime ( $func, $params, $xAxis );
		
		
		
		
		$ds = PointsV2::data_source();
		
		foreach ($ds as $k1 => $v1){
			foreach ($v1 as $k2 => $v2){
				foreach ($v2 as $k3 => $v3){
					echo "\$data[$k1][$k2][$k3]\t= ";
					$v3[3] = $k1 + $v3[1] ;
					$v3[2] = $k1  ;
					$v3[4] = divPeriodPrice($v3[3],$k2); ;
					echoArray($v3);
					echo ';<br/>';
				}
			}
		}
		
		$ds = PointsV2::data_source();
		//[33.41,1.11,98.89,[100.00]];
		foreach ($ds as $k1 => $v1){
			foreach ($v1 as $k2 => $v2){
				foreach ($v2 as $k3 => $v3){
					echo "\$data[$k1][$k2][$k3]\t= ";
					$v3[3] = $k1;// + $v3[1] ;
					// 					$v3[2] = $k1  ;
					$v3[4] = divide_equally($v3[3],$k2); ;
					echoArray($v3);
					echo ';<br/>';
				}
			}
		}
		
		exit;
	}
	
	
	public function getResource(){
		
		static  $_data = [];
		
		if(empty($_data)){
			$data = \DB::select('select * From __tmp_s ');
			foreach ($data as $k => $v){
				$_data [] = $v->phone;
			}
		}
		return $_data;
	}
	
	public function process(){
		
		$phone =[
				'18767135775',
				'18767135888',
				'18767135799',
		];
		$phone = $this->getResource();
		$len = count($phone);
		
		$user_data = [
				'phone' 	=> $phone[mt_rand(0,$len - 1)],
				'password' 	=> '123456',
		];
		$base = 'http://localhost:89';
		$res = curl_post($base.'/v2.0/user/login', $user_data);
		
		$uid 	= $res['data']['account']['uid'];
		$token 	= $res['data']['token'];
		
		$order_data= [
				'uid'		=> $uid,
				'token' 	=> $token,
				'credit'	=> 5000,
				'periods'	=> 6,
				'span'		=> 30,
		];
		
		$res = curl_post($base.'/v2.0/order/put_credit', $order_data);
		
		if($res['status'] != 200){
			$order_data['status'] = '[0,1,2]';
			$res = curl_post($base.'/v2.0/order/get_orderlist', $order_data);
			if(empty($res['data'])){
				\CommonTool::log('STOP', 'Data Empty');
				return false;
			}
			
			$orderid = $res['data'][0]['orderid'];
		}else{
			$orderid =  $res['data']['orderid'] ;
		}
		
		$order = \Order\Main::where('orderid',$orderid)->first();
		if($order['status'] == 0){
			//待审核
			$info_data = array(
					"orderid" 	=> $orderid,
					"uid" 		=> $uid,
					"status" 	=> 1,
					"info" 		=> '[SYSTEM AUTO]',
					"reason" 	=> '',
					"option" 	=> 'checkList',
					"created_at" => date("Y-m-d H:i:s", time()),
					"updated_at" => date("Y-m-d H:i:s", time())
			);
			
			
			$Task = \Order\Task::where('orderid',$orderid)->first();
			if($Task){
				$task_id = $Task['id'];
			}else{
				$insertSql =  createInsertSql('gzb_order_task', $info_data);
				\DB::insert($insertSql);
				$task_id = lastInsertId();
			}
			
			$updateData = array(
					"order_task_id" => $task_id,
					'status' => 1,
			);
			
			\Order\Main::where('orderid',$orderid)->update($updateData);
		}
		
		$order = \Order\Main::where('orderid',$orderid)->first();
		if($order['status'] == 1){
			$res = curl_post($base.'/v2.0/order/task_order', ["orderid" => $orderid,'freeint'=>0]);
			if($res['status'] == 200){
				$ver ['status'] = '2';
				$ver ['pay_at'] = date('Y-m-d H:i:s', time());
				\Order\Main::where('orderid',$orderid)->update($ver);
			}
		}
		
		$order = \Order\Main::where('orderid',$orderid)->first();
		if($order['status'] == 2){
			//还款
			$sendData = [
					'uid' => $uid,
					'orderid' => $orderid,
					'type' => 1,
					'pay_at' => date('Y-m-d H:i:s'),
					'difference' => 0,
			];
			$res = curl_post($base.'/v2.0/order/repayment_hend', $sendData);
		}
		return true;
	}
	
	public function test() {
		
		
		$this->a_new_function();
		
		
		
		edump(getprotobyname('tcp'));
		$data = [
				'uid' => '123',
				'token' => 'sadsad',
		];
		ksort($data);
		$AESTool = new AESTool();
		$AESTool->setSecretKey('gzb');

		$encrypt = $AESTool->encrypt(json_encode($data));
		dump($encrypt);
		
		$decrypt = $AESTool->decrypt($encrypt);
		dump(json_decode($decrypt,1));
		exit;
		
		
		\Lib\Fun\InfoProcessor::autoGeneration();
		exit;
		
		function GetIP(){
			if(!empty($_SERVER["HTTP_CLIENT_IP"]))
				$cip = $_SERVER["HTTP_CLIENT_IP"];
			else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
				$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			else if(!empty($_SERVER["REMOTE_ADDR"]))
				$cip = $_SERVER["REMOTE_ADDR"];
			else
				$cip = "无法获取！";
			return $cip;
		}
		echo "<br>访问IP: ".GetIP()."<br>";
		echo "<br>访问来路: ".$_SERVER["HTTP_REFERER"];
		
		
		exit;
		
		
		$url = "http://wap.cmread.com/sso/oauth2/login?e_l=2&f=44012&pg=";
		$fields = array(
				'uname'=>'meiye1988',
				'passwd'=>'123123',
				'rememberUname'=>'on',
				'login'=>'登入'
		);
		
		$client = new HTTPClient();
		$html = $client->get('http://wap.cmread.com/sso/auth?e_p=1&response_type=token&e_l=2&redirect_uri=http%3A%2F%2Fwap.cmread.com%2Fr%2Ff%2Fslr&layout=2&state=succurl%253D%252Fr%252Fp%252Findex.jsp%253Ff%253D3862%2526pg%253D11%2526layout%253D2%2526vt%253D2%26faildurl%253D%252Fr%252Fp%252Findex.jsp%253Ff%253D3862%2526pg%253D11%2526layout%253D2%2526vt%253D2&fr=113&client_id=cmread-wap&e_f=0&e_c=0000&e_s=0');
		echo $html, PHP_EOL;
		$html = $client->post($url, $fields);
		echo $html, PHP_EOL;
		
		
		exit;
		
		phpinfo();
		
		exit;
		$InfoProcessor = new \Lib\Fun\InfoProcessor();
		$result =  $InfoProcessor->process('fraudmetrix', [
					'name'		=> '陈夏',
					'phone' 	=> '18767109019',
					'identity' 	=> '3303271992010966107',
			]);		
		edump($result);
		
		edump(['电风扇'=>'asd']);
		edump(strtotime('Y-m-d H:i:s',time()));
		$value = [
				'ad' => 'dd'
				
		];
		$data = serialize($value);
		dump($data);
		edump(unserialize($data));
		
		
// 		dump(\DB::select('desc gzb_order_suborder'));
		\DB::beginTransaction();
		$res = \User\LoginType::where('id','<',10)->delete();
		dump($res);
		exit;
		\DB::commit();
		
		$AESTool = new AESTool();
		
		$AESTool->setSecretKey('icevisual');
		
		
		
		$filename = '0824.txt';
		$content = file_get_contents($filename);
		$content = iconv('GBK', 'UTF-8', $content);
		$contents = $AESTool->encrypt($content);
		
		$store = '0824sql.txt';
		
		file_put_contents($store, $contents);
		
		
		$contentss = file_get_contents($store);
		
		$content = iconv('UTF-8', 'GBK', $contentss);
		
		$content = $AESTool->decrypt($content);
		
		echo($content);
		exit;
		
		
		exit();
		
		dump(\DB::select('call select_info("phone","18767135888")'));
		
		dump(\App::environment());
		edump(time());
		
		
		
		
		ignore_user_abort(true); // 后台运行
		set_time_limit(0);
		ob_end_clean();
		header("Connection: close");
		header("HTTP/1.1 200 OK");
		ob_start();#开始当前代码缓冲
		echo "running.....";
		//下面输出http的一些头信息
		$size=ob_get_length();
		header("Content-Length: $size");
		ob_end_flush();#输出当前缓冲
		flush();#输出PHP缓冲
		$restart = 0;
		for($i = 0 ; $i < 100 ; $i ++){
			if($restart > 4){
				\CommonTool::log('RESTART', 'RESTART TOO OFFTEN',true);
				exit;
			}
			
			\CommonTool::log('START', $i);
			$res = $this->process();
			\CommonTool::log('END', $i);
			
			if($res === false){
				$restart ++ ;
				$ret = system('D:\wnmp\nginx\restert.bat',$return_var);
				\CommonTool::log('RESTART', $restart,true);
				sleep(1 * $restart);
			}else{
				$restart = 0;
			}
			sleep(0.1);
		}
		//D:\wnmp\nginx\restert.bat
		exit;
		
		
		$uid = 165646;
		$messag = '姓名、身份证、银行卡与手机号码不匹配';
		\User\InfoRefund::saveOrUpdate($uid, [
				'name' 		=> 	$messag,
				'identity' 	=> 	$messag,
				'card' 		=>	$messag,
		]);
		
		exit;
		
		$str =<<<EOL
  `uid` int(11) unsigned NOT NULL COMMENT '账户ID',
  `user_type` tinyint(2) DEFAULT '0' COMMENT '用户身份，0普通用户，1蓝衣天使,2离职蓝色天使',
  `blue_area` varchar(30) DEFAULT NULL COMMENT '作为蓝色天使时的所属区域',
  `beblue_at` timestamp NULL DEFAULT NULL COMMENT '成为蓝色天使的时间',
  `phone` varchar(11) DEFAULT NULL COMMENT '注册手机号码',
  `name` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `identity` varchar(20) DEFAULT NULL COMMENT '身份证',
  `type` varchar(11) DEFAULT NULL COMMENT '提现账号银行卡类别。',
  `card` varchar(40) DEFAULT NULL COMMENT '打款账户',
  `payname` varchar(40) DEFAULT '' COMMENT '打款银行名称',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '信息是否可用 0：未验证，1：可用，2：不可用',
  `credit_status` tinyint(2) DEFAULT NULL COMMENT '账户信用状态 0 无记录，1，信誉良好，2 有过逾期 ，3 黑名单 4，同盾可疑账户 5，三要素不一致',
EOL;
		
		preg_match_all('/`([\w_]*)`/', $str,$matchs);
		
		foreach ($matchs[1] as $k => $v){
			echo  'DECLARE _'.$v.' VARCHAR(50);<br/>';
		}
		
		$sc = '';
		$_sc = '';
		$__sc = '';
		foreach ($matchs[1] as $k => $v){
			$sc .= '`'.$v.'`,';
			$_sc .= '_'.$v.',';
			$__sc .= '_'.$v.','."\n";
		}
		$sc = substr($sc, 0,strlen($sc) - 1);
		$_sc = substr($_sc, 0,strlen($_sc) - 1);
		$__sc = substr($__sc, 0,strlen($__sc) - 2);
		echo($sc.' INTO '.$_sc.'<br/>');
		echo $__sc;
		exit;
		edump($str);
		
		
		
		edump(100/3);
		$ds = PointsV2::data_source();
		//[33.41,1.11,98.89,[100.00]];
		foreach ($ds as $k1 => $v1){
			foreach ($v1 as $k2 => $v2){
				foreach ($v2 as $k3 => $v3){
					echo "\$data[$k1][$k2][$k3]\t= ";
// 					$v3[3] = $k1;// + $v3[1] ;
					// 					$v3[2] = $k1  ;
					$v3[4] = Fun::divide_equally($v3[3],$k2); ;
					echoArray($v3);
					echo ';<br/>';
				}
			}
		}
		
		
		edump(Fun::divide_equally(5582.18, 3));
		
		dump(0=="a");
		dump(intval("a"));
		dump(strval(1));
		dump($q=0=="a");
		dump($q);
		
		edump(time());
		edump(Fun::divide_equally(1000, 11));
		
		$key = 'gzb_crm_key';
		$sign = 'gzb_crm_sign';
		
		$AESTool = new AESTool();
		$AESTool->setSecretKey('guozhongbao.com');
		dump($AESTool->encrypt($key));
		
		exit;
		
		header('Content-type:text/html;charset=gbk');
		$res = system('for /f "eol= delims== tokens=1,2 usebackq" %i in (`set`) do @echo %i = %j',$return);
		dump($res);
		edump($return);
		//量变模型，本质剖析，无限进化。极限突破
		//质变模型
// 		$res = \RPGCommon::multiple_call(100000, '\RPGCommon::critHit', 10);
// 		dump($res);
// 		exit;
// 		$this->test1();
		
// 		$critRate = 0.1;
// // 		\RPGCommon::critHit($critRate);
// // 		$res = \RPGCommon::multiple_time(10000,'\RPGCommon::critHit',[$critRate]);
// 		$res = \RPGCommon::multiple_time(10000,'\RPGCommon::hitRandom',[$critRate]);
// 		edump($res);
		$dataA = [
				'HP' => 1200,
				'attack' => 67,
				'defence' => 2,//伤害减少 （装甲值 * 0.06）／（装甲值 * 0.06 ＋ 1） 
				'hit rate'	=> 90, //攻击丢失率
				'crit rate'	=> 7, //暴击率
				'dodge rate' => 1, //闪避率
				'attack speed' => 1.5, //attack 1 time after 1.5 second
		];
		
		$dataB = [
				'HP' => 1000,
				'attack' => 49,
				'defence' => 5,//伤害减少 （装甲值 * 0.06）／（装甲值 * 0.06 ＋ 1）
				'hit rate'	=> 95,
				'crit rate'	=> 5,
				'dodge rate' => 5,
				'attack speed' => 1, //attack 1 time  per second
		];
		
		$pa = new RPGPersonUnit('Jon',$dataA);
		
		$pb = new RPGPersonUnit('Tom',$dataB);
		
// 		$res = \RPGCommon::multiple_time(100,'\RPGCommon::attack',[$pa,$pb]);
		\RPGCommon::battle2($pa, $pb);
		dump($res);
// 		edump(attack($dataA,$dataB));
		
		exit;
		$content = file_get_contents('secret.ept');
		
		$AESTool = new AESTool();
		$AESTool->setSecretKey('icevisual');
		$content = $AESTool->decrypt($content);
		echo iconv('GBK', 'UTF-8', $content);
		exit;
		
		
		
		
		
		$content = file_get_contents('secret.txt');
		
		$AESTool = new AESTool();
		$AESTool->setSecretKey('icevisual');
		$content = $AESTool->encrypt($content);
		echo $content;
		file_put_contents('secret.ept', $content);
// 		echo iconv('GBK', 'UTF-8', $content);
		
		
		
		exit;
		
		
		
		
		
		
		
		$url = 'http://localhost:86/redirect';
		$reqUrl = 'http://www.baidu.com';
		$reqUrl = 'http://localhost:89/v1.3.1/redpacket/get_redpacket_config';
		$data = [
				'url' => $reqUrl,
				'method' => 'get',
				'param' => '',
		];
		echo curl_post($url, $data);
		exit;
// 		header("Content-type:text/html;charset=gbk");
// 		$UserService = new \Ser\User\UserService();
		
// 		$res = $UserService->common_Validate('6228480038654607878', '潘贤金', '330624198911295337','15201900692');
// 		dump($res);
// 		exit;
		$data = [ 
				'uid' => 117215,
				'token' => 'bXP-L3OnCI',
				'name' => 'TEST',
				'identity' => '130404201401013871',
				'card' => '6013820800106129339',
				'comname' => 'asdasda',
				'comarea_id' => '26',
				'comaddress' => 'asddaas',
				'comtel' => '12312312',
				'homearea_id' => '26',
				'homeaddress' => 'sasadf' 
		];
		
		$myPrivKeyPath = storage_path().'/m-priv-key.pem';
		$myPubKeyPath = storage_path().'/m-pub-key.pem';
		
		$clientPrivKeyPath = storage_path().'/c-priv-key.pem';
		$clientPubKeyPath = storage_path().'/c-pub-key.pem';
		
		$RsaServer = new \RsaWorker($myPrivKeyPath, $clientPubKeyPath);
		$RsaClient = new \RsaWorker($clientPrivKeyPath, $myPubKeyPath);
		
		$sendData =  $RsaServer->sendData($data);
// 		$sendData['key'] = substr($sendData['key'], 1);
		$res = $RsaClient->receiveData($sendData);
		dump($sendData);
		edump($res);
		
		exit;
		$RsaClass = new \RsaTool ( storage_path () );
		
		
		
		$ress = $RsaClass->createKey ( storage_path ().'/public.pem');
		dump ( $ress );
		$data = 'AAAAAAAAAAAAAAAAA';
		
// 		$res = $RsaClass->sign ( $data );
// 		dump ( $res );
// 		dump ( $RsaClass->verify ( $data, $res ) );
		
		// $data = sha1($data);
		dump ( $data );
		$pen_data = $RsaClass->pubEncrypt ( $data );
		dump ( $pen_data );
		$de = $RsaClass->privDecrypt ( $pen_data );
		dump ( $de );
		dump ( $data );
		$pen_data = $RsaClass->privEncrypt ( $data );
		dump ( $pen_data );
		$de = $RsaClass->pubDecrypt ( $pen_data );
		dump ( $de );
		
		exit ();
		
		// http_build_query
		// chunk_split
		// array_chunk
		edump ( $_SERVER );
		dump ( hash_algos () );
		
		exit ();
		function chunk_split_unicode($str, $l = 76, $e = "\r\n") {
			$tmp = array_chunk ( preg_split ( "//u", $str, - 1, PREG_SPLIT_NO_EMPTY ), $l );
			$str = "";
			foreach ( $tmp as $t ) {
				$str .= join ( "", $t ) . $e;
			}
			return $str;
		}
		
		$str = "asdasdadasdadadadadadas";
		echo chunk_split ( $str, 4 ) . "\n";
		echo chunk_split_unicode ( $str, 4, '<br/>' );
		
		$input_array = array (
				'a',
				'b',
				'c',
				'd',
				'e' 
		);
		print_r ( array_chunk ( $input_array, 2 ) );
		print_r ( array_chunk ( $input_array, 2, true ) );
		
		exit ();
		
		$data = '山东科技将sd,.<>\\////????fsdf1243123123山东';
		$AESMcrypt = new \AESTool ();
		
		$randKey = \CommonTool::createNonceStr ( 32 );
		
		dump ( $randKey );
		$AESMcrypt->setSecretKey ( 'ads' );
		$res = $AESMcrypt->encrypt ( $data );
		$res = $AESMcrypt->decrypt ( $res );
		
		dump ( $res );
		
		exit ();
		dump ( $res );
		
		exit ();
		
		$RsaClass = new \RsaTool ( storage_path () . '/A' );
		// $RsaClass->createKey();
		
		$data = 'AAAAAAAAAAAAAAAAA';
		// $data = sha1($data);
		dump ( $data );
		$pen_data = $RsaClass->pubEncrypt ( $data );
		dump ( $pen_data );
		$de = $RsaClass->privDecrypt ( $pen_data );
		dump ( $de );
		dump ( $data );
		$pen_data = $RsaClass->privEncrypt ( $data );
		dump ( $pen_data );
		$de = $RsaClass->pubDecrypt ( $pen_data );
		dump ( $de );
		
		exit ();
		$r = openssl_pkey_new ( array (
				'private_key_bits' => 1024,
				'private_key_type' => OPENSSL_KEYTYPE_RSA 
		) );
		openssl_pkey_export ( $r, $privKey );
		$rp = openssl_pkey_get_details ( $r );
		var_dump ( $rp );
		echo $privKey;
		// $this->one();
		exit ();
		
		$OrderService = new Ser\VersionTwo\Order\OrderService ();
		// $res = $OrderService ->getPayListAllUser();
		$uid = '165682';
		$res = $OrderService->withholding_binding_after_pay ( $uid );
		// 165682
		dump ( $res );
		exit ();
		$uid = 165682;
		// 2016-03-09 00:00:00
		// 2016-02-08 00:00:00
		// 2016-01-09 00:00:00
		// 2015-12-10 00:00:00
		// 2015-11-10 00:00:00
		// 2015-10-11 00:00:00
		// $res = \Order\Pay::getLastPay($uid,'2015-11-10');
		$res = \Order\Pay::getPayListAllUser ( date ( 'Y-m-d' ), 0 );
		edump ( $res );
		exit ();
		
		$uid = '165705';
		$marketingService = new \Ser\VersionTwo\Marketing\MarketingService ();
		$res = $marketingService->get_wallet_info ( $uid );
		dump ( $res );
		exit ();
		
		$uid = '165682';
		$orderid = '1441872092080051';
		$type = '1';
		$pay_at = '2015-09-10 00:00:00';
		$config = [ 
				1 => [ 
						\Ser\Order\OrderService::class,
						'show_pay_task' 
				],
				2 => [ 
						\Ser\VersionTwo\Order\OrderService::class,
						'show_pay_task_v2_0' 
				] 
		];
		$order = \Order\Main::where ( 'orderid', $orderid )->first ();
		return call_user_func_array ( [ 
				new $config [$order ['order_version']] [0] (),
				$config [$order ['order_version']] [1] 
		], [ 
				$uid,
				$orderid,
				$type,
				$pay_at 
		] );
		
		exit ();
		// 165682 1441852794861894 144185453176846
		$data = [ 
				'adssd' => '123',
				'asd' => 'asd',
				'asds' => '123' 
		];
		
		edump ( serialize ( $data ) );
		$OrderService = new \Ser\VersionTwo\Order\OrderService ();
		$OrderService->repayment ( 'asd' );
		exit ();
		$uid = '165682';
		$orderid = '1441852794861894';
		$paytaskid = '144185453176846';
		\Marketing\RepaymentCash::doDeductibleSuccess ( $uid, $orderid, $paytaskid );
		exit ();
		\DB::beginTransaction ();
		$res = '';
		try {
			$uid = '165682';
			$gain_type = '2';
			$cash_type = '1';
			// $res = \Marketing\RepaymentCoupon::gain(165682, 1, [2000]);
			$res = \Marketing\RepaymentCash::gain ( $uid, $gain_type, $cash_type );
			$rrr = \Marketing\RepaymentCash::get ()->toArray ();
			dump ( $rrr );
		} catch ( \Exception $e ) {
			dump ( $e->getMessage () );
		}
		exit ();
		\DB::commit ();
		dump ( $res );
		exit ();
		$type = 1;
		$res = PointsV2::get_cash_by_type ( $type );
		$res = PointsV2::get_coupon_by_amount ( 2000 );
		
		edump ( $res );
		
		$data = [ 
				'uid' => 476,
				'token' => 'ujQG8n50R8',
				'credit' => 1000,
				'periods' => 1 
		];
		$res = curl_multi_request ( [ 
				'http://api.gzb.renrenfenqi.com/order/put_credit',
				'http://api.gzb.renrenfenqi.com/order/put_credit',
				'http://api.gzb.renrenfenqi.com/order/put_credit',
				'http://api.gzb.renrenfenqi.com/order/put_credit',
				'http://api.gzb.renrenfenqi.com/order/put_credit',
				'http://api.gzb.renrenfenqi.com/order/put_credit',
				'http://api.gzb.renrenfenqi.com/order/put_credit',
				'http://api.gzb.renrenfenqi.com/order/put_credit',
				'http://api.gzb.renrenfenqi.com/order/put_credit',
				'http://api.gzb.renrenfenqi.com/order/put_credit',
				'http://api.gzb.renrenfenqi.com/order/put_credit',
				'http://api.gzb.renrenfenqi.com/order/put_credit',
				'http://api.gzb.renrenfenqi.com/order/put_credit',
				'http://api.gzb.renrenfenqi.com/order/put_credit',
				'http://api.gzb.renrenfenqi.com/order/put_credit' 
		], $data );
		
		foreach ( $res as $v ) {
			echo $v . '<br/>';
		}
		exit ();
		$data = [ 
				'uid' => 476,
				'token' => 'ujQG8n50R8',
				'credit' => 1000,
				'periods' => 1 
		];
		
		for($i = 0; $i < 3; $i ++) {
			$result [] = \Lib\Fun\Post::post ( 'http://api.gzb.renrenfenqi.com/order/put_credit', $data );
		}
		dump ( $result );
		exit ();
		
		$file = base_path () . '\public\eye.jpg';
		try {
			$res = Fun::thumb ( $file, 216, '', true );
			dump ( $res );
		} catch ( \Exception $e ) {
			dump ( $e->getMessage () );
		}
		
		exit ();
		
		// $this->four();
		// $this->one();
		
		exit ();
		$typeArray = array (
				10 => 0,
				1,
				2,
				3 
		) + range ( 30, 38 );
		
		edump ( $typeArray );
		$str = '{\"uid\":165646,\"nickname\":null,\"name\":\"刘能飞\",\"sex\":0,\"age\":0,\"qq\":null,\"wechat\":null,\"email\":null,\"emergency_contact\":null,\"company\":\"韵达快递公司\",\"join_at\":null,\"identity\":\"513002198810120218\",\"created_at\":\"2015-08-24 10:00:55\",\"updated_at\":\"2015-08-24 10:03:25\",\"process_at\":\"2015-08-24 10:03:25\",\"audit_status\":3,\"credit_status\":3,\"fraudmetrix_status\":0,\"credit_status_info\":\"0\",\"address\":[{\"id\":53657,\"uid\":165646,\"area_id\":2756,\"address\":\"上海 上海市 青浦区 上海市青浦区徐径镇华徐公路508号\",\"tel\":\"15088789827\",\"type\":1,\"created_at\":\"2015-08-24 10:00:55\",\"updated_at\":\"2015-08-24 10:00:55\"},{\"id\":53658,\"uid\":165646,\"area_id\":2756,\"address\":\"上海 上海市 青浦区 上海市青浦区徐径镇华徐公公路508号\",\"tel\":null,\"type\":0,\"created_at\":\"2015-08-24 10:00:55\",\"updated_at\":\"2015-08-24 10:00:55\"}],\"payway\":[{\"id\":27877,\"uid\":165646,\"card\":\"6013820800106129339\",\"payname\":\"中国银行-长城电子借记卡-借记卡\",\"payway\":1,\"created_at\":\"2015-08-24 10:00:55\",\"updated_at\":\"2015-08-24 10:00:55\"}],\"data\":[{\"picurl\":\"http://localhost:91/datum/165646/165646_1440381470545426.jpg\",\"type\":0,\"info\":null},{\"picurl\":\"http://localhost:91/datum/165646/165646_1440381494583929.jpg\",\"type\":1,\"info\":null},{\"picurl\":\"http://localhost:91/datum/165646/165646_1440381498395195.jpg\",\"type\":2,\"info\":null},{\"picurl\":\"http://localhost:91/datum/165646/165646_1440381543073887.jpg\",\"type\":3,\"info\":null}],\"info_refund\":{\"id\":2010,\"uid\":165646,\"name\":\"姓名错误1430\",\"company\":null,\"identity\":\"身份证错误1 1\",\"card\":\"数字问题 1\",\"area_id\":\"省市区错误test\",\"comaddress\":\"省市区错误test\",\"comtel\":null,\"homeaddress\":null,\"hometel\":null,\"pic\":null,\"created_at\":\"2015-08-31 16:15:21\",\"updated_at\":\"2015-08-31 16:40:29\"}}';
		edump ( json_decode ( stripcslashes ( $str ), true ) );
		$res = json_decode_recursive ();
		edump ( $res );
		
		$RedpacketService = new \Ser\V1_3_1\Redpacket\RedpacketService ();
		$res = $RedpacketService->verity_process ( 341 );
		edump ( $res );
		exit ();
		// open an image file
		$file = base_path () . '\public\foo.jpg';
		
		$res = Fun::thumb ( $file, 1920 );
		dump ( $res );
		exit ();
		$pathinfo = pathinfo ( $file );
		$path = $pathinfo ['dirname'] . DIRECTORY_SEPARATOR . $pathinfo ['filename'] . '.' . $pathinfo ['extension'];
		edump ( $path );
		
		$img = \Image::make ( $file );
		
		$width = $img->width ();
		$height = $img->height ();
		$max = 800;
		dump ( $width );
		dump ( $height );
		if ($width > $height) {
			$height = intval ( $height * $max / $width );
			$width = $max;
		} else {
			$width = intval ( $width * $max / $height );
			$height = $max;
		}
		
		dump ( $width );
		dump ( $height );
		
		// now you are able to resize the instance
		$img->resize ( $width, $height );
		// and insert a watermark for example
		// $img->insert('public/watermark.png');
		
		// finally we save the image as a new file
		$img->save ( base_path () . '/public/bar.jpg' );
		
		// header('Cache-Control: private, max-age=0, no-store, no-cache, must-revalidate');
		// header('Cache-Control: post-check=0, pre-check=0', false);
		// header('Pragma: no-cache');
		// // header("content-type: image/jpg");
		// header("Content-Type: image/jpeg;text/html; charset=utf-8");
		// echo file_get_contents(base_path().'/public/bar.jpg',true);
		exit ();
		
		edump ( storage_path () );
		$filename = 'D:\wnmp\www\gzb_master\app\12.jpg';
		// $filename = 'D:\wnmp\www\gzb_master\app\helper.php';
		// $res = Fun::file_type($filename);
		// edump($res);
	}
	public function process_three_factor() {
		set_time_limit ( 0 );
		$allData = \DB::table ( 'three_factor' )->where ( 'run', 0 )->get ();
		$userSerivce = new \Ser\User\UserService ();
		$exec_count = 0;
		foreach ( $allData as $value ) {
			$exec_count ++;
			$value = ( array ) $value;
			$bank_account = $value ['card'];
			$name = $value ['name'];
			$identity = $value ['identity'];
			$result = $userSerivce->common_Validate ( $bank_account, $name, $identity );
			$updateData = [ 
					'run' => 1,
					'return' => $result ['return'],
					'message' => isset ( $result ['data'] ) ? $result ['data'] : '' 
			];
			\DB::table ( 'three_factor' )->where ( 'id', $value ['id'] )->update ( $updateData );
			sleep ( 0.1 );
		}
		echo 'Exec Count :' . $exec_count;
		exit ();
	}
	public function getCode() {
		$phone = Input::get ( 'phone' );
		$redis = LRedis::connection ();
		$userInfo = array ();
		$userInfo = json_decode ( $redis->get ( $phone ), 1 );
		Fun::msg ( 200, '', $userInfo );
	}
	
	/**
	 * 生成订单号
	 * 
	 * @param number $num        	
	 * @return string
	 */
	protected function createRepBusNo($num = 17) {
		list ( $usec, $sec ) = explode ( " ", microtime () );
		$usec = ( int ) ($usec * 10000);
		$str = $sec . $usec . mt_rand ( 100000, 999999 );
		$str = substr ( $str, 0, $num );
		return $str;
	}
	
	/**
	 * 分割Query
	 * 
	 * @param unknown $query        	
	 * @return multitype:Ambigous <>
	 */
	public function getQueryParams($query) {
		$params = explode ( '&', $query );
		$result = [ ];
		array_walk ( $params, function ($v, $k) use(&$result) {
			$param = explode ( '=', $v );
			$result [$param [0]] = $param [1];
		} );
		return $result;
	}
	
	/**
	 * 获取URL的query
	 * 
	 * @param unknown $reqUrl        	
	 * @return string|multitype:Ambigous
	 */
	public function getUrlQuery($reqUrl) {
		$reqUrl = urldecode ( $reqUrl );
		if (! parse_url ( $reqUrl )['query'])
			return '';
		$query = parse_url ( $reqUrl )['query'];
		return $this->getQueryParams ( $query );
	}
	
	/**
	 * 生成江浙沪的所有区ID
	 * 
	 * @param unknown $uid        	
	 * @return boolean
	 */
	public static function outer_province($uid) {
		echo 'Generate outer_province_ids...<br/>';
		$created_at = '2014-07-10 00:00:00';
		$provices_id = [ 
				'5',
				'2739',
				'1770' 
		]; // 浙江、广东、上海、江苏
		$city_id = [ ];
		$country_id = [ ];
		$areas_p = AreaMo::whereIn ( 'cid', $provices_id )->get ()->toArray ();
		foreach ( $areas_p as $value ) {
			$temp = AreaMo::where ( 'cup', $value ['cid'] )->get ()->toArray ();
			foreach ( $temp as $v ) {
				$city_id [] = $v ['cid'];
			}
		}
		$areas_c = AreaMo::whereIn ( 'cid', $city_id )->get ()->toArray ();
		foreach ( $areas_c as $value ) {
			$temp = AreaMo::where ( 'cup', $value ['cid'] )->get ()->toArray ();
			foreach ( $temp as $v ) {
				$country_id [] = $v ['cid'];
			}
		}
		
		$data = [ 
				'provices_id' => implode ( ',', $provices_id ),
				'city_id' => implode ( ',', $city_id ),
				'country_id' => implode ( ',', $country_id ) 
		];
		$jiang_zhe_hu_ids = $data ['provices_id'] . ',' . $data ['city_id'] . ',' . $data ['country_id'];
		$jiang_zhe_hu_ids = $data ['country_id'];
		$content = <<<EOF
<?php
	
	return [
		$jiang_zhe_hu_ids
];
	
EOF;
		file_put_contents ( 'jiang_zhe_hu_ids.php', $content );
		
		return count ( $data );
	}
	
	/**
	 * 获取江浙沪内有订单才OK的人数
	 */
	public function getAllValidOldUid() {
		$created_at = '2015-07-11 00:00:00';
		$jiang_zhe_hu_ids = '26,27,28,29,30,31,32,3234,3235,3236,3237,3238,3239,33,34,37,39,41,43,47,3240,3241,3242,3243,48,49,50,51,52,53,54,3244,3245,3246,3247,55,56,57,58,59,60,3248,61,62,63,64,3249,65,66,67,68,3250,3251,69,70,71,72,3252,3253,3254,3255,3256,73,74,75,76,77,78,84,85,86,87,91,92,93,94,95,96,97,98,3257,99,100,101,102,103,104,105,106,3258,1772,1773,1774,1775,1776,1777,1778,1779,1780,1781,1782,1783,1784,1786,1787,1788,1789,1790,1791,1792,1793,1795,1796,1797,1798,1799,1800,1801,1802,1803,1804,1805,1807,1808,1809,1810,1811,1812,1813,1815,1816,1817,1818,1819,1820,1821,1822,1823,1824,1825,1827,1828,1829,1830,1831,1832,1833,1834,1836,1837,1838,1839,1840,1841,1842,1844,1845,1846,1847,1848,1849,1850,1851,1853,1854,1855,1856,1857,1858,1859,1860,1861,1863,1864,1865,1866,1867,1868,1869,1871,1872,1873,1874,1875,1876,1878,1879,1880,1881,1882,1883,1885,1886,1887,1888,1889,2741,2742,2743,2744,2745,2746,2747,2748,2749,2750,2751,2752,2753,2754,2755,2756,2757,2758,2759';
		$sql = 'SELECT a.uid
			FROM gzb_user_account a
			INNER JOIN gzb_order_main m on a.uid = m.uid
			INNER JOIN gzb_user_address addr on addr.uid= a.uid
			WHERE a.created_at <\'' . $created_at . '\'
			AND addr.type= 1
			AND addr.area_id in (' . $jiang_zhe_hu_ids . ')
			GROUP BY a.uid';
		$result = DB::select ( $sql );
		$uids = [ ];
		foreach ( $result as $v ) {
			$uids [$v->uid] = true;
		}
		// 3290/30451
		dump ( count ( $uids ) );
	}
	
	/**
	 * uid计数
	 * 
	 * @param unknown $sql        	
	 * @return number
	 */
	public function countUid($sql) {
		$result = DB::select ( $sql );
		// $uids = [];
		// // dump('Result Count:'.count($result));
		// foreach($result as $v){
		// $uids[] = $v->auid;
		// }
		// 26276/30451
		
		$count = count ( $result );
		// dump('Uids Count:'.$count);
		// $count = count(array_flip($uids));
		// dump('FLIP Count:'.$count);
		return $count;
	}
	var $created_at = '';
	public function __construct() {
		$this->created_at = '2015-07-13 23:59:59';
	}
	
	/**
	 * 生成无效用户的SQL脚本
	 */
	public function invalidUidInsertSql() {
		$created_at = $this->created_at;
		
		$null_ok_at = '2015-07-01 00:00:00';
		
		echo 'Before ' . $created_at . '<br/>';
		$jiang_zhe_hu_ids = '26,27,28,29,30,31,32,3234,3235,3236,3237,3238,3239,33,34,37,39,41,43,47,3240,3241,3242,3243,48,49,50,51,52,53,54,3244,3245,3246,3247,55,56,57,58,59,60,3248,61,62,63,64,3249,65,66,67,68,3250,3251,69,70,71,72,3252,3253,3254,3255,3256,73,74,75,76,77,78,84,85,86,87,91,92,93,94,95,96,97,98,3257,99,100,101,102,103,104,105,106,3258,1772,1773,1774,1775,1776,1777,1778,1779,1780,1781,1782,1783,1784,1786,1787,1788,1789,1790,1791,1792,1793,1795,1796,1797,1798,1799,1800,1801,1802,1803,1804,1805,1807,1808,1809,1810,1811,1812,1813,1815,1816,1817,1818,1819,1820,1821,1822,1823,1824,1825,1827,1828,1829,1830,1831,1832,1833,1834,1836,1837,1838,1839,1840,1841,1842,1844,1845,1846,1847,1848,1849,1850,1851,1853,1854,1855,1856,1857,1858,1859,1860,1861,1863,1864,1865,1866,1867,1868,1869,1871,1872,1873,1874,1875,1876,1878,1879,1880,1881,1882,1883,1885,1886,1887,1888,1889,2741,2742,2743,2744,2745,2746,2747,2748,2749,2750,2751,2752,2753,2754,2755,2756,2757,2758,2759';
		// addr.area_id not in ($jiang_zhe_hu_ids)
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
		$result = DB::select ( $sql );
		$file = 'D:\sqlLog\cheat_ol.sql';
		$fp = fopen ( $file, 'w' ) or die ( 'Faild To Open File' );
		
		$create_sql = <<<EOF


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
		fwrite ( $fp, $create_sql . PHP_EOL );
		foreach ( $result as $key => $value ) {
			$data = ( array ) $value;
			$data = array_filter ( $data );
			$insertSql = createInsertSql ( 'gzb_user_cheat', $data );
			fwrite ( $fp, $insertSql . ';' . PHP_EOL );
		}
		
		fclose ( $fp );
		dump ( 'Insert Sql Count: ' . count ( $result ) );
		dump ( 'File :' . $file );
	}
	public function tongji() {
		$created_at = $this->created_at;
		$jiang_zhe_hu_ids = '26,27,28,29,30,31,32,3234,3235,3236,3237,3238,3239,33,34,37,39,41,43,47,3240,3241,3242,3243,48,49,50,51,52,53,54,3244,3245,3246,3247,55,56,57,58,59,60,3248,61,62,63,64,3249,65,66,67,68,3250,3251,69,70,71,72,3252,3253,3254,3255,3256,73,74,75,76,77,78,84,85,86,87,91,92,93,94,95,96,97,98,3257,99,100,101,102,103,104,105,106,3258,1772,1773,1774,1775,1776,1777,1778,1779,1780,1781,1782,1783,1784,1786,1787,1788,1789,1790,1791,1792,1793,1795,1796,1797,1798,1799,1800,1801,1802,1803,1804,1805,1807,1808,1809,1810,1811,1812,1813,1815,1816,1817,1818,1819,1820,1821,1822,1823,1824,1825,1827,1828,1829,1830,1831,1832,1833,1834,1836,1837,1838,1839,1840,1841,1842,1844,1845,1846,1847,1848,1849,1850,1851,1853,1854,1855,1856,1857,1858,1859,1860,1861,1863,1864,1865,1866,1867,1868,1869,1871,1872,1873,1874,1875,1876,1878,1879,1880,1881,1882,1883,1885,1886,1887,1888,1889,2741,2742,2743,2744,2745,2746,2747,2748,2749,2750,2751,2752,2753,2754,2755,2756,2757,2758,2759';
		
		$all = Account::count ();
		$all_before = Account::where ( 'created_at', '<', $created_at )->count ();
		
		dump ( 'All : ' . $all );
		dump ( 'All before ' . $created_at . ' : ' . $all_before );
		
		$sql = 'SELECT a.uid
			FROM gzb_user_account a
			INNER JOIN gzb_order_main m on a.uid = m.uid
			INNER JOIN gzb_user_address addr on addr.uid= a.uid
			WHERE a.created_at <\'' . $created_at . '\'
			AND addr.type= 1
			AND addr.area_id in (' . $jiang_zhe_hu_ids . ')
			GROUP BY a.uid';
		$count = $this->countUid ( $sql );
		dump ( 'All Valid : ' . $count );
		
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
		dump ( 'All Invalid : ' . $count );
		
		$sql = "SELECT a.uid auid,a.phone
		FROM gzb_user_account a
		LEFT JOIN gzb_order_main m on a.uid = m.uid
		WHERE a.created_at <'$created_at'
		AND (
				m.uid is null
		)
		GROUP BY a.uid";
		$count_no_order = $this->countUid ( $sql );
		dump ( 'All No order :' . $count_no_order );
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
		dump ( 'All Has order Out :' . $count_outer );
		
		$sql = "SELECT a.uid auid,a.phone,m.orderid,addr.*
				FROM gzb_user_account a
				INNER JOIN gzb_order_main m on a.uid = m.uid
				LEFT JOIN gzb_user_address addr on addr.uid= a.uid
				WHERE a.created_at < '$created_at'
				AND addr.uid is null
				GROUP BY a.uid";
		$count_Has_order_no_addr = $this->countUid ( $sql );
		
		dump ( 'All Has order No Addr :' . $count_Has_order_no_addr );
		// exit;
	}
	public function __call($method, $params) {
		$prefix = explode ( '_', $method );
		$method = substr ( $method, 1 + strlen ( $prefix [0] ) );
		
		if ($prefix [0] == 'time' && method_exists ( $this, $method )) {
			$t = microtime ( true );
			echo '<p>Call Method :' . $method . '</p>';
			
			register_shutdown_function ( function ($t) {
				$time = (microtime ( true ) - $t);
				// dump(func_get_args());
				echo '<p>Time :' . $time . 's</p>';
			}, $t );
			
			call_user_func_array ( array (
					$this,
					$method 
			), $params );
		} else {
			throw new \Exception ( "Error Processing Request", 1 );
		}
	}
	public function getRandUid() {
		return mt_rand ( 10, 1000000 );
	}
	public function getRandWithId() {
		if (mt_rand ( 10, 100000 ) > 50000) {
			return mt_rand ( 10, 100000 );
		}
		return null;
	}
	public function generate_large_sql() {
		set_time_limit ( 30 );
		$fp = fopen ( 'D:\sqlLog\Redpacket.sql', 'w' );
		$fp1 = fopen ( 'D:\sqlLog\RedpacketWithdraw.sql', 'w' );
		$arr = range ( 0, 1000000 );
		foreach ( $arr as $k => $v ) {
			$uid = $this->getRandUid ();
			while ( $uid == ($relation_id = $this->getRandUid ()) )
				;
				// $withdraw_num = mt_rand(0,15);
			
			$Redpacket = array (
					'uid' => $uid,
					'amount' => 500,
					'type' => mt_rand ( 1, 2 ),
					'relation_id' => $relation_id,
					'status' => mt_rand ( 0, 2 ),
					'created_at' => '2015-07-08 09:18:27',
					'withdraw_id' => $this->getRandWithId () 
			);
			$sql = createInsertSql ( 'gzb_user_redpacket', $Redpacket );
			fwrite ( $fp, $sql . ';' . PHP_EOL );
			$RedpacketWithdraw = array (
					'order_id' => $this->createRepBusNo ( 18 ),
					'uid' => $this->getRandUid (),
					'status' => mt_rand ( 0, 2 ),
					'amount' => 500,
					'reason' => '',
					'info' => '',
					'created_at' => '2015-07-08 09:20:25' 
			);
			$sql = createInsertSql ( 'gzb_user_redpacket_withdraw_task', $RedpacketWithdraw );
			
			fwrite ( $fp1, $sql . ';' . PHP_EOL );
			// n withdraw m redpacket k nowithdraw
		}
		fclose ( $fp );
		fclose ( $fp1 );
		echo 'OK!';
	}
	public static function CacheUid($uid, $value = '') {
		static $redis = false;
		
		if ($redis === false) {
			$redis = LRedis::connection ();
			$redis->flushdb ();
		}
		if ($value) {
			$redis->set ( $uid, $value );
		} else {
			return $redis->get ( $uid ) ? true : false;
		}
	}
	public function generate_code() {
		set_time_limit ( 9999 );
		
		for($i = 0; $i < 8; $i ++) {
			$file = 'D:\sqlLog\RedpacketCode_' . $i . '.sql';
			$did = false;
			if (file_exists ( $file )) {
				
				$did = true;
			}
			
			$fp = fopen ( $file, 'w' );
			
			$result = DB::select ( 'select * from gzb_user_redpacket_code order by uid desc limit ' . ($i * 100000) . ',100000' );
			$ruid = [ ];
			
			foreach ( $result as $v ) {
				
				if ($v->uid) {
					static::CacheUid ( $v->uid, 1 );
				} else {
					if ($did)
						continue;
					while ( static::CacheUid ( $uid = mt_rand ( 1, 1000000 ) ) )
						;
					
					if (mt_rand ( 3, 10 ) > 1) {
						$min = '010000';
						$len = 6;
					} else {
						$min = '01000';
						$len = 5;
					}
					
					while ( $min > ($code = randStr ( $len )) )
						;
					$data = array (
							'uid' => $uid,
							'from_code' => $code,
							'from_uid' => $this->getRandUid () 
					);
					$where = array (
							'my_code' => $v->my_code 
					);
					// RedpacketCode::where('my_code', $v->my_code )->update($data);
					$sql = createUpdateSql ( 'gzb_user_redpacket_code', $data, $where );
					fwrite ( $fp, $sql . ';' . PHP_EOL );
					static::CacheUid ( $uid, 1 );
				}
			}
			unset ( $result );
			fclose ( $fp );
		}
		
		echo 'OK!';
	}
	public function test1() {
		$needles = '123123123123';
		$haystack = '123123';
		$res = endsWith ( $haystack, $needles );
		edump ( $res );
		// $func = 'endWith';
		$func = 'endsWiths';
		$xAxis = range ( 0, 100 );
		$params = [ ];
		foreach ( $xAxis as $v ) {
			$params [] = [ 
					$haystack,
					$needles 
			];
		}
		return statisticsExecTime ( $func, $params, $xAxis );
		return chart ( [ 
				'categories' => $xAxis 
		], $data );
		$res = endsWiths ( $haystack, $needles );
		dump ( $res );
		
		$res = \Str::endsWith ( $haystack, $needles );
		edump ( $res );
		$a = [ 
				'a' => '2',
				'b' => '1b',
				'c' => '1d' 
		];
		
		$b = [ 
				'a' => '2a',
				'b' => '2b',
				'd' => '2e' 
		];
		$b [] = $b;
		$a [] = $b;
		dump ( $a );
		
		dump ( json_decode ( json_encode ( [ 
				json_encode ( $a ) 
		] ) ), true );
		exit ();
		
		$c = array_map_recursive ( function ($v) {
			return '--' . $v;
		}, $a );
		dump ( $c );
		exit ();
		
		array_walk_recursive ( $a, function ($v, $k) {
			
			echo $v;
		} );
		
		exit ();
		
		$c = array_map ( function ($v) {
			if (is_string ( $v ))
				return $v . '--';
		}, $a );
		edump ( $c );
		
		edump ( $a + $b );
		
		exit ();
		S ( [ 
				123 
		] );
		
		$RedpacketService = new Ser\V1_3_1\Redpacket\RedpacketService ();
		$RedpacketService->_insert_config ();
		// $res = $RedpacketService->is_from_open_area(17);
		
		// edump($res);
		exit ();
		
		$rsv = array (
				'江苏南京' => '025',
				'江苏无锡' => '0510',
				'江苏苏州' => '0512',
				'江苏常州' => '0519',
				'浙江杭州' => '0571',
				'浙江宁波' => '0574',
				'浙江金华' => '0579',
				'浙江温州' => '0577',
				'上海上海' => '021',
				'安徽合肥' => '0551',
				'广东广州' => '020',
				'广东深圳' => '0755',
				'广东东莞' => '0769',
				'广东中山' => '0760',
				'广东佛山' => '0757' 
		);
		// 江苏南京、江苏无锡、江苏苏州、江苏常州、浙江杭州、浙江宁波、浙江金华、浙江温州、上海上海、安徽合肥、广东广州、广东深圳、广东东莞、广东中山、广东佛山
		$sh = [ 
				'江苏南京',
				'江苏无锡',
				'江苏苏州',
				'江苏常州',
				'浙江杭州',
				'浙江宁波',
				'浙江金华',
				'浙江温州',
				'上海上海',
				'安徽合肥',
				'广东广州',
				'广东深圳',
				'广东东莞',
				'广东中山',
				'广东佛山' 
		];
		dump ( count ( $sh ) );
		$result = [ ];
		foreach ( $sh as $v ) {
			$str1 = mb_substr ( $v, 0, 2 );
			$str2 = mb_substr ( $v, 2 );
			
			$res = Account::select ( 'areacode' )->where ( 'province', $str1 )->where ( 'city', $str2 )->first ()->toArray ();
			$result [$v] = $res ['areacode'];
		}
		var_export ( $result );
		exit ();
		// Account::
		
		$res = file_get_contents ( 'http://www.ip138.com/post/search.asp?area=%C8%FD%C3%F7%CA%D0&action=area2zone' );
		edump ( $res );
		$res = curl_post ( 'http://www.ip138.com/post/search.asp', [ 
				'area' => '三明市',
				'action' => 'area2zone' 
		] );
		edump ( $res );
		//
		
		$res = curl_get ( 'http://www.ip138.com/post/search.asp?area=三明市&action=area2zone' );
		edump ( $res );
		// open an image file
		$img = \Image::make ( base_path () . '/public/foo.jpg' );
		
		dump ( $img->width () );
		dump ( $img->height () );
		
		// now you are able to resize the instance
		$img->resize ( 10, 240 );
		// and insert a watermark for example
		// $img->insert('public/watermark.png');
		
		// finally we save the image as a new file
		$img->save ( base_path () . '/public/bar.jpg' );
		
		edump ( storage_path () );
		$filename = 'D:\wnmp\www\gzb_master\app\12.jpg';
		// $filename = 'D:\wnmp\www\gzb_master\app\helper.php';
		// $res = Fun::file_type($filename);
		// edump($res);
		$Filesystem = new Filesystem ();
		$res = $Filesystem->type ( $filename );
		edump ( $res );
		
		$uid = '63';
		$pics = \User\Data::where ( 'uid', $uid )->get ()->toArray ();
		
		edump ( $pics );
		
		$count = \User\Data::where ( 'uid', $uid )->whereIn ( 'type', array (
				0,
				1,
				2,
				3 
		) )->count ();
		edump ( $count );
		
		$orderid = '1440117695578665';
		$uid = '547';
		
		$payday = \Order\Periods::select ( 'payday' )->where ( 'uid', $uid )->where ( 'orderid', $orderid )->first ();
		$payday = $payday ['payday'];
		sqlLastSql ();
		edump ( $payday );
		
		$dt = \Carbon\Carbon::createFromDate ( date ( 'Y' ), date ( 'm' ), date ( 'd' ) )->addDays ( 1 );
		$dt = $dt->format ( 'Y-m-d' );
		$day = Fun::diffBetweenTwoDays ( $dt, date ( 'Y-m-d' ) );
		
		edump ( Fun::diffBetweenTwoDays ( '2015-08-11 00:00:00', '2015-07-12' ) );
		edump ( $day );
		$res = PointsV2::loan_setting ( 1000, 1, 30 );
		
		dump ( $res );
		exit ();
		// 128452
		
		mt_mark ( 'start' );
		// get_caller_info('asd');
		$res = debug_backtrace ();
		if (isset ( $res [1] )) {
			dump ( $res [1] );
		}
		dmt_mark ( 'start', 'end' );
		
		exit ();
		
		$res = RedpacketCode::get_blue_info_by_uid ( '128452' );
		
		dump ( $res ['uid'] );
		exit ();
		
		$exchange_code = '65454';
		$dbprefix = \DB::getTablePrefix ();
		$record = \DB::table ( 'user_redpacket_code AS ' . $dbprefix . 'c' )->select ( 'c.*', 'a.user_type' )->join ( 'user_redpacket_account AS ' . $dbprefix . 'a', 'c.uid', '=', 'a.uid' )->where ( 'c.my_code', $exchange_code )->where ( 'c.uid', '>', 0 )->first ();
		
		$record = RedpacketCode::where ( 'my_code', $exchange_code )->where ( 'uid', '>', 0 )->first ()->toArray ();
		edump ( $record );
		
		$str = '{
				"resultcode": "200",
				"reason": "Return Successd!",
				"result": {
				"province": "浙江",
				"city": "杭州",
				"areacode": "0571",
				"zip": "310000",
				"company": "中国移动",
				"card": "移动187卡"
				}
			}';
		var_export ( json_decode ( $str, true ) );
		
		$res = getReturnInLogFile ( 'logs', 'Return' );
		edump ( $res );
		$function = array (
				LocalTestController::class,
				'index' 
		);
		$function = array (
				RedpacketService::class,
				'common_Validate' 
		);
		$function = 'dump';
		$result = getAnnotation ( $function );
		edump ( $result );
		
		$tables = DB::select ( 'show tables;' );
		$db_name = Config::get ( 'database.connections.mysql.database' );
		$table_field = 'Tables_in_' . $db_name;
		$hasUid = array ();
		foreach ( $tables as $value ) {
			// Tables_in_guozhongbao
			$tablename = $value->$table_field;
			// show columns from
			$showtable = DB::select ( 'show create table ' . $tablename );
			$showtable = ( array ) $showtable [0];
			if (preg_match ( '/`uid`\s*int/', $showtable ['Create Table'] )) {
				$hasUid [] = $tablename;
			}
		}
		dump ( $hasUid );
		edumpLastSql ();
		
		$res = get_defined_vars ();
		
		exit ();
		
		$validator = Validator::make ( array (
				'name' => 'Dayle' 
		), array (
				'name' => 'required|min:11' 
		) );
		$message = $validator->messages ();
		edump ( $message );
		
		exit ();
		
		unlink ( 'tmp.txt' );
		// $account = '6236682990000454075';
		// $res = \Ser\Lend\LendService::get_bank ( $account );
		// edump($res);
		
		$validator = Validator::make ( array (
				'name' => 'Dayle' 
		), array (
				'name' => 'required|min:11' 
		) );
		$message = $validator->messages ();
		edump ( $message );
		
		// $prefix = DB::getTablePrefix();
		
		$file = file ( 'tmp.txt' );
		
		foreach ( $file as $value ) {
			$value = trim ( $value );
			$sql = "DELETE FROM gzb_user_cheat where phone = '$value';" . PHP_EOL;
			echo $sql;
		}
		exit ();
		// 6223093310001039281
		// $this->tongji();
		$res = Fun::returnArray ( 200, 'ds', '_RPT_WITHDRAW_NO_BALANCE' );
		edump ( $res );
		exit ();
		
		$this->invalidUidInsertSql ();
		
		$res = Cheat::where ( 'registered_at', '<', '2015-07-01 00:00:00' )->whereNull ( 'address' )->whereNull ( 'identity' )->delete ();
		edump ( $res );
		exit ();
		
		// (new LocalTest1Controller())->run();
		// $this->time_generate_code();
		exit ();
		$this->time_generate_large_sql ();
		
		$this->tongji ();
		
		$this->invalidUidInsertSql ();
		exit ();
		
		$this->time_invalidUidInsertSql ();
		exit ();
		$this->invalidUidInsertSql ();
		$this->getAllValidOldUid (); // 3326/30451
		exit ();
		if (! file_exists ( 'jiang_zhe_hu_ids.php' )) {
			static::outer_province ( 111 );
		}
	}
	
	/**
	 * 生成接口文档
	 * 参数源
	 */
	public function generate_document() {
		$dics = new \Dic ();
		$dics->add ( '西湖区', 1 );
		$dics->add ( '西城区', 2 );
		$dics->add ( '西北区', 3 );
		$dics->add ( '西但', 322 );
		$dics->add ( '西北人', 3 );
		$dics->add ( '西北区区', 3 );
		$dics->add ( '西湖区', 4 );
		$dics->add ( '西湖区区', [ 
				1,
				2,
				3 
		] );
		
		$dics->scan ( true );
		$res = $dics->complete ( '西' );
		dump ( $res );
		dump ( $dics->find ( '西湖区区' ) );
		dump ( $dics->find ( '西湖区' ) );
		exit ();
	}
	public function four() {
		$areas = AreaMo::get ()->toArray ();
		$area = [ ];
		foreach ( $areas as $value ) {
			if ($value ['type'] >= 1) {
				$key = $value ['cup'] . '|' . $value ['name'];
				$area [$key] = $value ['cid'];
			}
		}
		$stat = array_fill ( 0, 20, 0 );
		$fp = fopen ( 'address_fix_' . __FUNCTION__ . '_' . ( int ) (time () / 100) . '.sql', 'w' );
		
		$result = \DB::select ( 'SELECT * FROM gzb_user_address WHERE  area_id > 3571 ORDER BY area_id  DESC' );
		foreach ( $result as $value ) {
			$value = ( array ) $value;
			$addr = explode ( ' ', $value ['address'] );
			$stat [count ( $addr )] ++;
			if (count ( $addr ) >= 4) {
				$p_id = $area ['3571|' . $addr [0]];
				$c_id = $area [$p_id . '|' . $addr [1]];
				$q_id = $area [$c_id . '|' . $addr [2]];
				$str = "$p_id $c_id $q_id";
				$sql = createUpdateSql ( 'gzb_user_address', array (
						'area_id' => $q_id 
				), array (
						'id' => $value ['id'] 
				) );
				fwrite ( $fp, $sql . ';' . PHP_EOL );
			}
		}
		fclose ( $fp );
		dump ( $stat );
	}
	
	/**
	 * 制作地址字典树
	 * 
	 * @return multitype:
	 */
	public function mkDic() {
		$areas = AreaMo::orderBy ( 'type' )->get ()->toArray ();
		$dics = [ ];
		
		$flect = [ ];
		
		foreach ( $areas as $value ) {
			if ($value ['type'] >= 1) {
				$value ['name'] = str_replace ( ' ', '', $value ['name'] );
				$flect [$value ['cid']] = [ 
						'cup' => $value ['cup'],
						'name' => $value ['name'] 
				];
				
				// preg_replace('/\s/', '', $flect[$cup] ['name'])!= $flect[$cup] ['name'] && dump($flect[$cup] ['name']);
				str_replace ( ' ', '', $value ['name'] ) != $value ['name'] && dump ( $value ['name'] );
				
				// '宾 县' == $value ['name'] && dump($value ['name']);
			}
		}
		
		$s = 3571;
		
		foreach ( $areas as $value ) {
			if ($value ['type'] >= 1) {
				$value ['name'] = str_replace ( ' ', '', $value ['name'] );
				$names = [ 
						$value ['name'] 
				];
				$cup = $value ['cup'];
				while ( isset ( $flect [$cup] ) ) {
					
					$names [] = str_replace ( ' ', '', $flect [$cup] ['name'] );
					$cup = $flect [$cup] ['cup'];
				}
				$names = array_reverse ( $names );
				$name = implode ( '', $names );
				
				$this->addDic ( $dics, $name, $value ['cid'] );
			}
		}
		return $dics;
	}
	
	/**
	 * 添加一条字典
	 * 
	 * @param unknown $dics        	
	 * @param unknown $key        	
	 * @param unknown $value        	
	 */
	public function addDic(&$dics, $key, $value) {
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
	public function find($dics, $str) {
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
	public function one() {
		$areas = AreaMo::orderBy ( 'type' )->get ()->toArray ();
		$dics = $this->mkDic ();
		// dump($this->find($dics, '仙桃市经济技术开发区青鱼湖路28号'));
		// dump($dics);
		$area_lv = [ ];
		$area_id = [ ];
		$area = [ ];
		foreach ( $areas as $value ) {
			if ($value ['type'] >= 1) {
				$key = $value ['cup'] . '|' . $value ['name'];
				$area [$key] = $value ['cid'];
				$area_lv [$value ['cid']] = $value ['type'];
				$area_id [$value ['cid']] = $value ['name'];
			}
		}
		$stat = array_fill ( 0, 20, 0 );
		// $str = '江苏省苏州市常熟市';
		
		// // $res = $this->find($dics, $str);
		// // edump($res);
		
		$fp = fopen ( 'D:\sqlLog\test\address_fix_' . __FUNCTION__ . '_' . ( int ) (time () / 100) . '.sql', 'w' );
		$_statistics = [ 
				'EPT' => 0,
				'NO3' => 0,
				'UN' => 0 
		];
		$result = \DB::select ( 'SELECT * FROM gzb_user_address WHERE  area_id = id  AND type = 1 ORDER BY area_id  DESC' );
		
		// $result = \DB::select('SELECT * FROM gzb_user_address WHERE area_id > 0 AND area_id < 3571 AND type = 1 LIMIT 30000');
		
		foreach ( $result as $value ) {
			$value = ( array ) $value;
			
			$addr = explode ( ' ', $value ['address'] );
			$stat [count ( $addr )] ++;
			
			if (1 || count ( $addr ) == 1) {
				// lp($value['id'].'|'.$value['created_at'].'|'.$value['address']);
				
				$value ['address'] = str_replace ( ' ', '', $value ['address'] );
				$res = $this->find ( $dics, $value ['address'] );
				$_3_id = 0;
				foreach ( $res as $k => $v ) {
					if (isset ( $area_lv [$k] ) && $area_lv [$k] == 3) {
						$_3_id = $k;
						break;
					}
				}
				
				if (empty ( $res ) || ! $_3_id) {
					lp ( $value ['id'] . '|' . $value ['area_id'] . '|' . $value ['created_at'] . '|' . $value ['address'] );
					dump ( $res );
					empty ( $res ) && $_statistics ['EPT'] ++;
					! empty ( $res ) && ! $_3_id && $_statistics ['NO3'] ++;
				} else {
					
					if ($_3_id != $value ['area_id']) {
						$_statistics ['UN'] ++;
						// lp('UNMATCH|ID='.$value['id'].'|AID='.$value['area_id'].'|3ID='.$_3_id.'|ADDR='.$value['address'].'|AID_ADDR='.( isset($area_id[$value['area_id']]) ? $area_id[$value['area_id']] : 'UNSETED' ));
						$sql = createUpdateSql ( 'gzb_user_address', array (
								'area_id' => $_3_id 
						), array (
								'id' => $value ['id'] 
						) );
						fwrite ( $fp, $sql . ';' . PHP_EOL );
					}
				}
			}
		}
		$stat = array_filter ( $stat );
		dump ( $stat );
		dump ( $_statistics );
		fclose ( $fp );
		exit ();
	}
	public function mkLoginTestData() {
		mt_mark ( 'start' );
		
		$result = Account::select ( 'uid' )->limit ( 100000 )->get ()->toArray ();
		$fp = fopen ( 'D:/sqlLog/' . __FUNCTION__ . '.sql', 'w' );
		$number = 100000;
		$count = count ( $result );
		for($i = 0; $i < $number; $i ++) {
			
			$index = mt_rand ( 0, $count - 1 );
			$rd_date = date ( 'Y-m-d H:i:s', time () - mt_rand ( 0, 6999999 ) );
			$data = [ 
					'uid' => $result [$index] ['uid'],
					'login_at' => $rd_date,
					'ip_addr' => '127.0.0.1' 
			];
			$sql = createInsertSql ( 'gzb_user_login_log', $data );
			fwrite ( $fp, $sql . ';' . PHP_EOL );
		}
		fclose ( $fp );
		dump ( mt_mark ( 'start', 'end', 'MB' ) );
	}
	public function mkChangeLog() {
		$account = Account::select ( 'uid', 'phone' )->limit ( 30000 )->get ()->toArray ();
		$number = count ( $account );
		$sen = [ 
				3,
				7,
				8,
				5 
		];
		
		$fp = fopen ( 'D:/sqlLog/rebindPhoneFrequently.sql', 'w' );
		
		for($i = 0; $i < $number; $i ++) {
			$rdkey = mt_rand ( 0, $number - 1 );
			$rdphone = '1' . $sen [mt_rand ( 0, 3 )] . mt_rand ( 10000, 99999 ) . mt_rand ( 10000, 99999 );
			if (isset ( $account [$rdkey] ['time'] )) {
				$t_prev = strtotime ( $account [$rdkey] ['time'] );
				$t_now = time ();
				$created_at = date ( 'Y-m-d H:i:s', $t_now - mt_rand ( 0, $t_now - $t_prev ) );
			} else {
				$created_at = date ( 'Y-m-d H:i:s', time () - mt_rand ( 0, 6999999 ) );
			}
			$data = array (
					'uid' => $account [$rdkey] ['uid'],
					'oldphone' => $account [$rdkey] ['phone'],
					'unbind_code' => 'TEST',
					'rebind_code' => 'TEST',
					'newphone' => $rdphone,
					'created_at' => $created_at,
					'save_status' => 1 
			);
			$account [$rdkey] ['phone'] = $rdphone;
			$account [$rdkey] ['time'] = $created_at;
			$sql = createInsertSql ( 'gzb_user_change_phone_log', $data );
			fwrite ( $fp, $sql . ';' . PHP_EOL );
		}
		fclose ( $fp );
		exit ();
	}
	public function generate() {
		$this->mkLoginTestData ();
	}
}















