<?php
define('IN_API', true);
define('CURSCRIPT', 'api');
define('DISABLEXSSCHECK', true);

// require '../../../class/class_core.php';
// require '../../../function/function_forum.php';

require('../../source/class/class_core.php');
require('../../source/function/function_forum.php');
require('../../source/function/function_mail.php');

// http://192.168.2.169/discuz/api/trade/notify_ucharge_vip.php?body=%E8%A5%BF%E7%94%B2+-+%E7%9A%87%E9%A9%AC+-+C%E7%BD%97+-+0.01%E5%85%83%E5%85%85%E3%80%90VIP1%E3%80%91&buyer_email=mykozhang%40gmail.com&buyer_id=2088002170369093&exterface=create_direct_pay_by_user&is_success=T&notify_id=RqPnCoPT3K9%252Fvwbh3InTva5tX1QkSnMlhRNdNBnLxCORRFQuWrOEN5Eua0iaovSfDG2k&notify_time=2015-04-02+20%3A00%3A41&notify_type=trade_status_sync&out_trade_no=VIP20150921095302l02mnzm460M5OaM&payment_type=1&seller_email=usport%40usportnews.com&seller_id=2088901870080728&subject=admin+-+VIP%E5%85%85%E5%80%BC&total_fee=0.01&trade_no=2015040200001000090047070922&trade_status=TRADE_SUCCESS&sign=ffbb8a3f200ab1c6110eda226ed713ed&sign_type=MD5
// 原来的处理 http://192.168.2.169/discuz/api/trade/notify_credit.php

$discuz = C::app();
$discuz->init();

$apitype = empty($_GET['attach']) || !preg_match('/^[a-z0-9]+$/i', $_GET['attach']) ? 'alipay' : $_GET['attach'];
include_once(DISCUZ_ROOT.'./api/trade/api_'.$apitype.'.php');
$PHP_SELF = $_SERVER['PHP_SELF'];
$siteurl_org = $_G['siteurl'];
$_G['siteurl'] = dhtmlspecialchars('http://'.$_SERVER['HTTP_HOST'].
	preg_replace("/\/+(source\/plugin\/ucharge\/api)?\/*$/i", '', substr($PHP_SELF, 0, strrpos($PHP_SELF, '/'))).'/');

$str_get = $str_post = $str_request = $str_notifydata = $str_order = '';

foreach(@$_GET as $key => $value)
{
	$str_get .= $key.'='.$value.'&';
}

foreach(@$_POST as $key => $value)
{
	$str_post .= $key.'='.$value.'&';
}

foreach(@$_REQUEST as $key => $value)
{
	$str_request .= $key.'='.$value.'&';
}
/*
$subject = '===测试通知0===';

$message = date('Y-m-d H:i:s');
$message.= "<br><br> str_get:".$str_get;
$message.= "<br><br> str_post:".$str_post;
$message.= "<br><br> str_request:".$str_request;
$add_info = "<br><br>SERVER_ADDR:".@$_SERVER['SERVER_ADDR']."<br>";
$add_info .= "SERVER_NAME:".@$_SERVER['SERVER_NAME']."<br>";
$add_info .= 'URL:http://'.@$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI']."<br>";

@sendmail('108986880@qq.com', $subject, $message.$add_info);
*/

$notifydata = trade_notifycheck('invite');
$success = '0';

foreach(@$notifydata as $key => $value)
{
	$str_notifydata .= $key.'='.$value.'&';
}

