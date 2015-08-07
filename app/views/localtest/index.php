<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>api</title>
<link href="flatUI/bootstrap/css/bootstrap.css" rel="stylesheet">

<!-- Loading Flat UI -->
<link href="flatUI/css/flat-ui.css" rel="stylesheet">
<link href="flatUI/css/demo.css" rel="stylesheet">
<script src="flatUI/js/jquery-1.8.3.min.js"></script>
<script src="flatUI/js/jquery-ui-1.10.3.custom.min.js"></script>
<script src="flatUI/js/jquery.ui.touch-punch.min.js"></script>
<script src="flatUI/js/bootstrap.min.js"></script>
<script src="flatUI/js/bootstrap-select.js"></script>
<script src="flatUI/js/bootstrap-switch.js"></script>
<script src="flatUI/js/flatui-checkbox.js"></script>
<script src="flatUI/js/flatui-radio.js"></script>
<script src="flatUI/js/jquery.tagsinput.js"></script>
<script src="flatUI/js/jquery.placeholder.js"></script>
<script src="flatUI/js/bootstrap-typeahead.js"></script>
<script src="flatUI/js/application.js"></script>
<script src="flatUI/js/inone.js"></script>
</head>
<body>
	<br />
	<br />
	<div class="form-group">
		<div class="alert alert-info">
			<button type="button" class="close fui-cross" data-dismiss="alert"></button>
			<pre id="res"></pre>
		</div>
	</div>
	<div class="demo-col" style="width: 600px">

		<div class="btn-group select select-block mbl">
			<button class="btn dropdown-toggle clearfix btn-hg btn-primary"
				data-toggle="dropdown">
				<span class="filter-option pull-left action_uri">ACTION URI</span>&nbsp;<span
					class="caret"></span> <input type="hidden" value="" id="action_uri" />
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
			var all_params = <?php echo json_encode($all_params) ?>;
			$(function(){
				console.log(all_params);
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
						$(element).val(unfillParams[v]+'='+getDefault(unfillParams[v]));
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
			<input type="text" name="param" value="" placeholder="PARAM=VALUE"
				class="form-control input-sm">
		</div>
	</div>
	
	<div class="demo-col">
		<span class="label label-info">Buttons</span>
		
	
		<a href="#fakelink" id="get_sub"
			class="btn btn-lg btn-block btn-primary">GET SUBMIT</a> <a
			href="#fakelink" id="post_sub"
			class="btn btn-lg btn-block btn-primary">POST SUBMIT</a> <a
			href="#fakelink" id="reset" class="btn btn-lg btn-block btn-warning">RESET</a>
		<a href="#fakelink" id="add" class="btn btn-lg btn-block btn-default">ADD
			PARAM</a>
		<br/>
			<div class="demo-inline demo-labels">
				<span class="label label-info">Base Url</span>
          </div>
		<div class="btn-group select select-block mbl">
			<button class="btn dropdown-toggle clearfix btn-info"
				data-toggle="dropdown">
				<span class="filter-option pull-left" id="baseuri-name" >Localhost</span>&nbsp;<span
					class="caret"></span>
				<input type="hidden" id="baseuri-hidden" value=""/>
			</button>
			<i class="dropdown-arrow"></i>
			<ul class="dropdown-menu" role="menu"
				style="overflow-y: auto; min-height: 108px;">
				<?php 
					$i = 0;
				
              		foreach ($baseUrls as $k => $v){
              			echo '
						<li rel="'.$i++.'" class="selected">
					<a tabindex="-1" href="#" data-base="'.$v.'" class="opt baseuri">
						<span class="pull-left">'.$k.'</span>
					</a>
				</li>';
              		}
              	?>
              	
              	
			</ul>
			<script>
			$(function(){
				$('.baseuri').click(function(){
					var text 			= $(this).find('span').html();
					var base 			= $(this).data('base');
					$('#baseuri-name').html(text);
					$('#baseuri-hidden').val(base);
				});
				$('.baseuri').eq(0).click();
			})
			</script>
		</div>
	</div>
	<script>

	var decomposeVariables = function(variable){
		if(typeof (variable) == "object" ){
			for (var a in variable) {
		        if (typeof (variable[a]) == "object") {
		        	decomposeVariables(variable[a]); //递归遍历
		        }
		        else {
			        if(all_params[a] && variable[a]){
			        	all_params[a] = variable[a];
					}
		        }
		    }
		}else{
			var strs = variable.split("&");
	      	for(var i = 0; i < strs.length; i ++) {
	      		var str = strs[i].split("=");
	      		if(str[1]) all_params[str[0]] = str[1];
	      	}
		}
		
	}

	var getDefault = function(name){
		return all_params[name] && all_params[name] != true ? all_params[name] : '';
	}
	
	$(function(){
		var execSubmit = function (type){
			var url = $('#action_uri').val();
			var params = $('input[name="param"]');
			var param = '';
			params.each(function(i,v){
				var vv = $.trim($(v).val());
				vv.replace(/\s/g, "");
				if(vv) param += '&' + vv;
			});
			var base = $('#baseuri-hidden').val();
			if(param) param = param.substring(1);
			if(param) decomposeVariables(param);
			$.ajax({
	            type: type,
	            url: base + url,
	            data: param,
	            dataType: "text",
	            success: function(data){
		            if(/^\{.*\}$/.test(data)){
		            	decomposeVariables(JSON.parse(data));
				    }
// 		            console.log(data);
// 		            console.log(obj);
// 		            console.log(isJson(data));

		            $('#res').html(jsl.format.formatJson(hexToDec(data)));
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
