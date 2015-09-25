<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perpage = 10;
$start = ($page -1) * $perpage;

ckstart($start, $perpage);

$grouplist = array();
$uid = isset($_GET['uid']) ? intval($_GET['uid']) : $_G['uid'];
$grouplist = C::t('forum_groupuser')->fetch_all_group_for_user($uid, 0, 0, $start, $perpage);

include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php');
foreach ($grouplist as $key => $group) {
// 	$forum = DB::fetch_first("select * from ".DB::table('forum_forum')." where fid = ".$group['fid']);
	$fields = DB::fetch_first("select * from ".DB::table('forum_forumfield')." where fid = ".$group['fid']);
// 	var_dump($forum);die;	
	$groupinfo = get_fansclub_info($group['fid']);
	$grouplist[$key]['name'] = $groupinfo['name'];
	$grouplist[$key]['icon'] = $groupinfo['icon'];
	$grouplist[$key]['membernum'] = $fields['membernum'];
	$grouplist[$key]['province_name'] = $groupinfo['province_name'];
	$grouplist[$key]['city_name'] = $groupinfo['city_name'];
	if (!empty($grouplist[$key])) {
		$special_verify = fansclub_get_level_apply_status($group['fid']);
		$grouplist[$key]['verify_org'] = $special_verify['verify_org'];
		$grouplist[$key]['verify_5u'] = $special_verify['verify_5u'];
	}
}

include_once template("diy:home/space_fans");