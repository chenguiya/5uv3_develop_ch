<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
global $_G;
loadcache('plugin');

$fid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
$page = isset($_GET['page']) && intval($_GET['page']) > 0 ? intval($_GET['page']) : 1;

if ($_GET['ajax'] == 1) {
	$fid = isset($_GET['fid']) ? intval($_GET['fid']) : showmessage('球迷会不存在');
	$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'lastpost';
	$orderby = isset($_GET['orderby']) ? trim($_GET['orderby']) : 'lastpost';
	
	$count = C::t('#fansclub#plugin_forum_thread')->count_by_fid_fileter($fid);
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 10;
	$limit = ($page-1)*$pagesize . ',' . $pagesize;
	
	$limit = ($page-1) * $pagesize . ',' . $pagesize;
	$threadlist = array();
	include_once DISCUZ_ROOT.'./source/plugin/fansclub/function/function_home.php';
	foreach (($threadlist = C::t('#fansclub#plugin_forum_thread')->fetch_thread_by_fid_filter($fid, $filter, $orderby, $limit)) as $key => $val) {
		$attachment = get_attachment($val['tid']);
		$threadlist[$key]['attachment'] = $attachment;
		$threadlist[$key]['avatar'] = avatar($val['authorid'], 'small', 1);
	}
	echo json_encode($threadlist);
	exit;
} elseif ($_GET['type'] == 'pic') {
	
	// zhangjh 2015-06-26 参照频道图片的显示做修改
	$arr_pic_list = C::t('#fansclub#plugin_forum_thread')->fetch_list_attachment($fid, 20, 2, 0); // 默认取20张
	$arr_pic_list = fansclub_get_video_list($arr_pic_list);
	$arr_pic_num = count($arr_pic_list);
} elseif ($_GET['type'] == 'video') {
	
	// zhangjh 2015-06-26 参照频道视频的显示做修改
	$limit = 16; // 每页16条
	$arr_video_list = C::t('#fansclub#plugin_forum_thread')->fetch_list_attachment($fid, $limit, 3, ($page - 1) * $limit, 'tid');
	
	$arr_video_list = fansclub_get_video_list($arr_video_list);
	$arr_video_count = C::t('#fansclub#plugin_forum_thread')->fetch_list_attachment($fid, 0, 3);
	$video_num = intval($arr_video_count['num']);
	
	$multipage = fansclub_multi($video_num, $limit, $page, 'fans/video/'.$_G['forum']['fid'].'/');
} 

function get_thread_cover($tid) {
	$cover = array();
	$attachments = C::t('forum_attachment')->fetch_all_by_id('tid', $tid);
	$cover = current($attachments);
	$cover = C::t('forum_attachment_n')->fetch($cover['tableid'], $cover['aid'], 1);
	return $cover;
}
