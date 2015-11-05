<?php
class NewpayAction extends HomeAction{
	
	/**
	 * 新版微信支付步骤
	 * 	1.根据商户配置获得OauthUrl， 跳转后，微信支付为回调链接添加参数Code，根据COde可获取用户openid
	 * 	2.通过openid实例化JsApi，进入支付确认界面
	 * 	3.确认支付，页面调用JsApi进行支付
	 * 	4.支付成功调用notifyurl，做业务处理，完成支付
	 * 	5，2和3需在一个连接内
	 * 支付三步
	 * 	1.保存所需参数，跳转2链接
	 * 		memberTopUp（保存参数在session中，进行跳转）
	 * 	2.获取openid，调起JSAPI，进入支付界面
	 * 		memberTopUpStep2（调用$this->toShowWeixinPay()）
	 * 	3.回调处理（注入 memberTopUpNotifyConf 和 memberTopUpNotifyDeal ）
	 * 		memberTopUpNotify（调用$this->newpayNotify();）
	 */
	
	function __construct(){
		parent::__construct();
		vendor('Newwxpay.WxPayPubHelper');
		vendor('Newwxpay.WxPay.pub.config');
	}	
	
	public function dshopPay(){
		$deal		= __FUNCTION__.'Deal';
		$step2		= __FUNCTION__.'Step2';
		//I('request.pays') == 'weipay' && $this->$deal($_REQUEST);
		$wid		= $_REQUEST['wid'];
		session('wid',$wid);
		session('pay_params',$_REQUEST);
		$url = C('HTTP_DOMIN').'newpay/'.$step2;//
		Header("Location:".$url);
		exit;
	}
	private function dshopPayDeal($param){
		$id 			= $param['id'];
		$wid 			= $param['wid'];
		$token 			= $param['token'];
		$detail 		= M('app_dshop_order')->where(array('id'=>$id))->find();
		if (!$detail['uid']||$detail['state']!=0||$detail['wid']!=$wid) {
			echo '参数错误';
			exit;
		}
		$user 			= M('app_dshop_user')->where(array('wid'=>$wid,'token'=>$token))->find();
		if ($user['id']!=$detail['uid']){
			echo 'token错误';
			exit;
		}
		$tconf 			= M('payment')->where("pid='".$wid."' and pc_type='weipaynew' and pc_enabled=1")->find();
		if (empty($tconf)){
			edumpsql();
			echo '商户尚未开通新微信支付';
			exit;
		}
		$detail['payname']= $tconf['pc_name'];
		$this->assign('pagepid',$detail['duser_id']);
		$data['orderBusiness'] 	= $detail['payname'];
		$data['orderId'] 		= $detail['id'];
		$data['orderName'] 		= $detail['total_title'];
		$data['orderPay']		= $detail['pay_price'];
		return $data;
	}
	public function dshopPayNotify(){
		$this->newpayNotify(__FUNCTION__.'Conf',__FUNCTION__.'Deal');
	}
	private function dshopPayNotifyConf($notify){
		
		$detail 			= M('app_dshop_order')->where("id='".$notify->data['out_trade_no']."'")->find();
		$tconf 				= M('payment')->where("pid='".$detail['wid']."' and pc_type='weipaynew'")->find();
		$tconf 				= (array)json_decode($tconf['pc_config']);
		
		return $tconf;
	}	
	private function dshopPayNotifyDeal($notify){
		$order 			= M('app_dshop_order')->where(array('id'=>$notify->data["out_trade_no"]))->find();
		M('app_dshop_order')->where("id='".$notify->data["out_trade_no"]."'")->save(array('state'=>1,'etime'=>time(),'paytime'=>time(),'trade_no'=>$notify->data['transaction_id'],'openid'=>$notify->data['openid']));
		
		//计算佣金
		D('Distribution')->createDistributionCommissionRecord($notify->data["out_trade_no"]);
		//
		D('Distribution')->sendNewOrderNotify($order);
		
		//更新操作日志
		$ldata['orderid'] 		= $notify->data["out_trade_no"];
		$ldata['action']		= 2;
		$ldata['auth'] 			= '买家';
		$ldata['ctime']			= time();
		M('app_dshop_order_log')->add($ldata);

		//更新支付日志
		$pdata['orderid']		= $notify->data["out_trade_no"];
		$pdata['payment']		= 1;
		$pdata['paytype']		= 1;
		$pdata['price']			= $notify->data["total_fee"] ;
		$pdata['ctime']			= time();
		M('app_dshop_order_pay_log')->add($pdata);

		//更新产品销量、库存
		$odetails 				= M('app_vshop_order_detail')->where(array('orderid'=>$notify->data["out_trade_no"]))->select();
		foreach ($odetails as $ov){
			if ($ov['spec_id']>0){
				M('app_dshop_spec')->where(array('id'=>$ov['spec_id'],'pid'=>$ov['productid']))->setDec('maxnum',$ov['num']);
			}
			M('app_dshop_product')->where(array('id'=>$ov['productid']))->setDec('maxnum',$ov['num']);
			M('app_dshop_product')->where(array('id'=>$ov['productid']))->setInc('salenum',$ov['num']);
		}
		//异步通知日志
		$notifylog['notifys']		= json_encode($_POST);
		$notifylog['ctime']			= time();
		M('app_dshop_notify_log')->add($notifylog);
	}
	public function dshopPayStep2(){
		$wid 		= session('wid');;
		$tconf		= $this->getWeixinConf($wid);
		$jsApi 		= new JsApi_pub($tconf);
		if (!isset($_GET['code'])){
			
			$url 		= $jsApi->createOauthUrlForCode(C('HTTP_DOMIN').'newpay/'.__FUNCTION__);
			Header('Location:'.$url);
		}else{
			//获取code码，以获取openid
			$code 		= $_GET['code'];
			
			$code = $_GET['code'];
			$jsApi->setCode($code);
			$openid = $jsApi->getOpenId();
			
			$param = session('pay_params');
			session('pay_params',NULL);
			
			$deal		= str_replace('Step2','Deal',__FUNCTION__);
			$data 		= $this->$deal($param);
			$display	= 'Dapp@Pay:newwxpay';//_membercard
			$notifyurl	= C('HTTP_DOMIN').'newpay/'.str_replace('Step2','Notify',__FUNCTION__);
			$success_url= '';
			$error_url	= '';
			$this->toShowWeixinPay($tconf, $data, $notifyurl, $openid, $display, $success_url, $error_url);
		}
	}	
	
	
	
	
	/**
	 * 微餐饮支付链接
	 */
	public function mcatPay(){
		$deal		= __FUNCTION__.'Deal';
		$step2		= __FUNCTION__.'Step2';
		$wid		= $_REQUEST['aid'];
		session('wid',$wid);
		session('pay_params',$_REQUEST);
		$url = C('HTTP_DOMIN').'newpay/'.$step2;//
		Header("Location:".$url);
		exit;
	}
	/**
	 * 微餐饮支付业务处理
	 */
	private function mcatPayDeal($param){
		$orderId		= intval($param['id']);
		$aid			= intval($param['aid']);
		$token			= $param['token'];
		$outletid 		= intval($param['outletid']);
		
		$tconf			= M('payment')->where("pid='".$aid."' and pc_type='weipaynew' and pc_enabled=1")->find();;
		
		$orderModel   	= M('app_dingcan_order');
		$recodeModel    = M('app_dingcan_order_record');
		$where			= array('id'=>$orderId,'aid'=>$aid,'outletid'=>$outletid,'token'=>$token);
		$order			= $recodeModel->where($where)->find();
		!$order && $this->error('未找到订单信息,请重新点击付款！',0);
		$order['pay_status'] != 0  && $this->error('订单已支付',0);
		unset($where['id']);
		unset($where['token']);
		$where['order_id']	= $orderId;
		$menu			= $orderModel->where($where)->select();
		$order_pay		= 0.00;
		foreach ($menu as $k=>$v) {
			$price		= floatval($v['price']);
			$num		= intval($v['nums']);
			$order_pay += $price * 1.0 * $num;
		}
		$order_pay == 0 && $this->error( '订单金额为0，无需支付！',0);
		
		
		$data['orderBusiness'] 	= $tconf['pc_name'];
		$data['orderId'] 		= $order['order_id'];
		$data['orderName'] 		= '微餐饮消费  '.$order_pay.' 元';;
		$data['orderPay']		= $order_pay;
		return $data;
	}
	/**
	 * 获取用户openid
	 */
	public function mcatPayStep2(){
	//=========步骤1：网页授权获取用户openid============
		//通过code获得openid
		$wid 		= session('wid');
		$tconf		= $this->getWeixinConf($wid);
		$jsApi 		= new JsApi_pub($tconf);
		if (!isset($_GET['code'])){
			
			$url 		= $jsApi->createOauthUrlForCode(C('HTTP_DOMIN').'newpay/'.__FUNCTION__);
			Header('Location:'.$url);
		}else{
			//获取code码，以获取openid
			$code 		= $_GET['code'];
			
			$code = $_GET['code'];
			$jsApi->setCode($code);
			$openid = $jsApi->getOpenId();
			
			$param = session('pay_params');
			session('pay_params',NULL);
			
			$deal		= str_replace('Step2','Deal',__FUNCTION__);
			$data 		= $this->$deal($param);
			$display	= 'Home@Webfood:newwxpay';//_membercard
			$notifyurl	= C('HTTP_DOMIN').'newpay/'.str_replace('Step2','Notify',__FUNCTION__);
			
			$success_url= 'http://'.$_SERVER['HTTP_HOST'].'/index.php?m=webfood&a=myorder&aid='.$param['aid'].'&outletid='.$param['outletid'];
			$error_url	= $success_url;
			$this->toShowWeixinPay($tconf, $data, $notifyurl, $openid, $display, $success_url, $error_url);
		}
	}
	/**
	 * 支付回调
	 */
	public function mcatPayNotify(){
		
		$this->newpayNotify(__FUNCTION__.'Conf',__FUNCTION__.'Deal');
		
	}
	/**
	 * 根据回调订单号获取微信支付配置
	 */
	private function mcatPayNotifyConf($notify){
		//获取商户信息
		$detail 			= M('app_dingcan_order_record')
								->where(array('order_id'=>$notify->data['out_trade_no']))
								->find();
		$tconf 				= M('payment')->where("pid='".$detail['aid']."' and pc_type='weipaynew'")->find();
		$tconf 				= (array)json_decode($tconf['pc_config']);
		return $tconf;
	}
	/**
	 * 支付成功的业务处理
	 */
	private function mcatPayNotifyDeal($notify){
		$saveData['pay_status'] = 1;
		$saveData['pay_time'] 	= time();
		$saveData['should_pay'] = $notify->data['total_fee']/100.0;
		$saveData['pay_amount'] = $notify->data['total_fee']/100.0;
		$save	= M('app_dingcan_order_record')
				->where(array('pay_status'=>'0','order_id'=>$notify->data['out_trade_no']))
				->save($saveData);
	}
	
