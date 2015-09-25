<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
//获取会员详细资料
require_once libfile('function/spacecp');
if (!$_G['uid']) {
	showmessage('not_loggedin', NULL, array(), array('login' => 1));
}
space_merge($space, 'count');
space_merge($space, 'field_home');
space_merge($space, 'field_forum');
space_merge($space, 'profile');
space_merge($space, 'status');

$creditstrans = $_G['setting']['creditstrans'];		//获取后台设置的支付货币类型

loadcache('forums');
$forum_option = '<option value="0">全部</option>';
if (empty($_G['cache']['forums'])) $_G['cache']['forums'] = array();
// var_dump($_G['cache']['forums']);
foreach ($_G['cache']['forums'] as $fid => $forum) {
	if ($forum['type'] != 'group') {
		$forum_option .= '<option value="'.$fid.'">'.$forum['name'].'</option>';
	}
}

if ($_GET['do'] == 'buy') {
	$rightsid = isset($_GET['rightsid']) ? intval($_GET['rightsid']) : showmessage('请选择您要购买的权益', '', 'error');

	$price = 0;
	$query = C::t('#rights#plugin_rights')->fetch_rights_by_id($rightsid);

	if ($query['price'] > $space['extcredits'.$creditstrans]) {
		showmessage('你的U币余额不去，请及时充值！');
	} else {
		if ($query['typeid'] == 3) {
			if (submitcheck('buysubmit')) {
				$fid = intval($_POST['clubfid']);
				updatemembercount($_G['uid'], array($creditstrans => "-$price"), true, 'BUR', $rightsid);
				$buy_data = array(
						'uid' => $fid,
						'rightsid' => $rightsid,
						'num' => 0,
						'dateline' => TIMESTAMP,
				);
				C::t('#rights#plugin_member_rights')->insert($buy_data);
// 				$temclub = C::t('forum_forum')->fetch_info_by_fid($fid);
// 				C::t('forum_forum')->update(array('fid' => $fid), array('name' => '<span style="color:red">'.$temclub['name'].'</span>'));
				showmessage('购买成功', 'home.php?mod=spacecp&ac=plugin&id=rights:rights');
			} else {
				$clubids = $clubinfo = array();
				//获取用户加入的所有球迷会
				$clubids1 = C::t('forum_groupuser')->fetch_all_fid_by_uids($_G['uid']);
				//已购买权益的球迷会
				$clubids2 = C::t('#rights#plugin_member_rights')->fetch_all_fid_by_rightsids($rightsid);
				
				$clubids = array_diff($clubids1, $clubids2);
				foreach ($clubids as $fid) {
					$clubinfo[] = C::t('forum_forum')->fetch_info_by_fid($fid);
				}
				include template('rights:clubs');
			}
			exit;
		} else{
			updatemembercount($_G['uid'], array($creditstrans => "-$price"), true, 'BUR', $rightsid);
			$buy_data = array(
					'uid' => $_G['uid'],
					'rightsid' => $rightsid,
					'num' => 0,
					'dateline' => TIMESTAMP,
			);
			C::t('#rights#plugin_member_rights')->insert($buy_data);			
			showmessage('购买成功', 'home.php?mod=spacecp&ac=plugin&id=rights:rights');
		}		
	}
}
$rights_type = array('通用', '权益包', '版块', '群组', '商品', '会员');
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
$perpage = max(20, empty($_GET['perpage'])) ? 20 : intval($_GET['perpage']);

$rightslists = array();
$start = ($page - 1)*$perpage;
$export_url[] = 'start='.$start;

$url = 'home.php?mod=spacecp&ac=plugin&id=rights:rightsbuy&perpage='.$perpage;
$count = C::t('#rights#plugin_rights')->count();
if ($count) {
	$multipage = multi($count, $perpage, $page, $url, 0, 3);
	$rightslists = C::t('#rights#plugin_rights')->fetch_all_by_where('1', $start, $perpage);
	if ($rightslists) {
		foreach ($rightslists as $key => $rights) {
			$rightslists[$key]['typeid'] = (int)$rights['typeid'];
			$rightslists[$key]['regdate'] = date('Y-m-d', $rights['regdate']);
			$rightslists[$key]['canceldate'] = date('Y-m-d', $rights['canceldate']);
			if ($rights['typeid'] == 1) {
				$rightslists[$key]['packinfo'] = dunserialize($rights['packinfo']);
				$rightslists[$key]['packtdstr'] = '';
				foreach ($rightslists[$key]['packinfo'] as $info) {
					$rightslists[$key]['packtdstr'] .= '<td>'.$info['name'].'</td>';
				}
			}			
			if (checkrights($_G['uid'], $rights['rightsid'])) {
				$rightslists[$key]['buystatus'] = 1;
			} else {
				$rightslists[$key]['buystatus'] = 0;
			}
			
		}
	}
}
// var_dump($rightslists);die;


function checkrights($uid, $rightsid) {
	$time = TIMESTAMP;
	if (C::t('#rights#plugin_member_rights')->fetch_rights_buy($uid, $rightsid)) {
		return true;
	} else {
		return false;
	}

}