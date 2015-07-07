<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>api</title>
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">

<!-- Loading Flat UI -->
<link href="css/flat-ui.css" rel="stylesheet">
<link href="css/demo.css" rel="stylesheet">
<script src="js/jquery-1.8.3.min.js"></script>
<script src="js/jquery-ui-1.10.3.custom.min.js"></script>
<script src="js/jquery.ui.touch-punch.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-select.js"></script>
<script src="js/bootstrap-switch.js"></script>
<script src="js/flatui-checkbox.js"></script>
<script src="js/flatui-radio.js"></script>
<script src="js/jquery.tagsinput.js"></script>
<script src="js/jquery.placeholder.js"></script>
<script src="js/bootstrap-typeahead.js"></script>
<script src="js/application.js"></script>
</head>
<body>
	<br />
	<br />
	<div class="form-group">
		<div class="alert alert-info">
			<button type="button" class="close fui-cross" data-dismiss="alert"></button>
			<p id="res"></p>
		</div>
	</div>
	<div class="demo-col" style="width: 500px">



		<div class="btn-group select select-block mbl">
			<button class="btn dropdown-toggle clearfix btn-hg btn-primary"
				data-toggle="dropdown">
				<span class="filter-option pull-left action_uri">ACTION URI</span>&nbsp;<span
					class="caret"></span>
				<input type="hidden" value="" id="action_uri" />
			</button>
			<i class="dropdown-arrow dropdown-arrow-inverse"></i>
			<ul class="dropdown-menu dropdown-inverse" role="menu"
				style="overflow-y: auto; min-height: 108px;">
				<?php 
              		foreach ($route as $k => $v){
              			if(!isset($v['params'])){
              				$v['params'] = array();
              			}
              			echo '
						<li rel="'.$k.'" class="">
							<a tabindex="-1" href="#" data-params=\''.json_encode($v['params']).'\' data-uri="'.$v['uri'].'" class="opt uris active">
								<span class="pull-left">'.$v['method'].$v['uri'].'</span>
							</a>
						</li>';
              		}
              	?>
			</ul>
			<script>
			$(function(){
				$('.uris').click(function(){
					var uri 			= $(this).data('uri');
					var text 			= $(this).find('span').html();
					var params 			= $(this).data('params');
					var paramInputs 	= $('input[name="param"]');
					var inputLog 	 	= new Array(); //可以保留的字段
					var inputRewrite 	= new Array(); //重写Element
					paramInputs.each(function(){
						var value = $.trim($(this).val());
						if(value){
							var paramName = value.split("=");
							if(params[paramName[0]] ) {
								inputLog[paramName[0]] = this;
							}else{
								inputRewrite.push(this);
							}
						}else{
							inputRewrite.push(this);
						}
					});
					var unfillParams = new Array();
					if(params){
						for(var pa in params){
							if(!inputLog[pa]){
								unfillParams.push(pa);
							}
						}
					}
					for(var v in unfillParams){
						var element = inputRewrite.shift();
						$(element).val(unfillParams[v]+'=');
					}
					for(var v in inputRewrite){
						$(inputRewrite[v]).val('');
					}
					$('.action_uri').html(text);
					$('#action_uri').val(uri);
				});
			})
			</script>
		</div>
		<div class="form-group">
			<input type="text" name="param" value="" placeholder="PARAM=VALUE"
				class="form-control input-sm">
		</div>
		<div class="form-group">
			<input type="text" name="param" value="" placeholder="PARAM=VALUE"
				class="form-control input-sm">
		</div>
		<div class="form-group">
			<input type="text" name="param" value="" placeholder="PARAM=VALUE"
				class="form-control input-sm">
		</div>
		<div class="form-group">
			<input type="text" name="param" value="" placeholder="PARAM=VALUE"
				class="form-control input-sm">
		</div>
		<div class="form-group">
			<input type="text" name="param" name="param" value=""
				placeholder="PARAM=VALUE" class="form-control input-sm">
		</div>
		<div class="form-group">
			<input type="text" name="param" value="" placeholder="PARAM=VALUE"
				class="form-control input-sm">
		</div>
		<div class="form-group">
			<input type="text" name="param" value="" placeholder="PARAM=VALUE"
				class="form-control input-sm">
		</div>
	</div>

	<div class="demo-col">
		<a href="#fakelink" id="get_sub"
			class="btn btn-lg btn-block btn-primary">GET SUBMIT</a> <a
			href="#fakelink" id="post_sub"
			class="btn btn-lg btn-block btn-primary">POST SUBMIT</a> <a
			href="#fakelink" id="reset" class="btn btn-lg btn-block btn-warning">RESET</a>
		<a href="#fakelink" id="add" class="btn btn-lg btn-block btn-default">ADD
			PARAM</a>
	</div>

	<script>
	$(function(){
		var execSubmit = function (type){
			var url = $('#action_uri').val();
			var params = $('input[name="param"]');
			var param = '';
			params.each(function(i,v){
				var vv = $.trim($(v).val());
				if(vv) param += '&' + $(v).val();
			});

			if(param) param = param.substring(1);
			$.ajax({
	             type: type,
	             url: url,
	             data: param,
	             dataType: "text",
	             success: function(data){
		             console.log(data);
		             $('#res').html(hexToDec(data));
	             },
            	error:function(){
					alert('ERROR');
                }
	         });
		}
		$('#get_sub').click(function(){
			execSubmit('get');
		});
		$('#post_sub').click(function(){
			execSubmit('post');
		});
		$('#reset').click(function(){

		});
		$('#add').click(function(){

		});

		var decToHex = function(str) {
		    var res=[];
		    for(var i=0;i < str.length;i++)
		        res[i]=("00"+str.charCodeAt(i).toString(16)).slice(-4);
		    return "\\u"+res.join("\\u");
		}
		var hexToDec = function(str) {
		    str=str.replace(/\\/g,"%");
		    return unescape(str);
		}
		
	})
	</script>
</body>
</html>