	/**
	 * 通用回调处理
	 * $notifyConf 为通过$notify获取微信支付配置的方法
	 * $notifyDeal 支付成功的处理方法
	 */
	public function newpayNotify($notifyConf,$notifyDeal){
		
		vendor('Newwxpay.WxPayPubHelper');
		//使用通用通知接口
		$notify = new Notify_pub();
		//存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];

		$notify->saveData($xml);
		
		$tconf				= $this->$notifyConf($notify);
		
		//验证签名，并回应微信。
		//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
		//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
		//尽可能提高通知的成功率，但微信不保证通知最终能成功。
		if($notify->checkSign($tconf) == FALSE){
			$notify->setReturnParameter("return_code","FAIL");//返回状态码
			$notify->setReturnParameter("return_msg","签名失败");//返回信息
		}else{
			$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
		}
		$returnXml = $notify->returnXml();
		echo $returnXml;
		$model = M('log_newweixin_notify');
		//以数据库形式记录回调信息
		$notifydata['xml'] = $xml;
		$notifydata['etime'] = time();
		$notifydata['notify'] = $returnXml;
		$model->data($notifydata)->add();

		if($notify->checkSign($tconf) == TRUE)
		{
			if ($notify->data["return_code"] == "FAIL") {
				//此处应该更新一下订单状态，商户自行增删操作
				$notifydata['xml'] = "【通信出错】:\n".$xml."\n";
				$notifydata['etime'] = time();
				$model->data($notifydata)->add();
			}
			elseif($notify->data["result_code"] == "FAIL"){
				//此处应该更新一下订单状态，商户自行增删操作
				$notifydata['xml'] = "【通信出错】:\n".$xml."\n";
				$notifydata['etime'] = time();
				$model->data($notifydata)->add();
			}
			else{
				//支付成功
				$notifydata['xml'] = "【支付成功】:\n".$xml."\n";
				$notifydata['out_trade_no'] = $notifydata->data["out_trade_no"];
				$notifydata['etime'] = time();

				//检测是否已存在该订单的支付成功记录
				$notifyRecord = $model->where(array('out_trade_no'=>$notifydata->data["out_trade_no"]))->find();
				if(empty($notifyRecord)){
					$model->data($notifydata)->add();
					$this->$notifyDeal($notify);
				}
			}
			echo 'success';
			exit;
		}
		//商户自行增加处理流程,
		//例如：更新订单状态
		//例如：数据库操作
		//例如：推送支付完成信息
		echo 'fail';
		exit;
	}
	
	
	
	
	/**
	 * 退款接口
	 */
	private  function refund(){
		$wid = '1018';
		$refund =  new Refund_pub($wid);
		$order = new OrderQuery_pub($wid);
		//out_trade_no，out_refund_no、total_fee、refund_fee
		$topUpRecode_4098 =  M('app_mcard_chargelogs')->where(array('bid'=>233,'uid'=>4098))
														->order('id desc')											
														->select();
		
		$transaction_id		= strval($topUpRecode_4098[0]['transaction_id']);										
		$out_trade_no 		= strval($topUpRecode_4098[0]['id']);
		$out_refund_no		= strval(intval($topUpRecode_4098[0]['id'])+200000000);
		$total_fee			= intval($topUpRecode_4098[0]['money']*100);
		$refund_fee 		= intval($topUpRecode_4098[0]['money']*100);
		
		$data['id'] 		= intval($topUpRecode_4098[0]['id'])+200000000;//600000027
		$data['bid'] 		= 233;
		$data['uid'] 		= 4098;
		$data['source'] 	= 2;
		$data['money'] 		= $topUpRecode_4098[0]['money'];
		$data['balance'] 	= $topUpRecode_4098[0]['balance'];
		$data['ctime'] 		= time();
		$data['state'] 		= 0;
		
		
		
		if($transaction_id){
			$order->setParameter("transaction_id",$transaction_id);//openid
		}
		$order->setParameter("out_trade_no",$out_trade_no);//
		$order_ret =  $order->getResult();
		dump($order_ret);
		
		if($transaction_id){
			$refund->setParameter("transaction_id",$transaction_id);//openid
		}
		$refund->setParameter("out_trade_no",$out_trade_no);//
	 	$refund->setParameter("out_refund_no",$out_refund_no);//
		$refund->setParameter("total_fee",$total_fee);//
		$refund->setParameter("refund_fee",$refund_fee);//
		
		dump($data);
		
		$rut = $refund->getResult();
		
		dump($rut);
	}
	
