<?php
Route::get ( '/', function () {
	return 'api.gzb.root';
} );


	

/**
 * V1.0
 * 
 */
Route::options ( '{all}', function () {
	$response = Response::make ( '' );
	$response->header ( 'Access-Control-Allow-Origin', '*' );
	$response->header ( 'Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, HEAD, OPTIONS' );
	$response->header ( 'Access-Control-Allow-Headers', 'X-Requested-With, Origin, X-Csrftoken, Content-Type, Accept' );
	return $response;
} )->where ( 'all', '.*' );


if( class_exists('LocaltestController')){
		include __DIR__.'/helper.php';
		Route::get('risk'				, 'Crm\RiskController@index');
		Route::get('localtest'			, 'LocalTestController@index');
		Route::get('document'			, 'LocalTestController@generate_api_doc');
		Route::get('test'				, 'GeneralTestController@test');
		Route::get('generate'			, 'GeneralTestController@generate');
		Route::post( 'get_create_code'	, 'GeneralTestController@getCode' ); // 注册--获取验证码
	}



// Redpacket相关
Route::group ( array (
		'prefix' => 'redpacket' 
), function () {
	Route::get ( '/group_fraudmetrix'	, 'Redpacket\RedpacketController@group_fraudmetrix' );// group_fraudmetrix
	Route::get ( '/redpacket_status'	, 'Redpacket\RedpacketController@redpacket_status' );// 判断红包活动是否开启
	Route::get ( '/is_user_registered'	, 'Redpacket\RedpacketController@is_user_registered' );// 验证用户是否已注册
	Route::get ( '/get_support_bank'	, 'Redpacket\RedpacketController@get_support_bank' );// 获取支持的银行列表。。
	Route::get ( '/get_redpacket_amount', 'Redpacket\RedpacketController@get_redpacket_amount' );// 获取红包金额
	Route::post ( '/register'			, 'Redpacket\RedpacketController@register' );// 带邀请码的注册
	Route::post ( '/get_user_code'		, array (
			'before' => 'uid_token',
			'uses' => 'Redpacket\RedpacketController@get_user_code' 
	) );// 获取我的兑换码
	Route::post ( '/get_redpacket_info'	, array (
			'before' => array (
					'uid_token' 
			),
			'uses' => 'Redpacket\RedpacketController@get_redpacket_info' 
	) );// 获取红包信息,检测是否开启
	Route::post ( '/store_withdraw_info', array (
			'before' => 'uid_token',
			'uses' => 'Redpacket\RedpacketController@store_withdraw_info' 
	) );// 保存用户提现信息,添加提交次数限制,根据身份证筛选适龄用户 18 - 45,3要素验证
	Route::post ( '/store_code', array (
			'before' => array (
					'redpacket_switch',
					'uid_token' 
			),
			'uses' => 'Redpacket\RedpacketController@store_code' 
	) );// 保存被邀请兑换码,检测是否开启,得到一个红包
	Route::post ( '/withdraw'			, array (
			'before' => array (
					'uid_token' 
			),
			'uses' => 'Redpacket\RedpacketController@withdraw' 
	) );// 保存被邀请兑换码,每日仅限一次,检测是否开启
	Route::any ( '/tranDirectReq_Notify', 'Redpacket\RedpacketController@tranDirectReq_Notify' );// 直连支付回调处理
} );

Route::group ( array (
		'before' => 'crm_auth',
		'prefix' => 'crm' 
), function () {
	Route::any ( 'sms', 'Crm\SmsController@index' );
	Route::post ( '/financial/repayment', 'Crm\FinancialController@repayment' ); // 还款信息
	Route::post ( '/financial/receivable', 'Crm\FinancialController@receivable' ); // 应收账款
	Route::post ( '/financial/overdueRate', 'Crm\FinancialController@overdueRate' ); // 逾期率
	Route::post('/financial/withholding', 'Crm\FinancialController@withholding'); //代扣管理
	Route::post('/financial/withholding_binding', 'Crm\FinancialController@withholding_binding'); //代扣管理
} );

