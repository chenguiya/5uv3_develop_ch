<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
if ($_G['uid'] > 0) {
	$uid = intval($_G['uid']);
} else {
	showmessage('请先登录');
}

onekeysignin();

$join_fansclub_ids = C::t('forum_groupuser')->fetch_all_fid_by_uids($uid);
var_dump($join_fansclub_ids);
exit;

/**
 * 签到操作
 */
function onekeysignin($uid, $fids) {
	global $_G;
	loadcache('plugin');
	$_setting = $_G['cache']['plugin']['k_misign'];
	foreach ($array_expression as $value) {
		;
	}
	var_dump($_setting);
	die;
}