	/**
	 * 充值获取积分
	 */
	private   function chargeScore($user,$price,$chid,$name){
	//充值获取积分
		$getScore = 0;
		$ntime    = time();
		$model    = M('app_mcard_scorestrategy');
		$scres    = $model->where(array('bid'=>$user['bid'],
										'type'=>1,
										'_string'=>'(is_time_limit=0) or(is_time_limit=1 and (start <= '.$ntime.' and end >= '.$ntime.')  )'))->select();
		if($scres){
			$setting = $scres[0]['setting'];
			if($setting){
				$set 		= (array)json_decode($setting,true);
				function rsortSetting($a,$b){
					return $a['s']==$b['s']?0:($a['s']>$b['s']?-1:1);
				}
				usort($set,'rsortSetting');
				foreach ($set as $k=>$v){
					if($price >= $v['s']){
						$getScore  = $v['v'];
						break;
					}					
				}
				if($getScore){
					$data['bid']       = $user['bid'];
					$data['uid']       = $user['id'];
					$data['type']      = '7';
					$data['salename']  = $name;
					$data['saleid']    = $chid;
					$data['salecoin']  = $price;
					$data['salescore'] = $getScore;
					$data['ctime'] = $data['pftime'] = $data['sytime'] = time();
				    $save     = M('app_mcard_tongji')->add($data);
					
				    if(!$save) return false;
					
					$mdata['id'] 	  =	$user['id'];
					$mdata['score']   = $user['score'] + $getScore;
					$mdata['jfnums']  = $user['jfnums']+ $getScore;
					$mdata['czjf']    = $user['czjf']  + $getScore;
					$save             = M('app_mcard_member')->save($mdata);
					if(!$save) return false;
				}
			}
		}//End 
		return true;
	}
	
	
	
