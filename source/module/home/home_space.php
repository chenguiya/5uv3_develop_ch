<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: home_space.php 33660 2013-07-29 07:51:05Z nemohou $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$dos = array('index', 'doing', 'blog', 'album', 'friend', 'wall',
	'notice', 'share', 'home', 'pm', 'videophoto', 'favorite',
	'thread', 'trade', 'poll', 'activity', 'debate', 'reward', 'profile', 'plugin', 'follow',
	'group',	//add by Daming 2015/9/21 for wap
);

$do = (!empty($_GET['do']) && in_array($_GET['do'], $dos))?$_GET['do']:'index';

if(!in_array($do, array('home', 'doing', 'blog', 'album', 'share', 'wall'))) {
	$_G['mnid'] = 'mn_common';
}
if(empty($_G['uid']) && in_array($_GET['do'], array('thread', 'trade', 'poll', 'activity', 'debate', 'reward', 'group'))) {
	showmessage('login_before_enter_home', null, array(), array('showmsg' => true, 'login' => 1));
}
$uid = empty($_GET['uid']) ? 0 : intval($_GET['uid']);

$member = array();
if($_GET['username']) {
	$member = C::t('common_member')->fetch_by_username($_GET['username']);
	if(empty($member) && !($member = C::t('common_member_archive')->fetch_by_username($_GET['username']))) {
		showmessage('space_does_not_exist');
	}
	$uid = $member['uid'];
	$member['self'] = $uid == $_G['uid'] ? 1 : 0;
}

if($_GET['view'] == 'admin') {
	$_GET['do'] = $do;
}
if(empty($uid) || in_array($do, array('notice', 'pm'))) $uid = $_G['uid'];
if(empty($_GET['do']) && !isset($_GET['diy'])) {
	
	if($_G['adminid'] == 1) {
		if($_G['setting']['allowquickviewprofile']) {
			// if(!$_G['inajax']) dheader("Location:home.php?mod=space&uid=$uid&do=profile");
			if(!$_G['inajax']) dheader("Location:home.php?mod=space&uid=$uid&do=thread&from=space");
		}
	}
	if(helper_access::check_module('follow')) {
		$do = $_GET['do'] = 'follow';
	} else {
		$do = $_GET['do'] = !$_G['setting']['homepagestyle'] ? 'profile' : 'index';
	}
} elseif(empty($_GET['do']) && isset($_GET['diy']) && !empty($_G['setting']['homepagestyle'])) {
	$_GET['do'] = 'index';
}

if($_GET['do'] == 'follow') {
	dheader("Location:home.php?mod=space&uid=$uid&do=thread&from=space");
	if($uid != $_G['uid']) {
		$_GET['do'] = 'view';
		$_GET['uid'] = $uid;
	}
	require_once libfile('home/follow', 'module');
	exit;
} elseif(empty($_GET['do']) && !$_G['inajax'] && !helper_access::check_module('follow')) {
	$do = 'profile';
}

if($uid && empty($member)) {
	$space = getuserbyuid($uid, 1);
	if(empty($space)) {
		showmessage('space_does_not_exist');
	}
    
    $member['self'] = $uid == $_G['uid'] ? 1 : 0; // 2015-08-31 zhangjh 增加对以上方法补充
    space_merge($space, 'count');
	space_merge($space, 'profile');
	space_merge($space, 'field_home');
} else {
    $member['self'] = $uid == $_G['uid'] ? 1 : 0; // 2015-08-31 zhangjh 增加对以上方法补充
    $space = &$member;
}
if(empty($space)) {
	if(in_array($do, array('doing', 'blog', 'album', 'share', 'home', 'trade', 'poll', 'activity', 'debate', 'reward', 'group'))) {
		$_GET['view'] = 'all';
		$space['uid'] = 0;
	} else {
		showmessage('login_before_enter_home', null, array(), array('showmsg' => true, 'login' => 1));
	}
} else {

	$navtitle = $space['username'];

	if($space['status'] == -1 && $_G['adminid'] != 1) {
		showmessage('space_has_been_locked');
	}

	if(in_array($space['groupid'], array(4, 5, 6)) && ($_G['adminid'] != 1 && $space['uid'] != $_G['uid'])) {
		$_GET['do'] = $do = 'profile';
	}
	
	if($do != 'profile' && $do != 'index' && !ckprivacy($do, 'view')) {
		$_G['privacy'] = 1;
		require_once libfile('space/profile', 'include');
		include template('home/space_privacy');
		exit();
	}

	if(!$space['self'] && $_GET['view'] != 'eccredit' && $_GET['view'] != 'admin') $_GET['view'] = 'me';

	get_my_userapp();

	get_my_app();
}

$diymode = 0;

list($seccodecheck, $secqaacheck) = seccheck('publish');
if($do != 'index') {
	$_G['disabledwidthauto'] = 0;
}

// 2015-07-28 zhangjh goto thread
if($do == 'profile' && $_G['inajax'] != 1 && empty($_GET['mobile']))
{
	dheader("Location:home.php?mod=space&uid=$uid&do=thread&from=space");
}

// var_dump($do);die;

require_once libfile('space/'.$do, 'include');

?>