<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
global $_G;

require_once libfile('function/extends');
include_once DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php';

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
}elseif ($_GET['type'] == 'activity'){
                        //xurui 2015-10-14 参照活动的显示做修改
                    //$query = DB::query("SELECT COUNT(*) AS count FROM ".DB::table('forum_thread')." WHERE  fid = $fid and special = 4");

                   // $result = DB::fetch($query);
                   // $count = intval($result['count']);
                    
                    $pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 9;
                    
                    $maxpage = @ceil($count/$pagesize);
                    $nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);
                    // $multipage_more = "forum.php?mod=activity&page=".$nextpage;

                    $start = ($page - 1) * $pagesize;
                    $limit = " LIMIT ".$start.','.$pagesize;
                    //$fids =DB::fetch_all("SELECT DISTINCT authorid FROM ".DB::table('forum_thread')." WHERE fid = $fid and special = 4  ORDER BY dateline DESC");
                    
                   // foreach ($fids as $v) {
    
                            $activitylists = DB::fetch_all("SELECT a.* FROM ".DB::table('forum_activity')." as  a , ".DB::table('forum_thread')." as b  WHERE  b.fid = $fid and b.special = 4 and a.tid = b.tid  ORDER BY a.starttimefrom DESC".$limit);
                            foreach ($activitylists as $key => $activity) {
                                        $activitylists[$key]['starttimefrom'] = date('Y-m-d', $activity['starttimefrom']);
                                        if ($activity['starttimeto'] && $activity['starttimeto'] >= TIMESTAMP) {
                                                $activitylists[$key]['status'] = true;
                                        } else {
                                                $activitylists[$key]['status'] = false;
                                        }
                                        if ($activity['starttimeto']) $activitylists[$key]['starttimeto'] = date('Y-m-d', $activity['starttimeto']);
                                        $thread = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid=".$activity['tid']);

                                        $activitylists[$key]['title'] = str_cut($thread['subject'], 57, '...');
                                        $attach = C::t('forum_attachment_n')->fetch('tid:'.$activity['tid'], $activity['aid']);

                                        if($attach['isimage']) {
                                                $activitylists[$key]['attachurl'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
 
                                                $activitylists[$key]['thumb'] = $attach['thumb'] ? getimgthumbname($activitylists[$key]['attachurl']) : $activitylists[$key]['attachurl'];

                                                $activitylists[$key]['width'] = $attach['thumb'] && $_G['setting']['thumbwidth'] < $attach['width'] ? $_G['setting']['thumbwidth'] : $attach['width'];
                                        }
                          //  }
                        }
                   // echo "<pre>";
                   // print_r($activitylists);exit;
                    $nobbname = TRUE;
                    $navtitle = '5U体育官网活动中心_';
                    if ($page > 1) $navtitle .= '第'.$page.'页_';
                    $navtitle .= $_G['setting']['bbname'];
                    $metakeywords = '活动,5U体育';
                    $metadescription = '5U体育最新网站活动中心，参与活动赢取精美礼品，活动流程与参与方式。';
                   // $multipage = fansclub_multi($maxpage, $pagesize, $page, 'fans/activity/'.$_G['forum']['fid'].'/');
                    //include template('extend/desktop/activity');
                    
}

function get_thread_cover($tid) {
	$cover = array();
	$attachments = C::t('forum_attachment')->fetch_all_by_id('tid', $tid);
	$cover = current($attachments);
	$cover = C::t('forum_attachment_n')->fetch($cover['tableid'], $cover['aid'], 1);
	return $cover;
}