	public function memberTopUp(){
		
		$myToken 	= "oIcPnjrRmUQDqCP7s2aGA-x0-y-U";
		$token		= $this->getOpenId();
		$wid		= I('request.wid');
		
		I('request.pays') == 'weipay' && $this->memberTopUpDeal($_REQUEST);
		
		session('wid',$wid);
		session('pay_params',$_REQUEST);
		session('mtu_wid'	,I('request.wid'));//店铺微信ID
		session('mtu_token'	,$this->getOpenId());//用户token
		
		$url = C('HTTP_DOMIN').'newpay/memberTopUpStep2';//
		Header("Location:".$url);
		exit;
	}

	public function memberTopUpStep2(){
		//=========步骤1：网页授权获取用户openid============
		//通过code获得openid
		$wid 		= session('wid');;
		$tconf		= $this->getWeixinConf($wid);
		$jsApi 		= new JsApi_pub($tconf);
		if (!isset($_GET['code'])){
			
			$url 		= $jsApi->createOauthUrlForCode(C('HTTP_DOMIN').'newpay/memberTopUpStep2');
			Header('Location:'.$url);
		}else{
			//获取code码，以获取openid
			$code 		= $_GET['code'];
			
			$code = $_GET['code'];
			$jsApi->setCode($code);
			$openid = $jsApi->getOpenId();
			
			$param = session('pay_params');
			session('pay_params',NULL);
			$data 		= $this->memberTopUpDeal($param);
			$display	= 'Vapp@Pay:newwxpay_membercard';//_membercard
			$notifyurl	= C('HTTP_DOMIN').'newpay/memberTopUpNotify';
			$success_url= '';
			$error_url	= '';
						
			$this->toShowWeixinPay($tconf, $data, $notifyurl, $openid, $display, $success_url, $error_url);
		}
	}
	/**
	 * 处理会员卡充值业务
	 */
	private  function memberTopUpDeal($param){
		
		$token		= $this->getOpenId();
		$wid		= intval($param['wid']);
		$money		= floatval($param['money']);
		$money		= sprintf("%.2f", $money);
		
		if(! ($money > 0) ){
			echo '请输入正确的金额!';
			exit;
		}
		//验证token
		$model	= M('app_mcard_member');
		$member	= $model->alias('m')
						->field('m.*')
						->join(C('DB_PREFIX').'app_mcard_business b on m.bid = b.id')
						->where(array('b.wid'=>$wid,'wechatid'=>$token))
						->find();
		if(empty($member['name'])){
			echo '不存在的会员!';
			exit;
		}
		//验证商户wid
		//验证支付配置
		//获取店铺微信的支付配置
		if($param['pays'] == 'weipaynew'){
			$tconf 	= M('payment')->where(array('pid'=>$wid,'pc_type'=>'weipaynew','pc_enabled'=>1))->find();
			if (empty($tconf)){
				echo '商户尚未开通新微信支付';
				exit;
			}
			//进入jsapi
			$busName				= $tconf['pc_name'];
			$tconf 					= (array)json_decode($tconf['pc_config']);
		}
		$source = $param['pays'] == 'weipay'? 4:3;
		$lastId	= M('app_mcard_chargelogs')->order('id desc')->getField('id');
		$data = array('bid'=>$member['bid'],
					  'uid'=>$member['id'],
					  'source'=>$source,
					  'money'=>$money,
					  'balance'=>$member['coin'],
					  'ctime'=>time(),
					  'state'=>0);
		if($lastId < 600000001){
			$data['id']	= 600000001;
		}
		
		$orderId = M('app_mcard_chargelogs')->data($data)->add();
		if($param['pays'] == 'weipay'){
			$newid = $orderId;
			redirect(C('HTTP_STCDOMIN').'/pay/weixin/?t=topup&wid='.$wid.'&id='.$newid.'&wxref=mp.weixin.qq.com&showwxpaytitle=1');
		}
		$data['orderBusiness'] 	= $busName;
		$data['orderId'] 		= $orderId;
		$data['orderName'] 		= '会员充值 '.$money.' 元';;
		$data['orderPay']		= $money;
		
		return $data;	
	}
	

