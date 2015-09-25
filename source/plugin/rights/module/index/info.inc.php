<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
loadcache('plugin');
$_settings = $_G['cache']['plugin']['rights'];

if (!$_G['uid']) {
	showmessage('not_loggedin', NULL, array(), array('login' => 1));
}

$creditstrans = $_G['setting']['creditstrans'];		//获取后台设置的支付货币类型
$membercount = DB::fetch_first("SELECT * FROM ".DB::table('common_member_count')." WHERE uid=".$_G['uid']);
$balance = $membercount['extcredits'.$creditstrans];

if (submitcheck(buysubmit)) {
	
	$data_log = array(
		'orderid' => dgmdate(TIMESTAMP, 'YmdHis').random(18),
		'status' => 2,
		'operation' => 'BLP',
		'log_time' => TIMESTAMP,
		'price' => intval($_POST['price']),
		'uid' => $_G['uid'],
		'username' => $_G['member']['username'],
		'subject' => $_G['member']['username'].' 购买了'.$_POST['name'].'权益。',
	);
	
	$rights = C::t('#rights#plugin_rights')->fetch_rights_by_id($_POST['rightsid']);
	
// 	var_dump($rights['typeid']);die;
	
	$data = array(
		'rightsid' => $rights['rightsid'],
		'dateline' => TIMESTAMP
	);
	
	switch (intval($rights['typeid'])) {
		case 2:
		$data['idtype'] = 'clubid';
		$data['uid'] = intval($_POST['clubid']);
		break;
		
		default:
		$data['idtype'] = 'uid';
		$data['uid'] = intval($_G['uid']);
		break;
	}
	
	if ($_POST['price'] > $balance) {
		showmessage('您的钱包余额不足，请先去为钱包充值！', 'plugin.php?id=rights:index&op=buy');
	} else {
		C::t("#rights#plugin_member_rights")->insert($data);
		C::t('#ucharge#plugin_ucharge_log')->insert($data_log);
		showmessage('您已经成功购买了XX权益，轻快去体验一下吧!', 'plugin.php?id=rights:index&op=me');
	}
}

$rightsid = isset($_GET['rightsid']) ? intval($_GET['rightsid']) : showmessage('权益过期或不存在，请联系管理员!');
$info = C::t('#rights#plugin_rights')->fetch_rights_by_id($rightsid);
//如果权益为球迷会权益，获取用户加入的所有球迷会
if ($info['typeid'] == '2') {
	$clubids = $clubinfo = array();
	//获取用户加入的所有球迷会
	$clubids1 = C::t('forum_groupuser')->fetch_all_fid_by_uids($_G['uid']);
	//已购买权益的球迷会
	$clubids2 = C::t('#rights#plugin_member_rights')->fetch_all_fid_by_rightsids($rightsid);
	
	$clubids = array_diff($clubids1, $clubids2);
	foreach ($clubids as $fid) {
		$clubinfo = C::t('forum_forum')->fetch_info_by_fid($fid);
		$clublists[$fid] = $clubinfo['name'];
	}
}

$useperoid = array(
	0 => '总可以',
	1 => '间隔一天',
	2 => '间隔24小时',
	3 => '间隔一周',
	4 => '间隔一个月'
);

// var_dump($info);die;