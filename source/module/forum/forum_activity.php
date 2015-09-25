<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;

require_once libfile('function/extends');
include_once DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php';
if (defined('IN_MOBILE')) {
	$query = DB::query("SELECT COUNT(*) AS count FROM ".DB::table('forum_activity'));;
} else {
	$query = DB::query("SELECT COUNT(*) AS count FROM ".DB::table('forum_activity')." WHERE `uid` IN(1,295)");
}

$result = DB::fetch($query);
$count = intval($result['count']);

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 9;

if (defined('IN_MOBILE')) $pagesize = 10;

$maxpage = @ceil($count/$pagesize);
$nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);
// $multipage_more = "forum.php?mod=activity&page=".$nextpage;

$start = ($page - 1) * $pagesize;
$limit = " LIMIT ".$start.','.$pagesize;

if (defined('IN_MOBILE')) {
	$activitylists = DB::fetch_all("SELECT * FROM ".DB::table('forum_activity')." ORDER BY starttimefrom DESC".$limit);
} else {
	$activitylists = DB::fetch_all("SELECT * FROM ".DB::table('forum_activity')." WHERE `uid` IN(1,295) ORDER BY starttimefrom DESC".$limit);
}

foreach ($activitylists as $key => $activity) {
	$activitylists[$key]['starttimefrom'] = date('Y-m-d', $activity['starttimefrom']);
	if ($activity['starttimeto'] && $activity['starttimeto'] >= TIMESTAMP) {
		$activitylists[$key]['status'] = true;
	} else {
		$activitylists[$key]['status'] = false;
	}
	if ($activity['starttimeto']) $activitylists[$key]['starttimeto'] = date('Y-m-d', $activity['starttimeto']);
	$thread = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid=".$activity['tid']);
// 	var_dump($thread);die;
	$activitylists[$key]['title'] = str_cut($thread['subject'], 57, '...');
	$attach = C::t('forum_attachment_n')->fetch('tid:'.$activity['tid'], $activity['aid']);
	if($attach['isimage']) {
		$activitylists[$key]['attachurl'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
		
		if (defined('IN_MOBILE')) {
// 			$aids = wap_getattachment($thread['tid'], 2);
// 			foreach ($aids as $aid) {
				$activitylists[$key]['thumb'] = getforumimg($activity['aid'], 0, 320, 170, 2);
// 			}
		} else {
			$activitylists[$key]['thumb'] = $attach['thumb'] ? getimgthumbname($activitylists[$key]['attachurl']) : $activitylists[$key]['attachurl'];
		}
		
		$activitylists[$key]['width'] = $attach['thumb'] && $_G['setting']['thumbwidth'] < $attach['width'] ? $_G['setting']['thumbwidth'] : $attach['width'];
	}
}

// var_dump($activitylists);die;

$nobbname = TRUE;
$navtitle = '5U体育官网活动中心_';
if ($page > 1) $navtitle .= '第'.$page.'页_';
$navtitle .= $_G['setting']['bbname'];
$metakeywords = '活动,5U体育';
$metadescription = '5U体育最新网站活动中心，参与活动赢取精美礼品，活动流程与参与方式。';

if (defined('IN_MOBILE')) {
	$multipage = multi($count, $pagesize, $page, "forum.php?mod=activity");	
} else {
	$multipage = fansclub_multi($count, $pagesize, $page, 'activity_');		
}

include template('extend/desktop/activity');