	public function memberTopUpNotify(){
		
		$this->newpayNotify(__FUNCTION__.'Conf',__FUNCTION__.'Deal');
	}

	private function memberTopUpNotifyConf($notify){
		//获取商户信息
		$detail 			= M('app_mcard_chargelogs')->alias('c')
														->field('c.*,b.wid')
													   	->join(C('DB_PREFIX').'app_mcard_business b on c.bid = b.id')
														->where("c.id='".$notify->data['out_trade_no']."'")
														->find();
		$tconf 				= M('payment')->where("pid='".$detail['wid']."' and pc_type='weipaynew'")->find();
		$tconf 				= (array)json_decode($tconf['pc_config']);
		return $tconf;
	}
	private function memberTopUpNotifyDeal($notify){
	
		
		$order 			= M('app_mcard_chargelogs')->where(array('id'=>$notify->data["out_trade_no"]))->find();
		$newBalance 	= sprintf("%.2f",($order['balance']+ $order['money']));
		//更新充值状态
		M('app_mcard_chargelogs')->where("id='".$notify->data["out_trade_no"]."'")
								->save(array('state'=>1,
							'paytime'=>time(),
							'balance'=> $newBalance ,
							'transaction_id'=>$notify->data['transaction_id']));
		//更新用户余额
		
		$user	= M('app_mcard_member')->where(array('id'=>$order['uid']))->find();						
								
		$member['id'] = $order['uid'];
		$member['coin']	=  floatval(sprintf("%.2f",floatval($user['coin'])+floatval($order['money'])));
		$member['etime']	= time();	
		M('app_mcard_member')->save($member);
		$this->chargeScore($user, $order['money'], $order['id'], '新微信支付');
		//充值获取积分
	}
	
	
	
	public function getOpenidTconf(){
		$tconf['appsecret'] =	C('OPENID_APPSECRET');
		$tconf['appid'] 	=	C('OPENID_APPID');
		return $tconf;
	}
	private function updateTokenToOpenid($token,$openid){
		/**
		//Vshop
		M('app_vshop_user')->where(array('token'=>$token))->save(array('token'=>$openid));
		//MemberCard
		M('app_mcard_member')->where(array('wechatid'=>$token))->save(array('wechatid'=>$openid));
		//BigWheel
		M('big_wheel_log')->where(array('code'=>$token))->save(array('code'=>$openid));
		M('big_wheel_code')->where(array('code'=>$token))->save(array('code'=>$openid));
		//Board
		//M('board_member')->where(array('openid'=>$token))->save(array('openid'=>$openid));
		//M('board_message')->where(array('openid'=>$token))->save(array('openid'=>$openid));
		//Card
		M('card_log')->where(array('code'=>$token))->save(array('code'=>$openid));
		M('card_code')->where(array('code'=>$token))->save(array('code'=>$openid));
		//Coupon
		M('coupon_sn')->where(array('ownuser'=>$token))->save(array('ownuser'=>$openid));
		M('coupon_log')->where(array('user'=>$token))->save(array('user'=>$openid));
		//Exam
		M('exam_joinuser')->where(array('token'=>$token))->save(array('token'=>$openid));
		M('exam_sn')->where(array('ownertoken'=>$token))->save(array('ownertoken'=>$openid));
		//Vote
		M('vote_log')->where(array('user'=>$token))->save(array('user'=>$openid));
		//Egg
		M('smashegg_code')->where(array('wechat_code'=>$token))->save(array('wechat_code'=>$openid));
		M('smashegg_log')->where(array('code'=>$token))->save(array('code'=>$openid));
		//Fruit
		M('app_fruit_log')->where(array('user'=>$token))->save(array('user'=>$openid));
		M('app_fruit_sn')->where(array('ownuser'=>$token))->save(array('ownuser'=>$openid));
		
		M('lottery_log')->where(array('user'=>$token))->save(array('user'=>$openid));
		M('lottery_user')->where(array('token'=>$token))->save(array('token'=>$openid));
		M('lottery_sn')->where(array('ownuser'=>$token))->save(array('ownuser'=>$openid));
		
		M('hotel_order')->where(array('token'=>$token))->save(array('token'=>$openid));
		M('app_survey_user')->where(array('token'=>$token))->save(array('token'=>$openid));
		
		M('app_property_myimpress')->where(array('token'=>$token))->save(array('token'=>$openid));
		
		M('app_tuan_myshop')->where(array('token'=>$token))->save(array('token'=>$openid));
		M('app_tuan_order')->where(array('token'=>$token))->save(array('token'=>$openid));
		
		M('app_car_usercar')->where(array('wechatid'=>$token))->save(array('wechatid'=>$openid));
		M('app_car_wereserve_book')->where(array('wechatid'=>$token))->save(array('wechatid'=>$openid));
		M('app_car_wereserve_care_book')->where(array('wechatid'=>$token))->save(array('wechatid'=>$openid));
		M('wereserve_book')->where(array('wechatid'=>$token))->save(array('wechatid'=>$openid));
		M('app_dingcan_order')->where(array('token'=>$token))->save(array('token'=>$openid));
		M('app_dingcan_order_record')->where(array('token'=>$token))->save(array('token'=>$openid));
		M('app_dingcan_userinfo')->where(array('token'=>$token))->save(array('token'=>$openid));
		**/
	}
	
	
	/**
	 * 获得本服务好的微信配置（appsecret、appid）
	 */
	public function getThisOpenidConf($wid){
		$tconf 				= M('payment')->where(array('pid'=>$wid,'pc_type="weipaynew"'))->find();
		if($tconf){
			$tconf 			= (array)json_decode($tconf['pc_config']);
			if($tconf['appid'] && $tconf['appsecret'] ){
				//Check appid and appsecret
				//How to test : Get access_token
				$tconf['conf_from'] = 'SELF';
				return $tconf;
			}
		}
		$tconf = $this->getOpenidTconf();
		$tconf['conf_from'] = 'CONFIG'; 
		return $tconf;
	}
	
