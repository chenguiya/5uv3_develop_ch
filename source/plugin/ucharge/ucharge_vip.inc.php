<?php
// 获取插件的变量信息
$config = $_G['cache']['plugin']['ucharge'];	//获取插件的变量信息

// 一些检查
if($_G['uid'] + 0 <= 0)
{
	showmessage('请先登录', 'member.php?mod=logging&action=login');
}

if($config['charge_enable'] != 1)
{
	showmessage('VIP充值暂不开放', 'home.php?mod=spacecp&ac=credit');
}

$charge_forums = unserialize($config['charge_forums']);	// 可充值板块
$charge_types = unserialize($config['charge_types']);	// 可充值类型

$arr_forum_list = get_forum_list($charge_forums);
// echo '<pre>';
// var_dump($arr_forum_list);die;
$arr_type_list = array();

for($i = 0; $i < count($charge_types); $i++)
{
	$_str = $config['charge_vip'.$charge_types[$i]];
	$_arr = explode('|', $_str);
	$arr_type_list[$charge_types[$i]]['value'] = $_arr[0] + 0;
	$arr_type_list[$charge_types[$i]]['text'] = trim($_arr[1]);
}

// 如果用户点击提交
if($_POST['charge_submit'] == '1') 
{
	$league = $_POST['league'] + 0;
	$club = $_POST['club'] + 0;
	$star = $_POST['star'] + 0;
	$charge_type = $_POST['charge_type'] + 0;
	
	// 测试数据
	/*
	$league = 38;
	$club = 39;
	$star = 40;
	$charge_type = 1;
	*/
	
	// 错误返回URL
	$return_url = 'home.php?mod=spacecp&ac=plugin&op=credit&id=ucharge:ucharge_vip&league='.$league.'&club='.$club.'&star='.$star.'&charge_type='.$charge_type;
	
	if($league == 0)
	{
		showmessage('请选择联赛', $return_url.'&focus=league');
	}
	if($club == 0)
	{
		showmessage('请选择俱乐部', $return_url.'&focus=club');
	}
	if($charge_type == 0)
	{
		showmessage('请选择充值金额', $return_url.'&focus=charge_type');
	}
	
	$fid = $star > 0 ? $star : ($club > 0 ? $club : $league);
	// 商品详细
	$body = '';
	$body .= ($league > 0) ? get_forum_name($league) : '';
	$body .= ($club > 0) ? ' - '.get_forum_name($club) : '';
	$body .= ($star > 0) ? ' - '.get_forum_name($star) : '';
	
	// 组织参数
	$data = array();
	$data['uid'] = $_G['uid'] + 0;
	$data['username'] = $_G['member']['username'];
	$data['price'] = $arr_type_list[$charge_type]['value'];
	$data['subject'] = $_G['member']['username'].' - VIP充值';
	$data['body'] = $body.' - '.$arr_type_list[$charge_type]['text'];
    $data['notify_url'] = $_G['siteurl'].'api/trade/notify_ucharge_vip.php';
    $data['return_url'] = $_G['siteurl'].'api/trade/notify_ucharge_vip.php';
	$data['show_url'] = $_G['siteurl'];
	$data['seller_email'] = $_G['setting']['ec_account'];
	$data['timestamp'] = $_G['timestamp'];
	$data['clientip'] = $_G['clientip'];
	$data['email'] = $_G['member']['email'];
	$data['fid'] = $fid;
	$data['charge_type'] = $charge_type;
	if (!checkisbuy($data['uid'], $data['fid'])) {
		showmessage('你已经购买过此球会/球星版块的VIP身份了');
	}
	charge_vip($data);
	exit;
	//showmessage('功能测试中', $return_url);
}

// 生成支付URL和订单号
function vip_payurl(&$orderid, $data)
{
	$orderid = 'VIP'.dgmdate(TIMESTAMP, 'YmdHis').random(15);
	$args = array(
		'subject'		=> $data['subject'],
		'body'			=> $data['body'],
		'service'		=> 'trade_create_by_buyer',
		'partner'		=> DISCUZ_PARTNER,
		'notify_url'	=> $data['notify_url'],
		'return_url'	=> $data['return_url'],
		'show_url'		=> $data['show_url'],
		'_input_charset'	=> CHARSET,
		'out_trade_no'	=> $orderid,
		'price'			=> $data['price'],
		'quantity'		=> 1,
		'seller_email'	=> $data['seller_email'],
		'extend_param'	=> 'isv^dz11'
	);
	
	if(DISCUZ_DIRECTPAY) {
		$args['service'] = 'create_direct_pay_by_user';
		$args['payment_type'] = '1';
	} else {
		$args['logistics_type'] = 'EXPRESS';
		$args['logistics_fee'] = 0;
		$args['logistics_payment'] = 'SELLER_PAY';
		$args['payment_type'] = 1;
	}
	return trade_returnurl($args);
}

