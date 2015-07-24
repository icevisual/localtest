<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>1.3版本优化-重新绑定手机 - Tower</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="renderer" content="webkit">
<meta name="baidu-site-verification" content="qLDoHdGnb64RHlkm">
<meta name="alexaVerifyID" content="SIgQikd9LazsFz9M1vPBaQyC4Gw">
<link
	href="flatUI/css/application.css"
	media="all" rel="stylesheet" type="text/css">
<meta content="authenticity_token" name="csrf-param">
<meta content="9dwC+PgnYepFQ0RDgEH/gLZSBuY0lMGbSF/ezMAdKEY="
	name="csrf-token">
</head>
<body class="win">
	<div class="wrapper">
		<div class="header"></div>
		<div class="container workspace simple-stack simple-stack-transition">
			<div class="page page-2 simple-pjax">
				<div class="page-inner" data-since="2015-07-23 05:34:22 UTC"
					data-creator-guid="28f8e5d867514645bb4eaa55be762e7a"
					data-project-creator="7a8721f013854cca98585726b18bc244"
					data-page-name="1.3版本优化-重新绑定手机" id="page-doc">
					<div class="doc-wrap">
						<div class="doc printable"
							data-created-at="2015-07-17 15:45:11 +0800"
							data-updated-at="2015-07-17 15:45:11 +0800">
							<h3 class="doc-title">
								<span class="document-rest">1.3版本优化-重新绑定手机</span>
							</h3>
							<div id="doc-content" class="doc-content editor-style ">
								<h3
									style="font-weight: normal; color: rgb(51, 51, 51); margin-top: 1em !important; margin-bottom: 1em !important; font-size: 1.2em !important; line-height: 1.6 !important;">
									<b>（1）获取解绑验证码</b>
								</h3>
								<ul>
									<li><b>URL</b>:/user/get_unbind_code</li>
									<li><b>METHOD</b>:POST</li>
									<li><b>params</b>:
										<ul>
											<li>uid 用户UID</li>
											<li>token&nbsp;</li>
										</ul></li>
									<li><b>返回格式</b>:</li>
								</ul>
								<pre>
									<code>{
    "status": 200,
    "message": "短信发送成功！",
    "data": []
}
<br>
									</code>
								</pre>
								<p
									style="margin-bottom: 1.5em; font-size: 16px; color: rgb(51, 51, 51); word-wrap: break-word; font-family: arial, sans-serif; line-height: 25.6000003814697px;">
									<b>（2）获取新手机绑定验证码</b><br>
								</p>
								<ul>
									<li><b>URL</b>:/user/get_rebind_code</li>
									<li><b>METHOD &nbsp;</b>POST</li>
									<li><b>params</b>:
										<ul>
											<li>uid &nbsp; &nbsp; 用户UID</li>
											<li>token&nbsp;</li>
											<li>phone 新的手机号码<br>
											</li>
										</ul></li>
									<li><b>返回格式</b>:</li>
								</ul>
								<pre>
									<code>{
    "status": 200,
    "message": "短信发送成功！",
    "data": []
}
<br>
									</code>
								</pre>
								<p
									style="margin-bottom: 1.5em; font-size: 16px; color: rgb(51, 51, 51); word-wrap: break-word; font-family: arial, sans-serif; line-height: 25.6000003814697px;">
									<b>（3）重新绑定手机</b><br>
								</p>
								<ul>
									<li><b>URL</b>:/user/rebind_phone</li>
									<li><b>METHOD</b>:POST</li>
									<li><b>params</b>:
										<ul>
											<li>uid &nbsp; &nbsp; 用户UID</li>
											<li>token&nbsp;</li>
											<li>phone 新的手机号码<br>
											</li>
											<li>unbind_code 解绑验证码<br>
											</li>
											<li>rebind_code 绑定验证码<br>
											</li>
										</ul></li>
									<li><b>返回格式</b>:</li>
								</ul>
								<pre>
									<code>{
    "status": 200,
    "message": "ok",
    "data": []
}
<br>
									</code>
								</pre>
							</div>
						</div>

					</div>

					<div class="comments streams">
						<div class="event event-common event-document-add"
							id="event-23242335"
							data-ancestor-guid="76727c2113c84402866a7757c2cb5fef"
							data-ancestor-name="国中宝接口"
							data-ancestor-url="/projects/76727c2113c84402866a7757c2cb5fef"></div>

					</div>
				</div>
			</div>
		</div>
		<div class="footer">
			© <a href="http://mycolorway.com/" target="_blank">彩程设计</a>
		</div>
	</div>
	</a>
</body>
</html>