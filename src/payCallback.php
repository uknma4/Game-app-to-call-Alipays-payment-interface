<?php
//这是签名验证串，在 App配置 查询或重置
$secretKey = "XXXXXXXXXXXXXXXXXXXXXXXXXX";
//发送通知时的 Unix 时间戳，可用于处理通知过期策略
$timestamp = intval($_POST["timestamp"]);
//假定15分钟内有效
if (($timestamp + 15 * 60) < date_timestamp_get(date_create())) {
    //通知已过期
    return;
}
$kv = array();
//遍历 form 表单
foreach ($_POST as $key => $val) {
    $kv[$key] = $val;
}
ksort($kv);
reset($kv);
$param = '';
foreach ($kv AS $key => $val) {
    if ($key != 'sign') {
        $param .= "$key=$val&";
    }
}
$param = substr($param, 0, -1).$secretKey;
$verify_result = (md5($param) == strtolower($kv["sign"]));
if ($verify_result) {
    //获取有效的参数值
    $tradeid = $kv["tradeid"];  //支付平台上的交易号
    $orderid = $kv["orderid"];  //订单号
    $amount  = $kv["amount"] ;  //支付金额
    $channel = $kv["channel"];  //支付渠道, 0 微信，1 支付宝, 2 银联云闪付, 3 银联全民付
    $attach  = $kv["attach"];   //原样返回发起支付时的附加字段（使用V2版通知时）
 
    //切记，一定要比对金额：
    //实付金额与订单应付金额是否相符，不相等直接退出
		$orderLenght=strlen(orderid);
    $indexpos=strpos(orderid,"-");
	$payIndex=substr(orderid,strpos+1,orderLenght-indexpos);
	// 从文件中读取数据到PHP变量 
 $json_string = file_get_contents('payItemList.json'); 
 $data = json_decode($json_string); 
 if ($data[$payIndex])
	 $dingdanjiner=$data[$payIndex]["cash"];
	 else
		exit(); 
    if ($amount != $dingdanjiner) exit();

    //用上面的参数处理支付成功的后续业务
    //请在这里自由发挥，尽量不要修改其他地方
	if (1<=$payIndex<=10)
  $.ajax({
		  url:'gmquery.php',
		  type:'post',
		  'data':{type:'charge',checknum:"123456",uid:$attach['actorID'],num:$amount,qu:$attach['qu']},
          'cache':false,
          'dataType':'json',
		  success:function(data){
			  console.log('data',data);
			  alert(data.info);
		  },
		  error:function(){
			  alert('操作失败');
		  }
	  });
	else if ($payIndex==1000)
	  $.ajax({
		  url:'gmquery.php',
		  type:'post',
		  'data':{type:'buymonthcard',checknum:"123456",uid:$attach['actorID'],qu:$attach['qu']},
          'cache':false,
          'dataType':'json',
		  success:function(data){
			  console.log('data',data);
			  alert(data.info);
		  },
		  error:function(){
			  alert('操作失败');
		  }
	  });	
	  
	  else 
	   
   $.ajax({
		  url:'gmquery.php',
		  type:'post',
		  'data':{type:'buyprivilegemonthcard',checknum:"123456",uid:$attach['actorID'],qu:$attach['qu']},
          'cache':false,
          'dataType':'json',
		  success:function(data){
			  console.log('data',data);
			  alert(data.info);
		  },
		  error:function(){
			  alert('操作失败');
		  }
	  });	
    //处理成功后，http 要返回 OK 字符，否则你的服务器可能会多次收到确认通知
    echo "OK";
} else {
    //签名验证失败
}
?>
