<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

global $_G;

if (empty($_G['uid'])) {
	showmessage('not_loggedin', '', '', array('login' => 1));
}

//获取当前用户地址信息
$address = C::t('#dc_mall#dc_mall_address')->getbyuid(intval($_G['uid']));

if (submitcheck('paysubmit')) {		
	$data = array(
		'uid' => isset($_G['uid']) ? intval($_G['uid']) : 0,
		'username' => isset($_G['member']['username']) ? trim($_G['member']['username']) : 'guest'.random(5),
		'price' => $_POST['realprice'],
		'subject' => (isset($_G['member']['username']) ? trim($_G['member']['username']) : 'guest'.random(5)).' - '.$_POST['gname'].'购买',
// 		'oid' => 'RB-'.$_POST['gid'].'-'.dgmdate(TIMESTAMP, 'YmdHis'),
		'body' => $_POST['gname'],
		'notify_url' => $_G['siteurl'].'api/trade/notify_ucharge_vip.php',
		'return_url' => $_G['siteurl'].'api/trade/notify_ucharge_vip.php',
		'show_url' => $_G['siteurl'],
		'timestamp' => TIMESTAMP,
		'clientip' => $_G['clientip'],
		'email' => $_G['member']['email'],
		'attach' => '',
		'goods_tag' => '',
	);
		
	buy($data);
	exit();
} elseif ($_GET['op'] == 'callback') {	
	//根据返回信息写数据
	$orderid = $_GET['orderid'];
	$status = $_GET['flag'] ? 2 : 0;
	//更新系统自带表forum_order
	C::t('forum_order')->update($orderid, array('status' => $status));
	//更新自定义表plugin_ucharge_log
	C::t('#ucharge#plugin_ucharge_log')->update($orderid, array('status' => $status));
	
	$showmessage = $_GET['flag'] ? '支付成功' : '支付失败';
	showmessage($showmessage);
}

//生成微信支付的URL
function creat_wx_payurl(&$orderid, $data) {
	$returnurl = 'http://wx.5usport.com/index.php/wxpay/Native?';

	$orderid = 'RGB-'.dgmdate(TIMESTAMP, 'YmdHis').random(15);
	$args = array(
		'oid' => $orderid,
		'body' => $data['body'],
		'total_fee' => $data['price'],
		'attach' => $data['attach'],
		'goods_tag' => $data['goods_tag']
	);
	foreach ($args as $key => $vo) {
		$returnurl .= '&'.$key.'='.$vo;
	}
	return $returnurl;
}

//购买权益/商品，些订单数据后跳转支付URL
function buy($data) {
	$orderid = '';
	$requesturl = creat_wx_payurl($orderid, $data);
		
	if (!empty($orderid)) {
		//写系统自带表forum_order
		$data_new = array();
		$data_new['orderid'] = $orderid;
		$data_new['status'] = '1';
		$data_new['buyer'] = $data['username'];
		$data_new['uid'] = $data['uid'];
		$data_new['amount'] = '1';
		$data_new['price'] = $data['price'];
		$data_new['submitdate'] = $data['timestamp'];
		$data_new['email'] = $data['email'];
		$data_new['ip'] = $data['clientip'];
		C::t('forum_order')->insert($data_new);
		
		//写自定义表plugin_ucharge_log
		$data_new = array();
		$data_new['orderid'] = $orderid;
		$data_new['status'] = '1'; //1:初始状态, 2 ：支付成功， 0：支付失败
		$data_new['log_time'] = $data['timestamp'];
		$data_new['amount'] = '1';
		$data_new['price'] = $data['price'];
		$data_new['uid'] = $data['uid'];
		$data_new['fid'] = $data['fid'];
		$data_new['charge_type'] = $data['charge_type'];
		$data_new['username'] = $data['username'];
		$data_new['email'] = $data['email'];
		$data_new['ip'] = $data['clientip'];
		$data_new['subject'] = $data['subject'];
		$data_new['body'] = $data['body'];
		$data_new['seller_email'] = $data['seller_email'];;
		$data_new['notify_url'] = $data['notify_url'];
		$data_new['return_url'] = $data['return_url'];
		$data_new['show_url'] = $data['show_url'];
		$data_new['bill_type'] = '3';	//1:积分充值 2:VIP充值 3:权益购买
		C::t('#ucharge#plugin_ucharge_log')->insert($data_new);
		        
        dheader("Location:".$requesturl);
	}
}

//保存用户信息此处使用原有商城表 pre_dc_mall_address
function seva_address($data) {
	//判断用户之前是否已经保存过信息
	$address_data = array();	
	$address_data['uid'] = $data['uid'];
	$address_data['realname'] = $data['realname'];
	$address_data['address'] = $data['address'];
	$address_data['del'] = $data['mobile'];
	$address_data['default'] = '0';
	
	C::t('#dc_mall#dc_mall_address')->insert($address_data);
}
//更新用户信息
function function_name($data) {
	//判断用户之前是否已经保存过信息
	$address_data = array();	
	$uid = intval($data['uid']);
	$address_data['realname'] = $data['realname'];
	$address_data['address'] = $data['address'];
	$address_data['del'] = $data['mobile'];
	$address_data['default'] = '0';
	
	C::t('#dc_mall#dc_mall_address')->updatebyuid($uid, $address_data);
}


include_once DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php';