	public function getUserInfoForDapp(){
		//获取店铺微信的支付配置
		//进入jsapi
		$wid 	= I('request.wid') ? I('request.wid',0,'intval')  :session('wid');
		$jsApi = new User_Notify_pub($wid);
		if (!isset($_GET['code'])){
			//触发微信返回code码
			$OauthUrl = session('url');
			$url = $jsApi->createOauthUrlForCodeUserInfo($OauthUrl);
			redirect($url);
		}else{
			//获取code码，以获取openid
			$code 	= $_GET['code'];
			$jsApi->setCode($code);
			$openid = $jsApi->getOpenId();
			$tkid 	= I('request.tkid',0,'intval');
			$wid 	= I('request.wid',0,'intval');
			if($openid){
				$data['wid']	= $wid;
				$data['token'] = $openid;
				$record = M('weixin_wid_openid')->where($data)->find();
				if($record){
					if(!$record['nickname'] || !$record['headimgurl'] ){
						$userInfo = $jsApi->getUserInfo(); //getUserInfo
						M('weixin_wid_openid')->where($data)->save($userInfo);
					}
				}else{
					$userInfo = $jsApi->getUserInfo(); //getUserInfo
					$sdate = array_merge($userInfo,$data);
					$sdate['ctime'] = time();
					M('weixin_wid_openid')->add($sdate);
				}
				$backurl = 'http://'.$wid.'.'.C('DSHOP_DOMIN').'/index.php?g=dapp&openid='.$openid;
				if($tkid > 0 ){
					$backurl = 'http://'.$wid.'.'.C('DSHOP_DOMIN').'/index.php?g=dapp&pid='.$tkid.'&openid='.$openid;
				}
				redirect($backurl);
			}
			exit('用户授权失败，请查阅相关设置项！');
		}
	}
	public function getUserInfoForVapp(){
		//获取店铺微信的支付配置
		//进入jsapi
		$tconf = $this->getOpenidTconf();
		$jsApi = new JsApi_pub($tconf);
		$tkid = I('request.wid',0,'intval');
		//=========步骤1：网页授权获取用户openid============
		//通过code获得openid
		if (!isset($_GET['code'])){
			//触发微信返回code码
			$url = $jsApi->createOauthUrlForCode(session('url'));
		//	dump($url);exit;
			redirect($url);
		}else{
			//获取code码，以获取openid
			$code = $_GET['code'];
			$jsApi->setCode($code);
			$openid = $jsApi->getOpenId();
			$wid = I('request.wid',0,'intval');
			/**
			$tkid = I('request.tkid',0,'intval');
			$wid = I('request.wid',0,'intval');
			$result = M('weixin_token_openid')->where(array('id'=>$tkid))->find();
			if($result && !$result['openid']){
				M('weixin_token_openid')->where(array('id'=>$tkid))->save(array('openid'=>$openid));
				$this->updateTokenToOpenid($result['token'], $openid);
			}
			**/
			$backurl = 'http://'.$wid.'.'.C('VSHOP_DOMIN').'/index.php?g=vapp&openid='.$openid;
			//dump($backurl);exit;
			redirect($backurl);
		}
	}
	public function getUserInfoForAfanti(){
		//获取店铺微信的支付配置
		//进入jsapi
		$tconf = $this->getOpenidTconf();
		$jsApi = new JsApi_pub($tconf);
		//=========步骤1：网页授权获取用户openid============
		//通过code获得openid
		if (!isset($_GET['code'])){
			//触发微信返回code码
			$url = $jsApi->createOauthUrlForCode(session('url'));
		//	dump($url);exit;
			redirect($url);
		}else{
			//获取code码，以获取openid
			$code = $_GET['code'];
			$jsApi->setCode($code);
			$openid = $jsApi->getOpenId();
			/**
			$tkid = I('request.tkid');
			$result = M('weixin_token_openid')->where(array('id'=>$tkid))->find();
			if($result && !$result['openid']){
				M('weixin_token_openid')->where(array('id'=>$tkid))->save(array('openid'=>$openid));
				$this->updateTokenToOpenid($result['token'], $openid);
			}
			**/
			$backurl = 'http://'.C('DOMIN').'index.php?g=app&m=membercard&openid='.$openid;
			//dump($backurl);exit;
			redirect($backurl);
		}
	}
	
	
	private function getWeixinConf($w_id){
		$wid				= intval($w_id);
		$tconf 				= M('payment')->where("pid='".$wid."' and pc_type='weipaynew' and pc_enabled=1")->find();
		if (empty($tconf)){
			echo '商户尚未开通新微信支付';
			exit;
		}
		$tconf 				= (array)json_decode($tconf['pc_config']);
		return $tconf;
	}
	
	
	
	
	private function toShowWeixinPay(
				$tconf,
				$data,
				$notifyurl,
				$openid,
				$display,
				$success_url,
				$error_url
		){
		if (empty($data['orderBusiness'])
				||empty($data['orderId'])
				||empty($data['orderName'])
				||empty($data['orderPay'])
				){
			echo '参数错误，请填写商户名、订单号、订单描述、支付金额';
			exit;
		}
		$detail['payname']		= $data['orderBusiness'] ;
		$detail['id']			= $data['orderId'] ;
		$detail['total_title']	= $data['orderName'] ;
		$detail['pay_price']	= $data['orderPay'] ;
		
		//使用jsapi接口 JsApi_pub($wid)
		$jsApi = new JsApi_pub($tconf);
		$unifiedOrder = new UnifiedOrder_pub($tconf);
		$detailid = $detail['id'];
		
		$unifiedOrder->setParameter("openid","$openid");//openid
	 	$unifiedOrder->setParameter("body",mb_substr($detail['total_title'],0,40,'utf-8'));//商品描述
		$unifiedOrder->setParameter("out_trade_no","$detailid");//商户订单号
		$unifiedOrder->setParameter("total_fee",strval($detail['pay_price']*100));//总金额
		$unifiedOrder->setParameter("notify_url",$notifyurl);//通知地址
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
		
		
		
		$prepay_id = $unifiedOrder->getPrepayId();
		//=========步骤3：使用jsapi调起支付============
		$jsApi->setPrepayId($prepay_id);
		$jsApiParameters = $jsApi->getParameters();
		//echo $jsApiParameters;
		
		
		$this->assign('jsApiParameters',$jsApiParameters);
		$this->assign('detail',$detail);
		$this->assign('success_url',$success_url);
		$this->assign('error_url',$error_url);
		$this->display($display);
	}
	
	
	
