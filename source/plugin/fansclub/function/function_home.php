<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 通过主题id获取附件列表
 * @param int $tid
 * @return array $images
 */
function get_attachment($tid) {
	$attachment = $images = array();
	
	$attachment = C::t('#fansclub#plugin_forum_attachment')->get_attachment_by_tid($tid);
	foreach ($attachment as $vo) {
		$images[] = C::t('forum_attachment_n')->fetch($vo['tableid'], $vo['aid']);
	}
	return $images;
}

function get_thread_message($tid) {
	$post = array();
	$post = C::t('#fansclub#plugin_forum_post')->fetch_message_by_tid($tid);
}

/**
 * 获取帖子的阅读量和回复量
 * @param int $tid
 * @return array $data
 */
function get_thread_data($tid) {
	$data = $threadinfo = $forum = array();
	$threadinfo = C::t('forum_thread')->fetch_all_by_tid($tid);
	
	$data['views'] = $threadinfo['views'];
	$data['replies'] = $threadinfo['replies'];
	$forum = C::t('forum_forum')->fetch_all_info_by_fids($threadinfo['fid']);	
	$data['fid'] = $forum[$threadinfo['fid']]['fid'];
	$data['forum_name'] = $forum[$threadinfo['fid']]['name'];
	$data['status'] = $forum[$threadinfo['fid']]['status'];
	return $data;
}
/**
 * 判断帖子是否为当前球迷会
 * @param int $tid
 * @param int $gid
 * @return boolean
 */
function get_add_credits($tid, $gid) {
	$log = array();
	if ($log = DB::fetch_first("SELECT * FROM ".DB::table('plugin_fans_credit_log')." WHERE tid=".$tid." AND gid=".$gid)) {
		return $log['credits'];
	} else {
		return false;
	}
}

function create_video_html($tid) {
	$matches = array();
	$postinfo = DB::fetch_first("SELECT * FROM ".DB::table('forum_post')." WHERE tid=".$tid." AND first=1");
	$pattern = '/\[media=swf,(?P<width>\d+),(?P<height>\d*)\](?P<playurl>.*)\[\/media\]/';
	
	preg_match($pattern, $postinfo['message'], $matches);
	
	return $matches['playurl'];

}
