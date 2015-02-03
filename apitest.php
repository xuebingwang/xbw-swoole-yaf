<?php
/**
 * json数据转数组，避免空的数据异常
 * @param string $json
 * @return multitype:|Ambigous <multitype:, mixed>
 */
function json_to_array($json) {
    if (! is_string ( $json )) {
        return array ();
    }
    $value = json_decode ( $json, TRUE );
    return $value ? $value : array ();
}
/**
 * CURL发送请求
 *
 * @param string $url            
 * @param mixed $data            
 * @param string $method            
 * @param string $cookieFile            
 * @param array $headers            
 * @param int $connectTimeout            
 * @param int $readTimeout            
 */
function curlRequest($url, $data = '', $method = 'POST', $cookieFile = '', $headers = array(), $connectTimeout = 30, $readTimeout = 30) {
    $headers = array_merge ( $headers, array (
            "Content-Type:application/json;charset=UTF-8" 
    ));
    
    $method = strtoupper ( $method );
    
    $option = array (
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => $connectTimeout,
            CURLOPT_TIMEOUT => $readTimeout 
    );
    
    if ($headers) {
        $option [CURLOPT_HTTPHEADER] = $headers;
    }
    
    if ($cookieFile) {
        $option [CURLOPT_COOKIEJAR] = $cookieFile;
        $option [CURLOPT_COOKIEFILE] = $cookieFile;
    }
    
    if ($data && $method == 'POST') {
        $option [CURLOPT_POST] = 1;
        $option [CURLOPT_POSTFIELDS] = $data;
    }
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt_array ( $ch, $option );
    $response = curl_exec ( $ch );
    
    if (curl_errno ( $ch ) > 0) {
        return curl_error ( $ch );
    }
    curl_close ( $ch );
    return $response;
}

if ($_POST) {
    $content = curlRequest ( $_POST ['url'], $_POST ['data'] );
    echo $content;
//     $encode = mb_detect_encoding($content);
//     var_dump($encode);
    die ();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<title>接口测试</title>
<meta name="viewport"
	content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta charset="UTF-8">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="expires" content="0">
<link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css"rel="stylesheet">
</head>
<body>
	<div class="panel panel-default">
		<div class="panel-heading">接口测试</div>
		<div class="panel-body">
			<div class="form-group">
				<label for="exampleInputEmail1">模块名</label> 
				<select class="form-control" id="module">
					<option value="">-----请选择-----</option>
					<option value="0">APP</option>
				</select>
			</div>
			<div class="form-group">
				<label for="exampleInputEmail1">接口名</label> 
				<select class="form-control" id="api-name">
					<option value="">-----请选择-----</option>
				</select>
			</div>
			<div class="form-group">
				<label for="apiurl">接口地址</label> 
				<input type="text" class="form-control" id="apiurl" />
			</div>
			<div class="form-group">
				<label for="send-data">发送数据</label>
				<textarea id="send-data" class="form-control" rows="3"></textarea>
			</div>
			<div class="form-group">
				<label for="resp-data">响应数据</label>
				<textarea id="resp-data" class="form-control" rows="4"></textarea>
			</div>
			<button type="button" id="send-btn" class="btn btn-primary btn-lg btn-block">提交发送</button>
		</div>
	</div>

	<script src="http://libs.baidu.com/jquery/2.0.3/jquery.min.js"></script>
	<script>
        $(document).ready(function(){

            var data = [];
            data[0] = [];
            data[0].push(
                        [
                         'app/public/login',
                         '用户登录',
                         '{"username":"testxbw","password":"123456"}'
                        ]
                    );

            $("#module,#apiurl,#send-data,#resp-data").val('');

            var api_sel = $("#api-name");

            var url     = 'http://192.168.28.129:9501/';
            
            $("#module").change(function(){
                api_sel.html('<option value="">-----请选择-----</option>');
                if(data[this.value] != null){
                    $.each(data[this.value],function(k,item){
                        var option = $('<option value="'+item[0]+'">'+item[1]+'</option>');
                        option.data('json',item[2]);
                        api_sel.append(option);
                    });
                }
            })
            
            api_sel.change(function(){
                var api_name = this.value;
                if(api_name != ''){
                    $("#apiurl").val(api_name.indexOf('http://') == 0 ? api_name : url+api_name);
                    $("#send-data").val($('#api-name option:selected').data('json'));
                }else{

                    $("#apiurl").val('');
                    $("#send-data").val('');
                }
            })

            $("#send-btn").click(function(){

                var url = $("#apiurl").val();
                var data = $("#send-data").val();
                if(url == ''){
                    return false;
                }
                var self = $(this);
                self.text('请稍后...').prop('disabled',true);
                $.post('api.php',{url:url,data:data}).done(function(resp){

                    $("#resp-data").val(resp);
                }).always(function (){

                    self.text('提交发送').prop('disabled',false);
                });
            })
            
        })
        </script>
</body>
</html>