// 充值VIP，写数据库后跳转支付URL
function charge_vip($data)
{
	global $_G;
	if($data['seller_email'] != '')	// 设置了支付宝账号
	{
		require_once libfile('function/trade');
		$orderid = '';
		$requesturl = vip_payurl($orderid, $data);	// 生成支付宝交易用的URL
		if($orderid != '')
		{
			// 写 forum_order 表，系统自带的
			$arr_data = array();
			$arr_data['orderid'] = $orderid;
			$arr_data['status'] = '1';
			$arr_data['buyer'] = $data['username'];
			$arr_data['uid'] = $data['uid'];
			$arr_data['amount'] = '1';
			$arr_data['price'] = $data['price'];
			$arr_data['submitdate'] = $data['timestamp'];
			$arr_data['email'] = $data['email'];
			$arr_data['ip'] = $data['clientip'];
			C::t('forum_order')->insert($arr_data);
			
			// 写 pre_plugin_ucharge_log 表，自定义的
			$arr_data = array();
			$arr_data['orderid'] = $orderid;
			$arr_data['status'] = '1';
			$arr_data['log_time'] = $data['timestamp'];
			$arr_data['amount'] = '1';
			$arr_data['price'] = $data['price'];
			$arr_data['uid'] = $data['uid'];
			$arr_data['fid'] = $data['fid'];
			$arr_data['charge_type'] = $data['charge_type'];
			$arr_data['username'] = $data['username'];
			$arr_data['email'] = $data['email'];
			$arr_data['ip'] = $data['clientip'];
			$arr_data['subject'] = $data['subject'];
			$arr_data['body'] = $data['body'];
			$arr_data['seller_email'] = $data['seller_email'];;
			$arr_data['notify_url'] = $data['notify_url'];
			$arr_data['return_url'] = $data['return_url'];
			$arr_data['show_url'] = $data['show_url'];
            $arr_data['bill_type'] = '2'; // 2015-09-17 多增加一类型 1:积分充值 2:VIP充值 3:权益购买
			C::t('#ucharge#plugin_ucharge_log')->insert($arr_data);
			
			echo '<input type="hidden" id="my_data" name="my_data" value="'.$orderid.'-'.$arr_data['price'].'-'.$arr_data['uid'].'-'.$arr_data['fid'].'-'.$arr_data['charge_type'].'"">';
			echo '<form id="payform" action="'.$requesturl.'" method="post"></form>';
			echo '<script>setTimeout(function(){document.getElementById("payform").submit();}, 500);</script>';
			// echo '<script>document.getElementById("payform").submit();</script>';
		}
	}
}

// 取版块的名称
function get_forum_name($fid)
{
	global $_G;
	if(!isset($_G['cache']['forums']))
	{
		loadcache('forums');
	}
	
	$forums_cache = $_G['cache']['forums'];
	return $forums_cache[$fid]['name'];
}

// 取论坛版块数组，联动显示用
function get_forum_list($uid, $charge_forums = array())
{
	global $_G;
	$arr_return = $arr1 = $arr2 = array();
	
	if(!isset($_G['cache']['forums']))
	{
		loadcache('forums');
	}
	
	$forums_cache = $_G['cache']['forums'];
	
	foreach($forums_cache as $fid => $forum)
	{
		if($forum['status'] == 1 && (!$forum['viewperm'] && $_G['group']['readaccess']) || ($forum['viewperm'] && forumperm($forum['viewperm'])))
		{
			if($forum['type'] == 'group')
			{
				$arr_return[$fid]['name'] = $forum['name'];
				$foruminfo = C::t('forum_forum')->fetch_info_by_fid($fid);
				$arr_return[$fid]['displayorder'] = $foruminfo['displayorder'];
			}
			elseif($forum['type'] == 'forum')
			{				
				$arr_return[$forum['fup']]['list'][$fid] = array('name' => $forum['name']);
				$_tmp[$fid] = $forum['fup'];
			}
			elseif($forum['type'] == 'sub') // todo 这里显示不是很完美
			{
				if (!checkisbuy($_G['uid'], $forum['fup'])) {
					$arr_return[$_tmp[$forum['fup']]]['list'][$forum['fup']]['list'] = array();
				} else {
					$arr_return[$_tmp[$forum['fup']]]['list'][$forum['fup']]['list'][$fid] = array('name' => $forum['name']);
				}				
				
			}
		}
	}
	$arr_return[1]['list'][38]['list'][99999] = array('name' => '特里');
	
	$arr2 = array_sort($arr_return, 'displayorder', SORT_DESC);
	return $arr2;
}
/**
 * 判断选中的俱乐部/球星版块的VIP身份是否已被购买
 * @param int $uid
 * @param int $fid
 * @return boolean
 */
function checkisbuy($uid, $fid) {
	if (C::t('#ucharge#plugin_ucharge_log')->fetch($uid, $fid)) {
		return FALSE;
	} else {
		return TRUE;
	}
}

function array_sort($array, $on, $order = SORT_ASC) {
	$new_array = array();
	$sortable_array = array();
	
	if (count($array) > 0) {
		foreach ($array as $k => $vo) {		
			$sortable_array[$vo[$on]] = $k;
		}
		
		foreach ($sortable_array as $vo) {
			$new_array[$vo] = $array[$vo];
		}
	}
	return $new_array;
}
