<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
require_once libfile('function/extends');
include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php');
// echo 111;die;
$clubid = isset($_GET['fid']) ? intval($_GET['fid']) : showmessage('club not exist', 'group.php');
$forums = &$_G['cache']['forums'];
$newfansclubids = $newfansclub = $fansclub = array();

$fields = DB::fetch_first("select * from ".DB::table('forum_forumfield')." where fid = ".intval($clubid));
$newfansclubids = array_filter(explode(',', $fields['relatedgroup']));
foreach ($newfansclubids as $gid) {
	$fansclubinfo = C::t('#fansclub#plugin_fansclub_info')->fetch($gid); // table_plugin_fansclub_apply_log
	if ($fansclubinfo['displayorder'] >= 0) {
		$fansclub[$gid] = get_fansclub_info($gid);
		if (!empty($fansclub[$gid])) {
			$special_verify = fansclub_get_level_apply_status($gid);
			$fansclub[$gid]['verify_org'] = $special_verify['verify_org'];
			$fansclub[$gid]['verify_5u'] = $special_verify['verify_5u'];
		}
	}
}

foreach ($forums as $key => $forum) {
	if ($forum['fup'] == $clubid) {
		$fields = DB::fetch_first("select * from ".DB::table('forum_forumfield')." where fid = ".intval($key));
		$newfansclubids = array_filter(explode(',', $fields['relatedgroup']));
		foreach ($newfansclubids as $gid) {
			$fansclubinfo = C::t('#fansclub#plugin_fansclub_info')->fetch($gid); // table_plugin_fansclub_apply_log
			if ($fansclubinfo['displayorder'] >= 0) {
				$fansclub[$gid] = get_fansclub_info($gid);			
				if (!empty($fansclub[$gid])) {
					$special_verify = fansclub_get_level_apply_status($gid);
					$fansclub[$gid]['verify_org'] = $special_verify['verify_org'];
					$fansclub[$gid]['verify_5u'] = $special_verify['verify_5u'];
				}
			}
		}		
	}
}
// var_dump($fansclub);
echo json_encode(array_filter($fansclub));
exit;
?>