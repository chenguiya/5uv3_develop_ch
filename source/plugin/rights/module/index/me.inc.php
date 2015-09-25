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
$uid = intval($_G['uid']);
$count = C::t('#rights#plugin_member_rights')->count($uid);
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 2;

$starlimit = ($page - 1)*$pagesize;
$myrights = $myrightsids = array();
$myrightsids = C::t('#rights#plugin_member_rights')->fetch_rightsid_by_uid($uid, $starlimit, $pagesize);
foreach ($myrightsids as $key => $value) {
	$rights = C::t('#rights#plugin_rights')->fetch_rights_by_id($value['rightsid']);
	$myrights[$key] = $rights;
	if ($rights['regdate'] > TIMESTAMP) {
		$myrights[$key]['status'] = 1;
	} elseif ($rights['canceldate'] < TIMESTAMP) {
		$myrights[$key]['status'] = -1;
	} else {
		$myrights[$key]['status'] = 0;
	}

	$myrights[$key]['regdate'] = date('Y/m/d', $rights['regdate']);
	$myrights[$key]['canceldate'] = date('Y/m/d', $rights['canceldate']);
}

// 	var_dump($myrights);die;

$nultipage = multi($count, $pagesize, $page, 'plugin.php?id=rights:index&op=me');