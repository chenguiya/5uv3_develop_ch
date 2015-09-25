<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
$uid = intval($_G['uid']);
if (submitcheck('uploadsubmit')) {
	$data = array(
		'uid' => $uid,
		'dateline' => TIMESTAMP,	
	);
	
	if ($_FILES['banner']) {
		$upload = new discuz_upload();
		$picid = TIMESTAMP;
		$upload->init($_FILES['banner'], 'forum', $picid, $uid);
		$upload->save();
		
		
		$data['banner'] = $upload->attach['attachment'];	//取原件
	}	
	
	if (checkbannerexist($uid)) {
		C::t('#rights#plugin_rights_personal_banner')->update($uid, $data['banner']);
		showmessage('设置成功', 'plugin.php?id=rights:rightsrun&ac=setpersonalpic');
	} else {
		if (C::t('#rights#plugin_rights_personal_banner')->insert($data, true)) {
			showmessage('设置成功', 'plugin.php?id=rights:rightsrun&ac=setpersonalpic');
		} else {
			showmessage('设置失败');
		}
	}
}
$banner = C::t('#rights#plugin_rights_personal_banner')->fetch($uid);
// var_dump($banner);die;

function checkbannerexist($uid) {
	if (C::t('#rights#plugin_rights_personal_banner')->fetch($uid)) {
		return true;
	} else {
		return false;
	}
}