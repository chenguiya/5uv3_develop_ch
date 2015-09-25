<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
global $_G;
$uid = intval($_G['uid']);
$action = isset($_GET['action']) ? $_GET['action'] : 'set';
if ($action == 'set') {
	$data = array(
		'uid' => $uid,
		'gid' => intval($_GET['fid']),
		'dateline' => TIMESTAMP,
	);
	$showmessage = '您的主球迷会设置成功';
	if (C::t('#userspace#plugin_user_hostclub')->insert($data)) showmessage($showmessage, 'plugin.php?id=userspace:mygroup&uid='.$uid.'&do=club&from=space');
} elseif ($action == 'cancel') {
	if ($_G['uid'] == $_G['forum']['founderuid']) {
		showmessage('group_exit_founder');
	}
	$showmessage = '主球迷会取消成功!';
		C::t('#userspace#plugin_user_hostclub')->delete(array('uid'=>$uid));
	showmessage($showmessage, 'plugin.php?id=userspace:mygroup&uid='.$uid.'&do=club&from=space');
}