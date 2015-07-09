<?php

Route::get ( '/', function () {
	return 'api.gzb.root';
} );

Route::options ( '{all}', function () {
	$response = Response::make ( '' );
	$response->header ( 'Access-Control-Allow-Origin', '*' );
	$response->header ( 'Access-Control-Allow-Methods', 'GET, PUT, POST, DELETE, HEAD, OPTIONS' );
	$response->header ( 'Access-Control-Allow-Headers', 'X-Requested-With, Origin, X-Csrftoken, Content-Type, Accept' );
	return $response;
} )->where ( 'all', '.*' );

if( class_exists('LocaltestController')){
	include __DIR__.'/helper.php';
	Route::get('localtest',   'LocalTestController@index');
	Route::get('test',   'LocalTest1Controller@run');
	Route::post ( 'get_create_code', 'LocalTestController@getCode' ); // 注册--获取验证码
}

// Redpacket相关
Route::group ( array (
		'prefix' => 'redpacket'
), function () {
	// 判断红包活动是否开启
	Route::get ( '/redpacket_status'		, 'Redpacket\RedpacketController@redpacket_status' ); 
	
	//验证用户是否已注册
	Route::get ( '/is_user_registered'		, 'Redpacket\RedpacketController@is_user_registered' );
	
	//获取支持的银行列表。。
	Route::get ( '/get_support_bank'		, 'Redpacket\RedpacketController@get_support_bank' );
	
	// 带邀请码的注册
	Route::post( '/register'				, 'Redpacket\RedpacketController@register' );
	
	// 获取我的兑换码
	Route::post ( '/get_user_code', array (
			'before' => 'uid_token',
			'uses' => 'Redpacket\RedpacketController@get_user_code' 
	) ); 
	
	// 获取红包信息
	// 检测是否开启
	Route::post ( '/get_redpacket_info', array (
			'before' => array (
					'redpacket_switch',
					'uid_token' 
			),
			'uses' => 'Redpacket\RedpacketController@get_redpacket_info' 
	) ); 
	
	// 保存用户提现信息
	//添加提交次数限制
	//根据身份证筛选适龄用户 18 - 45
	//3要素验证
	Route::post ( '/store_withdraw_info', array (
			'before' => 'uid_token',
			'uses' => 'Redpacket\RedpacketController@store_withdraw_info' 
	) );
	
	// 保存被邀请兑换码
	// 检测是否开启
	// 得到一个红包
	Route::post ( '/store_code', array (
			'before' => array (
					'redpacket_switch',
					'uid_token' 
			),
			'uses' => 'Redpacket\RedpacketController@store_code' 
	) );
	
	// 保存被邀请兑换码
	// 每日仅限一次
	// 检测是否开启
	Route::post ( '/withdraw', array (
			'before' => array (
					'redpacket_switch',
					'uid_token' 
			),
			'uses' => 'Redpacket\RedpacketController@withdraw' 
	) );
} );

Route::group(array('before' => 'crm_auth','prefix'=>'crm'), function () {
	Route::any('sms', 'Crm\SmsController@index');
});


// user相关
Route::group ( array (
		'prefix' => 'user' 
), function () {
	Route::post ( '/create', 'User\UserController@create' ); // 用户注册
	Route::post ( '/create_user_test', 'User\UserController@create_user_test' ); // 用户注册 test
	Route::post ( '/login', 'User\UserController@login' ); // 用户登陆
	Route::post ( '/get_findpass_code', 'User\UserController@get_findpass_code' ); // 找回密码--获取验证码
	Route::post ( '/get_create_code', 'User\UserController@get_create_code' ); // 注册--获取验证码
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
	Route::post ( '/task_order', 'Order\OrderController@task_order' ); // 审核订单接口
	Route::post ( '/task_payment', 'Order\OrderController@task_payment' ); // 支付宝异步测试接口
	Route::post ( '/upay_pay_req_shortcut', 'Order\OrderController@upay_pay_req_shortcut' ); // 一键支付，请求trade_no
	Route::post ( '/get_upay_ucard', 'Order\OrderController@get_upay_ucard' ); // 获取用户已经绑定的U付银行卡
	Route::post ( '/req_smsverify_shortcut', 'Order\OrderController@req_smsverify_shortcut' ); // 发送协议短信
	Route::post ( '/agreement_pay_confirm_shortcut', 'Order\OrderController@agreement_pay_confirm_shortcut' ); // 协议支付 确认支付
	Route::any ( '/uPay_Notify', 'Order\OrderController@uPay_Notify' ); // 一键支付，接收异步请求
	Route::post ( '/tranDirectReq', 'Order\OrderController@tranDirectReq' ); // U付,直连支付
} );

// lend相关
Route::group ( array (
		'prefix' => 'lend' 
), function () {
	Route::any ( '/doFraudmetrix', 'Lend\LendController@doFraudmetrix' ); // 同盾查询
	Route::any ( '/get_bank', 'Lend\LendController@get_bank' ); // 根据银行卡号获取银行信息
	Route::post ( '/che_audit_order', 'Lend\LendController@che_audit_order' ); // 检查用户正在审核的订单
	Route::post ( '/che_audit_credit_task', 'Lend\LendController@che_audit_credit_task' ); // 检查用户正在审核的手工额度提升
	Route::post ( '/che_audit_credit_order_task', 'Lend\LendController@che_audit_credit_order_task' ); // 检查用户正在审核的手工额度提升+检查用户正在审核的订单
	Route::post ( '/check_imput_user', 'Lend\LendController@check_imput_user' ); // 手工导入数据做四要素黑名单验证
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


//鹰眼
Route::group ( array ('prefix' => 'eagleeye'), function () {

    Route::group ( array ('prefix' => 'user'), function () {
    	
        Route::get ( '/all', 'Eagleeye\User\InfoController@all' );
    });

});