// user相关
Route::group ( array (
		'prefix' => 'user' 
), function () {
	Route::get ( '/phone_home', 'User\UserController@phone_home' ); // 手机号码归属地完善
	Route::post ( '/create', 'User\UserController@create' ); // 用户注册
	Route::post ( '/create_user_test', 'User\UserController@create_user_test' ); // 用户注册 test
	Route::post ( '/get_user_code', 'User\UserController@get_user_code' ); // 手工找回用户验证码
	Route::post ( '/login', 'User\UserController@login' ); // 用户登陆
	Route::post ( '/get_findpass_code', array (
			'uses' => 'User\UserController@get_findpass_code' 
	) ); // 找回密码--获取验证码
	Route::post ( '/get_create_code', array (
			'uses' => 'User\UserController@get_create_code' 
	) ); // 注册--获取验证码
	Route::post ( '/get_unbind_code', array (
			'uses' => 'User\UserController@get_unbind_code'
	));// 解绑--获取验证码
	Route::post ( '/get_rebind_code', array (
			'uses' => 'User\UserController@get_rebind_code'
	)); // 重新绑定--获取验证码
	Route::post ( '/req_change_phone', 'User\UserController@req_change_phone' ); // 检测用户是否可以更换手机号码
	Route::post ( '/rebind_phone', 'User\UserController@rebind_phone' ); // 重新绑定
	Route::post ( '/find_password', 'User\UserController@find_password' ); // 找回密码
	Route::post ( '/get_credit', 'User\UserController@get_credit' ); // 获取用户积分，分期额度
	Route::post ( '/check_user_credit_task', 'User\UserController@check_user_credit_task' ); // 获取用户最新升级额度信息
	Route::post ( '/create_userinfo', 'User\UserController@create_userinfo' ); // 保存用户信息
	Route::post ( '/check_double', 'User\UserController@check_double' ); // 判断用户信息是否重复
	Route::post ( '/create_user_pic', 'User\UserController@create_user_pic' ); // 单独上传审核照片
	Route::post ( '/put_credit', 'User\UserController@put_credit' ); // 手工提升用户额度
	Route::post ( '/put_credit_order', 'User\UserController@put_credit_order' ); // 订单提交+提升用户额度
	Route::post ( '/get_user_info', 'User\UserController@get_user_info' ); // 获取用户信息
	Route::post ( '/get_user_holding', 'User\UserController@get_user_holding' ); // 获取代扣信息
	Route::post ( '/get_user_bank_info', 'User\UserController@get_user_bank_info' ); // 获取代扣用户，银行资料
	Route::post ( '/get_credit_task', 'User\UserController@get_credit_task' ); // 获取用户升级审核列表
	Route::post ( '/withholding_bank_card', 'User\UserController@withholding_bank_card' ); // 提交代扣银行卡
	Route::post ( '/user_fraudmetrix', 'User\UserController@user_fraudmetrix' ); // 用户风控检测
} );