if($notifydata['validator']) {
	$orderid = $notifydata['order_no'];
	$postprice = $notifydata['price'];
	$order = C::t('forum_order')->fetch($orderid);
	
	foreach(@$order as $key => $value)
	{
		$str_order .= $key.'='.$value.'&';
	}

	if($order && floatval($postprice) == floatval($order['price']) && ($apitype == 'tenpay' || $_G['setting']['ec_account'] == $_REQUEST['seller_email'])) {
		
		if($order['status'] == 1) {
			$success = TRUE;
			// 更新附表
			$result_1 = 'orderid='.$orderid.'|status=2&confirm_time='.$_G['timestamp'].'&api_type='.$apitype.'&trade_no='.$notifydata['trade_no'].'|';
			
			/*
			$subject = '===测试通知0.5===';
			$message = date('Y-m-d H:i:s')."<br><br>";
			$message.= $success ? '充值成功' : '已经通知';
			$message.= "<br>".$success;
			
			$message.= "<br><br> str_get:".$str_get;
			$message.= "<br><br> str_post:".$str_post;
			$message.= "<br><br> str_request:".$str_request;
			$message.= "<br><br> str_notifydata:".$str_notifydata;
			$message.= "<br><br> str_order:".$str_order;
			$message.= "<br><br> result_1:".$result_1;
			
			$add_info = "<br><br>SERVER_ADDR:".@$_SERVER['SERVER_ADDR']."<br>";
			$add_info .= "SERVER_NAME:".@$_SERVER['SERVER_NAME']."<br>";
			$add_info .= 'URL:http://'.@$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI']."<br>";
			@sendmail('108986880@qq.com', $subject, $message.$add_info);
			*/
			
			$result_1 .= C::t('#ucharge#plugin_ucharge_log')->update($orderid, 
				array('status' => '2', 
					  'confirm_time' => $_G['timestamp'], 
					  'api_type' => $apitype, 
					  'trade_no' => $notifydata['trade_no']));
			
			
			// 更新主表
			$result_2 = 'orderid='.$orderid.'|status=2&buyer='.$notifydata['trade_no']."\t".$apitype.'&confirmdate='.$_G['timestamp'].'|';
			
			/*
			$subject = '===测试通知1===';
			$message = date('Y-m-d H:i:s')."<br><br>";
			$message.= $success ? '充值成功' : '已经通知';
			$message.= "<br>".$success;
			
			$message.= "<br><br> str_get:".$str_get;
			$message.= "<br><br> str_post:".$str_post;
			$message.= "<br><br> str_request:".$str_request;
			$message.= "<br><br> str_notifydata:".$str_notifydata;
			$message.= "<br><br> str_order:".$str_order;
			$message.= "<br><br> result_1:".$result_1;
			$message.= "<br><br> result_2:".$result_2;
			
			$add_info = "<br><br>SERVER_ADDR:".@$_SERVER['SERVER_ADDR']."<br>";
			$add_info .= "SERVER_NAME:".@$_SERVER['SERVER_NAME']."<br>";
			$add_info .= 'URL:http://'.@$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI']."<br>";
			@sendmail('108986880@qq.com', $subject, $message.$add_info);
			*/
			
			$result_2 .= C::t('forum_order')->update($orderid, 
				array('status' => '2', 
						'buyer' => $notifydata['trade_no']."\t".$apitype, // zhangjh 这个参数可能从2015-06-10开始有误， 2015-09-18 去掉
					  'confirmdate' => $_G['timestamp']));
					  
		}
	}
}

if($notifydata['location']) {
	if($apitype == 'tenpay') { // 现在没有tenpay
		echo <<<EOS
<meta name="TENCENT_ONLINE_PAYMENT" content="China TENCENT">
<html>
<body>
<script language="javascript" type="text/javascript">
window.location.href='$_G[siteurl]misc.php?mod=buyinvitecode&action=paysucceed&orderid=$orderid';
</script>
</body>
</html>
EOS;
	} else {
		// 完成后会跳转这个 http://192.168.2.169/discuz/source/plugin/ucharge/api/misc.php?mod=buyinvitecode&action=paysucceed&orderid=20150402193326hjqijJMYih8IciazDn
		// dheader('location: '.$_G['siteurl'].'misc.php?mod=buyinvitecode&action=paysucceed&orderid='.$orderid);
		/*
		$subject = '===测试通知2===';
		$message = date('Y-m-d H:i:s')."<br><br>";
		$message.= $success ? '充值成功' : '已经通知';
		$message.= "<br>".$success;
		
		$message.= "<br><br> str_get:".$str_get;
		$message.= "<br><br> str_post:".$str_post;
		$message.= "<br><br> str_request:".$str_request;
		$message.= "<br><br> str_notifydata:".$str_notifydata;
		$message.= "<br><br> str_order:".$str_order;
		$message.= "<br><br> result_1:".$result_1;
		$message.= "<br><br> result_2:".$result_2;
		
		$add_info = "<br><br>SERVER_ADDR:".@$_SERVER['SERVER_ADDR']."<br>";
        $add_info .= "SERVER_NAME:".@$_SERVER['SERVER_NAME']."<br>";
        $add_info .= 'URL:http://'.@$_SERVER['HTTP_HOST'].@$_SERVER['REQUEST_URI']."<br>";
		
		@sendmail('108986880@qq.com', $subject, $message.$add_info);
		*/
		
		
		showmessage($success ? '充值成功' : '已经通知', $siteurl_org.'home.php?mod=spacecp&ac=plugin&op=credit&id=ucharge:ucharge_vip');
	}
} else {
	exit($notifydata['notify']);
}
