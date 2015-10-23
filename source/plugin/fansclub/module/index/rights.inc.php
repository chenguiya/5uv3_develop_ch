<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

global $_G;

// if (empty($_G['uid'])) {
// 	showmessage('not_loggedin', '', '', array('login' => 1));
// }

$goods_list = array(
		1 => array (
				'goodsid' => 1,
				'gname' => '幽月球迷会T恤',
// 				'thumb' => '/template/usportstyle/common/images/demo001.png',
				'description' => '幽月球迷会曼市德比助威T恤，“曼市德比，蓝月升起”',
				'type' => 1,
				'realprice' => '0.01',
				'marketprice' => '199',
				'size' => array (
						'S',
						'M',
						'L' 
				),
				'supplier' => '广州市晌网文化传播有限公司' 
		),
// 		2 => array (
// 				'goodsid' => 2,
// 				'gname' => 'C罗签名球衣',
// 				'thumb' => '/template/usportstyle/common/images/demo002.png',
// 				'description' => '5U体育icons独家代理阿根廷主场世界杯版梅西亲笔签名球衣相关的签名产品我们都是拥有完善的认证以及证书的，相信你收到产品后，里面也有相关证书，认证证书上有防伪标签，以作保障。',
// 				'type' => 1,
// 				'realprice' => '0.01',
// 				'marketprice' => '1037',
// 				'supplier' => '广州市晌网文化传播有限公司' 
// 		),
// 		3 => array (
// 				'goodsid' => 3,
// 				'gname' => '皇马官方见面会入场券兑换码',
// 				'thumb' => '/template/usportstyle/common/images/demo003.png',
// 				'description' => '5U体育icons独家代理阿根廷主场世界杯版梅西亲笔签名球衣相关的签名产品我们都是拥有完善的认证以及证书的，相信你收到产品后，里面也有相关证书，认证证书上有防伪标签，以作保障。',
// 				'type' => 2,
// 				'realprice' => '0.01',
// 				'marketprice' => '450',
// 				'supplier' => '广州市晌网文化传播有限公司' 
// 		) 
);

//获取当前用户地址信息
$uid = intval($_G['uid']);
$address = DB::fetch_first("SELECT * FROM ".DB::table('dc_mall_address')." WHERE uid=".$uid);
if (submitcheck('paysubmit')) {	
	if (empty($_POST['size'])) {
		showmessage('请先选取你要购买商品的size，以免造成不必要的麻烦！', 'plugin.php?id=fansclub&ac=rights&type=detail&gid='.$_POST[gid].'&mobile=2');
	}	
	
	$data = array(
		'uid' => $uid,
		'goodsid' => intval($_POST['gid']),
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
		'attach' => $_POST['size'],
		'goods_tag' => '',
		'realname' => $_POST['realname'],
		'mobile' => $_POST['mobile'],
		'address' => $_POST['address'],
		'extdata' => serialize(array(
						'goodsid' => $_POST['gid'],
						'gname' => $_POST['gname'],
						'size' => $_POST['size'],
						'color' => $_POST['color'],
						'realname' => $_POST['realname'],
						'mobile' => $_POST['mobile'],
						'address' => $_POST['address'],
						'state' => 0,
					)),
		'backurl' => urlencode($_G['siteurl'].'plugin.php?id=fansclub&ac=rights&type=detail&gid='.$_POST['gid'].'&mobile=2'),
	);
	//用户地址信息处理
	if (!$address) {
		seva_address($data);
	} elseif ($_POST['addressedit']) {
		update_address($uid, $data);
	}
		
	buy($data);
	exit();
} elseif ($_GET['op'] == 'callback') {		
	//根据返回信息写数据
	$orderid = trim($_GET['oid']);
	$status = ($_GET['flag'] == '1') ? 2 : 0;	
	//更新系统自带表forum_order	
	C::t('forum_order')->update($orderid, array('status' => $status));
	//更新自定义表plugin_ucharge_log
	C::t('#ucharge#plugin_ucharge_log')->update($orderid, array('status' => $status));	
	$showmessage = $_GET['flag'] ? '支付成功' : '支付失败';
	showmessage($showmessage);
} elseif ($_GET['type'] == 'orderlist') {
	$order = array();
	$order = DB::fetch_first("SELECT * FROM ".DB::table('plugin_ucharge_log')." WHERE orderid='".$_GET['orderid']."'");
	$order['extdata'] = unserialize($order['body']);
} elseif ($_GET['type'] == 'index') {
} elseif ($_GET['type'] == 'list') {
	
} elseif ($_GET['type'] == 'detail') {
	$gid = intval($_GET['gid']);
	$goods = $goods_list[$gid];
}

//生成微信支付的URL
function creat_wx_payurl(&$orderid, $data) {
	$returnurl = 'http://wx.5usport.com/index.php/wxpay/Native?';

	$orderid = 'RGB-'.dgmdate(TIMESTAMP, 'YmdHis').random(5);
	$args = array(
		'oid' => $orderid,
		'body' => $data['body'],
		'total_fee' => $data['price'],
		'attach' => $data['attach'],
		'goods_tag' => $data['goods_tag'],
		'backurl' => $data['backurl'],
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
		$data_new['body'] = $data['extdata'];
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
	$address_data['tel'] = $data['mobile'];
	$address_data['default'] = '0';
	
	C::t('#dc_mall#dc_mall_address')->insert($address_data);
}
//更新用户信息
function update_address($data) {
	//判断用户之前是否已经保存过信息
	$address_data = array();	
	$uid = intval($data['uid']);
	$address_data['realname'] = $data['realname'];
	$address_data['address'] = $data['address'];
	$address_data['tel'] = $data['mobile'];
	$address_data['default'] = '0';
	
	C::t('#dc_mall#dc_mall_address')->updatebyuid($uid, $address_data);
}


include_once DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php';