	public function showpay(){

		if(I('request.id')&&I('request.wid')&&I('request.token')){
			session('id',I('request.id'));//订单ID
			session('wid',I('request.wid'));//店铺微信ID
			session('token',I('request.token'));//用户token
		}

		$detail 														= M('app_vshop_order')->where(array('id'=>session('id')))->find();
		if (!$detail['uid']||$detail['state']!=0||$detail['wid']!=session('wid')) {
			echo '参数错误';
			exit;
		}
		$user 														= M('app_vshop_user')->where(array('wid'=>session('wid'),'token'=>session('token')))->find();
		if ($user['id']!=$detail['uid']){
			echo 'token错误';
			exit;
		}
		$tconf 														= M('payment')->where("pid='".session('wid')."' and pc_type='weipaynew' and pc_enabled=1")->find();
		if (empty($tconf)){
			echo '商户尚未开通新微信支付';
			exit;
		}
		$detail['payname']									= $tconf['pc_name'];
		$tconf 														= (array)json_decode($tconf['pc_config']);
		
		/**
		 * JS_API支付
		 * ====================================================
		 * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。
		 * 成功调起支付需要三个步骤：
		 * 步骤1：网页授权获取用户openid
		 * 步骤2：使用统一支付接口，获取prepay_id
		 * 步骤3：使用jsapi调起支付
		 */

		//使用jsapi接口 JsApi_pub($wid)
		$jsApi = new JsApi_pub($tconf);

		//=========步骤1：网页授权获取用户openid============
		//通过code获得openid
		if (!isset($_GET['code']))
		{
			//触发微信返回code码
			$url = $jsApi->createOauthUrlForCode(C('HTTP_DOMIN').'newpay/showpay');
			Header("Location: $url");
		}else
		{
			//获取code码，以获取openid
			$code = $_GET['code'];
			$jsApi->setCode($code);
			$openid = $jsApi->getOpenId();
		}
		//=========步骤2：使用统一支付接口，获取prepay_id============
		//使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub($tconf);

		/*$unifiedOrder->setParameter("openid","$openid");//openid
		 $unifiedOrder->setParameter("body",$detail['total_title']);//商品描述
		$unifiedOrder->setParameter("out_trade_no",$detail['id']);//商户订单号
		$unifiedOrder->setParameter("total_fee",$detail['total_price']);//总金额
		$unifiedOrder->setParameter("notify_url",C('HTTP_DOMIN')."newpay/wx_shop_notify");//通知地址
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型*/

		$detailid = $detail['id'];
		$total_price = $detail['total_price'];
		$notifyurl = C('HTTP_DOMIN')."newpay/wx_shop_notify";
		$unifiedOrder->setParameter("openid","$openid");//openid
		$unifiedOrder->setParameter("body",mb_substr($detail['total_title'],0,40,'utf-8'));//商品描述
		$unifiedOrder->setParameter("out_trade_no","$detailid");//商户订单号
		$unifiedOrder->setParameter("total_fee",strval($detail['pay_price']*100));//总金额
		$unifiedOrder->setParameter("notify_url",C('HTTP_DOMIN').'newpay/wx_vshop_notify');//通知地址
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
		
		//dump($unifiedOrder);exit;
		
		$prepay_id = $unifiedOrder->getPrepayId();
		
		//=========步骤3：使用jsapi调起支付============
		$jsApi->setPrepayId($prepay_id);
		$jsApiParameters = $jsApi->getParameters();
		//echo $jsApiParameters;
		$this->assign('jsApiParameters',$jsApiParameters);
		$this->assign('detail',$detail);
		$this->display('Vapp@Pay:newwxpay');
	}