// order相关
Route::group ( array (
		'prefix' => 'order' 
), function () {
	Route::post ( '/put_credit', 'Order\OrderController@put_credit' ); // 订单提交
	Route::post ( '/get_pay_task', 'Order\OrderController@get_pay_task' ); // 请求还款流水
	Route::post ( '/get_orderlist', 'Order\OrderController@get_orderlist' ); // 获取用户订单列表
	Route::post ( '/getPayList', 'Order\OrderController@getPayList' ); // 请求当月还款列表
	Route::any ( '/repayment', 'Order\OrderController@repayment' ); // 异步还款接口
	Route::any ( '/check_pay_task', 'Order\OrderController@check_pay_task' ); // 按时检查用户是否逾期
	Route::any ( '/bank_withholding', 'Order\OrderController@bank_withholding' ); // 银行代扣业务
	Route::any ( '/set_overdue_sms', 'Order\OrderController@set_overdue_sms' ); // 检查即将逾期的用户并发送短信
	Route::any ( '/repayment', 'Order\OrderController@repayment' ); // 异步还款接口
	Route::post ( '/repayment_hend', 'Order\OrderController@repayment_hend' ); // 手工还款接口
	Route::post ( '/show_pay_task', 'Order\OrderController@show_pay_task' ); // 手工还款接口
	Route::post ( '/task_order', 'Order\OrderController@task_order' ); // 审核订单接口
	Route::post ( '/upay_pay_req_shortcut', 'Order\OrderController@upay_pay_req_shortcut' ); // 一键支付，请求trade_no
	Route::post ( '/get_upay_ucard', 'Order\OrderController@get_upay_ucard' ); // 获取用户已经绑定的U付银行卡
	Route::post ( '/req_smsverify_shortcut', 'Order\OrderController@req_smsverify_shortcut' ); // 发送协议短信
	Route::post ( '/agreement_pay_confirm_shortcut', 'Order\OrderController@agreement_pay_confirm_shortcut' ); // 协议支付 确认支付
	Route::any ( '/uPay_Notify', 'Order\OrderController@uPay_Notify' ); // 一键支付，接收异步请求
	Route::post ( '/tranDirectReq', 'Order\OrderController@tranDirectReq' ); // U付,直连支付
	Route::get ( '/task_payment', 'Order\OrderController@task_payment' ); // 支付宝异步测试接口
	Route::post ( '/locat', 'Order\OrderController@locat' ); // 支付宝异步测试接口
} );

// lend相关
Route::group ( array (
		'prefix' => 'lend' 
), function () {
	
	Route::any ( '/fourFactorsAndBlackCheck', 'Lend\LendController@fourFactorsAndBlackCheck' ); // 同盾&四要素查询
	Route::any ( '/doFraudmetrix', 'Lend\LendController@doFraudmetrix' ); // 同盾查询
	Route::any ( '/get_bank', 'Lend\LendController@get_bank' ); // 根据银行卡号获取银行信息
	Route::post ( '/che_audit_order', 'Lend\LendController@che_audit_order' ); // 检查用户正在审核的订单
	Route::post ( '/che_audit_credit_task', 'Lend\LendController@che_audit_credit_task' ); // 检查用户正在审核的手工额度提升
	Route::post ( '/che_audit_credit_order_task', 'Lend\LendController@che_audit_credit_order_task' ); // 检查用户正在审核的手工额度提升+检查用户正在审核的订单
	Route::post ( '/check_imput_user', 'Lend\LendController@check_imput_user' ); // 手工导入数据做四要素黑名单验证
	Route::get ( '/verify', 'Lend\LendController@verify' ); // 获取验证码
	Route::post ( '/check_verify', 'Lend\LendController@check_verify' ); // 验证验证码
} );

// 第三方专用路由
Route::group ( array (
		'prefix' => 'third',
		'before' => 'head_agreement_encryption_v1' 
), function () {
	Route::post ( '/get_user_code', 'User\UserController@get_user_code' ); // 手工找回用户验证码
	Route::post ( '/repayment_hend', 'Order\OrderController@repayment_hend' ); // 手工还款接口
} );

Route::group ( array (
		'prefix' => 'version' 
), function () {
	Route::get ( '/all', 'Version\VersionController@all' );
	Route::get ( '/latest', 'Version\VersionController@latest' );
} );

Route::group ( array (
		'prefix' => 'area' 
), function () {
	Route::get ( '/all', 'Area\AreaController@all' );
	Route::get ( '/list', 'Area\AreaController@lists' );
} );

// 鹰眼
Route::group ( array (
		'prefix' => 'eagleeye' 
), function () {
	
	Route::group ( array (
			'prefix' => 'user' 
	), function () {
		
		Route::get ( '/all', 'Eagleeye\InfoController@all' );
	} );
	
	Route::group ( array (
			'prefix' => 'fraudmetrix' 
	), function () {	
		Route::post ( '/getuserfin', 'Eagleeye\Fraudmetrix\FraudmetrixController@getuserfin' );	
		Route::post ( '/imput_shebei', 'Eagleeye\Fraudmetrix\FraudmetrixController@imput_shebei' );
	} );
} );

