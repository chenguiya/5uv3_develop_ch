<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function checkrightperm($perms, $id) {
	$id = $id ? intval($id) : '';
	return strexists("\t".trim($perms)."\t", "\t".trim($id)."\t") || !$perms;
}

function getrights($rightsid, $rightsnum, $uid, $fotce = 0) {
	if (C::t('#rights#plugin_member_rights')->count($uid, $rightsid)) {
		;
	} else {
		C::t('#rights#plugin_member_rights')->insert(array(
			'uid' => $uid,
			'rightsid' => $rightsid,
			'num' => $rightsnum
		));
	}
}

function userights($rightsid, $totalnum, $num = 1) {
	global $_G;
	
	if ($totalnum == $num) {
		C::t('#rights#plugin_member_rights')->delete($_G['uid'], $rightsid);
	} else {
		C::t('#rights#plugin_member_rights')->increase($_G['uid'], $rightsid, array('num' => -$num));
	}
}
/**
 * 判断用户或者群组是否有权益
 * @param string $idtype	id类型member	 会员，forum 版块，group 群组，good 商品
 * @param int $id
 * @param int $rightsid		权益id
 */
function check_member_rights($idtype = 'member', $id, $rightsid) {
	global $_G;
	if (!$id) return false;
// 	echo $id.'<br>'.$rightsid;die;
	$rights = C::t('#rights#plugin_rights')->fetch_rights_by_id($rightsid);
// 	var_dump($rights);die;
	if (C::t('#rights#plugin_member_rights')->fetch_rights_buy($id, $rightsid)) {
// 		echo 1111;die;
		if (TIMESTAMP > $rights['canceldate']) {
			C::t('#rights#plugin_member_rights')->delete($id, $rightsid);
			return false;
		} elseif (TIMESTAMP < $rights['regdate']) {
			return false;
		} else {
			return true;
		}
	} else {
// 		echo 2222;die;
		return false;
	}
}