	public function wx_vshop_notify(){
		vendor('Newwxpay.WxPayPubHelper');
		//使用通用通知接口
		$notify = new Notify_pub();
		//存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];

		$notify->saveData($xml);

		//获取商户信息
		$detail 							= M('app_vshop_order')->where("id='".$notify->data['out_trade_no']."'")->find();
		$tconf 							= M('payment')->where("pid='".$detail['wid']."' and pc_type='weipaynew'")->find();
		$tconf 							= (array)json_decode($tconf['pc_config']);

		//验证签名，并回应微信。
		//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
		//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
		//尽可能提高通知的成功率，但微信不保证通知最终能成功。
		if($notify->checkSign($tconf) == FALSE){
			$notify->setReturnParameter("return_code","FAIL");//返回状态码
			$notify->setReturnParameter("return_msg","签名失败");//返回信息
		}else{
			$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
		}
		$returnXml = $notify->returnXml();
		echo $returnXml;

		//以数据库形式记录回调信息
		$notifydata['xml'] = $xml;
		$notifydata['etime'] = time();
		$notifydata['notify'] = $returnXml;
		M('log_newweixin_notify')->data($notifydata)->add();

		if($notify->checkSign($tconf) == TRUE)
		{
			if ($notify->data["return_code"] == "FAIL") {
				//此处应该更新一下订单状态，商户自行增删操作
				$notifydata['xml'] = "【通信出错】:\n".$xml."\n";
				$notifydata['etime'] = time();
				M('log_newweixin_notify')->data($notifydata)->add();
			}
			elseif($notify->data["result_code"] == "FAIL"){
				//此处应该更新一下订单状态，商户自行增删操作
				$notifydata['xml'] = "【通信出错】:\n".$xml."\n";
				$notifydata['etime'] = time();
				M('log_newweixin_notify')->data($notifydata)->add();
			}
			else{
				//此处应该更新一下订单状态，商户自行增删操作
				$notifydata['xml'] = "【支付成功】:\n".$xml."\n";
				$notifydata['etime'] = time();
				M('log_newweixin_notify')->data($notifydata)->add();

				$order 			= M('app_vshop_order')->where(array('id'=>$notify->data["out_trade_no"]))->find();
				M('app_vshop_order')->where("id='".$notify->data["out_trade_no"]."'")->save(array('state'=>1,'etime'=>time(),'paytime'=>time(),'trade_no'=>$notify->data['transaction_id'],'openid'=>$notify->data['openid']));

				//更新操作日志
				$ldata['orderid'] 		= $notify->data["out_trade_no"];
				$ldata['action']		= 2;
				$ldata['auth'] 			= '买家';
				$ldata['ctime']			= time();
				M('app_vshop_order_log')->add($ldata);

				//更新支付日志
				$pdata['orderid']		= $notify->data["out_trade_no"];
				$pdata['payment']		= 1;
				$pdata['paytype']		= 1;
				$pdata['price']			= $notify->data["total_fee"] ;
				$pdata['ctime']			= time();
				M('app_vshop_order_pay_log')->add($pdata);

				//更新产品销量、库存
				$odetails 				= M('app_vshop_order_detail')->where(array('orderid'=>$notify->data["out_trade_no"]))->select();
				foreach ($odetails as $ov){
					if ($ov['spec_id']>0){
						M('app_vshop_spec')->where(array('id'=>$ov['spec_id'],'pid'=>$ov['productid']))->setDec('maxnum',$ov['num']);
					}
					M('app_vshop_product')->where(array('id'=>$ov['productid']))->setDec('maxnum',$ov['num']);
					M('app_vshop_product')->where(array('id'=>$ov['productid']))->setInc('salenum',$ov['num']);
				}
				//异步通知日志
				$notifylog['notifys']		= json_encode($_POST);
				$notifylog['ctime']			= time();
				M('app_vshop_notify_log')->add($notifylog);
			}
			echo 'success';
			exit;
		}
		//商户自行增加处理流程,
		//例如：更新订单状态
		//例如：数据库操作
		//例如：推送支付完成信息
		echo 'fail';
		exit;
	}
}