// 地理位置
Route::group ( array (
		'prefix' => 'location' 
), function () {	
	// 观察上报
	Route::post ( '/Reported', 'User\ReportedController@getData' );
	// 注册上报
	Route::post ( '/RegReported', 'User\ReportedController@regReport' );
	// 获取观察
	Route::get ( '/observe', 'User\GetReportedController@getObserve' );
	// 获取注册
	Route::get ( '/reg', 'User\GetReportedController@getReg' );	
	// 上报模式
	Route::get ( '/mode', 'User\ReportModeController@home' );
} );

// 通信录
Route::group ( array (
		'prefix' => 'contacts' 
), function () {
	// 提交
	Route::post ( '/v1/submit', 'User\ReportController@contactsReport' );
} );


// 推送
Route::group ( array (
    'prefix' => 'push'
), function () {
    //后台推送
    Route::post ( '/crm', 'Crm\PushController@all' );
    Route::get( '/app', 'User\AppController@home' );


} );
	
/**
 * v1.3.1
 */
Route::group ( array (
		'prefix' => '/v1.3.1/'
), function () {

	Route::group ( array (
			'prefix' => 'redpacket'
	), function () {
		
		Route::get ( '/get_register_code'	, 'V1_3_1\Redpacket\RedpacketController@get_register_code' );
		// 不发送手机验证码， 获取注册验证码
		
		Route::post ( '/can_you_share'		, array (
				'before' => 'uid_token',
				'uses' => 'V1_3_1\Redpacket\RedpacketController@can_you_share'
		) );// 判断用户是否可以分享
		
		Route::get ( '/redpacket_status'	, 'V1_3_1\Redpacket\RedpacketController@redpacket_status' );// 判断红包活动是否开启
		Route::get ( '/is_user_registered'	, 'V1_3_1\Redpacket\RedpacketController@is_user_registered' );// 验证用户是否已注册
		Route::get ( '/get_support_bank'	, 'V1_3_1\Redpacket\RedpacketController@get_support_bank' );// 获取支持的银行列表。。
		Route::get ( '/get_redpacket_amount', 'V1_3_1\Redpacket\RedpacketController@get_redpacket_amount' );// 获取红包金额
		Route::post ( '/register'			, 'V1_3_1\Redpacket\RedpacketController@register' );// 带邀请码的注册
		Route::post ( '/get_user_code'		, array (
				'before' => 'uid_token',
				'uses' => 'V1_3_1\Redpacket\RedpacketController@get_user_code'
		) );// 获取我的兑换码
		Route::post ( '/get_redpacket_info'	, array (
				'before' => array (
						'uid_token'
				),
				'uses' => 'V1_3_1\Redpacket\RedpacketController@get_redpacket_info'
		) );// 获取红包信息,检测是否开启
		Route::post ( '/store_withdraw_info', array (
				'before' => 'uid_token',
				'uses' => 'V1_3_1\Redpacket\RedpacketController@store_withdraw_info'
		) );// 保存用户提现信息,添加提交次数限制,根据身份证筛选适龄用户 18 - 45,3要素验证
		Route::post ( '/withdraw'			, array (
				'before' => array (
						'uid_token'
				),
				'uses' => 'V1_3_1\Redpacket\RedpacketController@withdraw'
		) );// 保存被邀请兑换码,每日仅限一次,检测是否开启
	} );

	// user相关
	Route::group ( array (
			'prefix' => 'user'
	), function () {
		Route::post ( '/get_findpass_code', array (
				'before' => 'check_verify',
				'uses' => 'User\UserController@get_findpass_code'
		) ); // 找回密码--获取验证码
		Route::post ( '/get_create_code', array (
				'before' => 'check_verify',
				'uses' => 'User\UserController@get_create_code'
		) ); // 注册--获取验证码
		Route::post ( '/get_unbind_code', array (
				'before' => 'check_verify',
				'uses' => 'User\UserController@get_unbind_code'
		));// 解绑--获取验证码
		Route::post ( '/get_rebind_code', array (
				'before' => 'check_verify',
				'uses' => 'User\UserController@get_rebind_code'
		)); // 重新绑定--获取验证码
	} );

} );
	
