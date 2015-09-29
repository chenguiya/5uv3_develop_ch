<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;

include_once libfile('function/extends');
include_once DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php';

$type = isset($_GET['type']) ? trim($_GET['type']) : 'football';
$op = isset($_GET['op']) ? trim($_GET['op']) : 'index';

$forums = $_G['cache']['forums'];
foreach ($forums as $key => $forum) {
	if ($forum['type'] == 'forum') {
		$thread_fields = C::t('forum_forum')->fetch_info_by_fid($forum['fid']);
		$forums[$key]['icon'] = $thread_fields['icon'];
		$forums[$key]['threads'] = $thread_fields['threads'];
		$forums[$key]['todayposts'] = $thread_fields['todayposts'];
	}
}
//足球圈
if ($type == 'football' && $op == 'index') {
	//官方发布
	$official['data'] = C::t('common_block_item')->fetch_all_by_bid(137, true);
	//推荐版块
	$recommond_forum['data'] = C::t('common_block_item')->fetch_all_by_bid(139, true);
	foreach ($recommond_forum['data'] as $key => $forum) {
		$recommond_forum['data'][$key]['fields'] = dunserialize($forum['fields']);
	}
	//推荐球迷会
	$recommond_fansclub['data'] = C::t('common_block_item')->fetch_all_by_bid(141, true);
	foreach ($recommond_fansclub['data'] as $key => $fansclub) {
		$recommond_fansclub['data'][$key]['fields'] = dunserialize($fansclub['fields']);
	}
	//热门活动
	$hot_activity['data'] = C::t('common_block_item')->fetch_all_by_bid(143, true);
	foreach ($hot_activity['data'] as $key => $value) {
		$activity = DB::fetch_first("SELECT * FROM ".DB::table('forum_activity')." WHERE tid=".$value['id']);
		$hot_activity['data'][$key]['starttimefrom'] = date('Y-m-d', $activity['starttimefrom']);
		if ($activity['starttimeto']) $hot_activity['data'][$key]['starttimeto'] = date('Y-m-d', $activity['starttimeto']);
		if (!empty($activity['starttimeto'])) {
			if ($activity['starttimeto'] > TIMESTAMP) {
				$hot_activity['data'][$key]['status'] = true;
			} else {
				$hot_activity['data'][$key]['status'] = false;
			}
		} else {
			$hot_activity['data'][$key]['status'] = true;
		}			
	}
// 	var_dump($hot_activity['data']);die;
	
	//获取西甲、英超、德甲、意甲、法甲和中超的所有版块id
	$football_fids = array();
	foreach ($_G['cache']['forums'] as $forum) {
		$groupid = get_groupid_by_fid($forum['fid']);
		if (in_array($groupid, array(1,54,64,81,82,185))) {
			$football_fids[] = $forum['fid'];
		}
	}
	
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 10;
	
	$mem_check = memory('check'); // 先检查缓存是否生效
	$allthreadlist = $threadlist = FALSE;
	if($mem_check != '') // 有搜索的不用缓存
	{
		$count = memory('get', 'int_circle_football_threadlist_count');
		$threadlist = memory('get', 'arr_circle_football_threadlist_'.$page);
		$maxpage = memory('get', 'int_circle_football_threadlist_maxpage');
	}
	
	if($count == FALSE || $threadlist == FALSE || $maxpage == FALSE)
	{
		$count = C::t('forum_thread')->count_by_fid_typeid_displayorder($football_fids, null, '>0');
		$maxpage = @ceil($count/$pagesize);
		$nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);
		$multipage = fansclub_multi($count, $pagesize, $page, 'football', '_', '/');
		
		$start = ($page - 1) * $pagesize;
		$threadlist = C::t('forum_thread')->fetch_all_by_fid_displayorder($football_fids, '>0', null, null, $start, $pagesize);
		foreach ($threadlist as $key => $thread) {
			$threadlist[$key]['author_avatar'] = avatar($thread['authorid'], 'middle', 1);
			$threadlist[$key]['message'] = get_message(0, $thread['tid']);
			$threadlist[$key]['dateline'] = date('m月d日 H:s', $thread['dateline']);
		}
		
		if($mem_check != '')
		{
			memory('set', 'int_circle_football_threadlist_count', $count, 60*60*24);
			memory('set', 'arr_circle_football_threadlist_'.$page, $threadlist, 60*60*24);
			memory('set', 'int_circle_football_threadlist_maxpage', $maxpage, 60*60*24);
		}
	}
	else
	{
		$multipage = fansclub_multi($count, $pagesize, $page, 'football', '_', '/');
	}
	
	$nobbname = TRUE;
	$navtitle = '足球新闻资讯_国际足球明星图片视频_';
	if ($page > 1) {
		$navtitle .= '第'.$page.'页_';
	}
	$navtitle .= $_G['setting']['bbname'];
	$metakeywords = '足球,新闻资讯,球星';
	$metadescription = '汇聚足球明星新闻图片与视频资讯，顶级国际足球俱乐部球迷社区，深挖各俱乐部第一手资讯。';
                        //update by xurui 2015-09-25 换成球队频道
                       $a = $threadlist;
                       foreach($a as $k=>$v){
                            if($_G['cache']['forums'][$v['fid']]['type'] == 'sub'){
                                     $threadlist[$k]['fid'] = $_G['cache']['forums'][$v['fid']]['fup'];
                                }
                     }
	include template('extend/desktop/ball_index');
} elseif ($type == 'basket' && $op == 'index') {
//官方发布
	$official['data'] = C::t('common_block_item')->fetch_all_by_bid(138, true);
	//推荐版块
	$recommond_forum['data'] = C::t('common_block_item')->fetch_all_by_bid(140, true);
	foreach ($recommond_forum['data'] as $key => $forum) {
		$recommond_forum['data'][$key]['fields'] = dunserialize($forum['fields']);
	}
	//推荐球迷会
	$recommond_fansclub['data'] = C::t('common_block_item')->fetch_all_by_bid(142, true);
	foreach ($recommond_fansclub['data'] as $key => $fansclub) {
		$recommond_fansclub['data'][$key]['fields'] = dunserialize($fansclub['fields']);
	}
	//热门活动
	$hot_activity['data'] = C::t('common_block_item')->fetch_all_by_bid(144, true);
	foreach ($hot_activity['data'] as $key => $value) {
			$activity = DB::fetch_first("SELECT * FROM ".DB::table('forum_activity')." WHERE tid=".$value['id']);
			$hot_activity['data'][$key]['starttimefrom'] = date('Y-m-d', $activity['starttimefrom']);
			if ($activity['starttimeto']) $hot_activity['data'][$key]['starttimeto'] = date('Y-m-d', $activity['starttimeto']);
			if (!empty($activity['starttimeto'])) {
				if ($activity['starttimeto'] > TIMESTAMP) {
					$hot_activity['data'][$key]['status'] = true;
				} else {
					$hot_activity['data'][$key]['status'] = false;
				}
			} else {
				$hot_activity['data'][$key]['status'] = true;
			}			
		}
	$football_fids = array();
	foreach ($_G['cache']['forums'] as $forum) {
		$groupid = get_groupid_by_fid($forum['fid']);
		if (in_array($groupid, array(129,194))) {
			$football_fids[] = $forum['fid'];
		}
	}
	
	
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 10;
	
	$mem_check = memory('check'); // 先检查缓存是否生效
	$allthreadlist = $threadlist = FALSE;
	if($mem_check != '') 
	{
		$count = memory('get', 'int_circle_basket_threadlist_count');
		$threadlist = memory('get', 'arr_circle_basket_threadlist_'.$page);
		$maxpage = memory('get', 'int_circle_basket_threadlist_maxpage');
	}
	
	if($count == FALSE || $threadlist == FALSE || $maxpage == FALSE)
	{
		$count = C::t('forum_thread')->count_by_fid_typeid_displayorder($football_fids, null, '>0');
		$maxpage = @ceil($count/$pagesize);
		$nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);
		$multipage = fansclub_multi($count, $pagesize, $page, 'basket', '_', '/');
		
		$start = ($page - 1) * $pagesize;
		$threadlist = C::t('forum_thread')->fetch_all_by_fid_displayorder($football_fids, null, null, null, $start, $pagesize);
		foreach ($threadlist as $key => $thread) {
			$threadlist[$key]['author_avatar'] = avatar($thread['authorid'], 'middle', 1);
			$threadlist[$key]['message'] = get_message(0, $thread['tid']);
			$threadlist[$key]['dateline'] = date('m月d日 H:s', $thread['dateline']);
		}
		
		if($mem_check != '')
		{
			memory('set', 'int_circle_basket_threadlist_count', $count, 60*60*24);
			memory('set', 'arr_circle_basket_threadlist_'.$page, $threadlist, 60*60*24);
			memory('set', 'int_circle_basket_threadlist_maxpage', $maxpage, 60*60*24);
		}
	}
	else
	{
		$multipage = fansclub_multi($count, $pagesize, $page, 'basket', '_', '/');
	}
	
	$nobbname = TRUE;
	$navtitle = 'NBA篮球新闻视频_NBA球星球队战报_';
	if ($page > 1) {
		$navtitle .= '第'.$page.'页_';
	}
	$navtitle .= $_G['setting']['bbname'];
	$metakeywords = 'nba,篮球,视频,球星';
	$metadescription = '精选NBA战报和篮球视频，NBA球星八卦新闻，篮球明星最新资讯。';
	 
                       $a = $threadlist;
                       foreach($a as $k=>$v){
                            if($_G['cache']['forums'][$v['fid']]['type'] == 'sub'){
                                     $threadlist[$k]['fid'] = $_G['cache']['forums'][$v['fid']]['fup'];
                                }
                     }
	include template('extend/desktop/ball_index');
} elseif ($type == 'football' && $op == 'allcategory') {
	//当前论坛所有足球类的分区的id
	$groupid_arr = array(1,54,64,81,82,185);	
	$all_forums = array();
	foreach ($forums as $key => $forum) {
		if (in_array($forum['fup'], $groupid_arr)) {
			$all_forums[$forum['fup']]['fields'] = $forums[$forum['fup']];
			$all_forums[$forum['fup']]['sub'][] = $forum;
		}
	}
	
	$nobbname = TRUE;
	$navtitle = '足球联赛热门球队球迷会_'.$_G['setting']['bbname'];
	$metakeywords = '足球,球迷,俱乐部';
	$metadescription = '五大足球联赛热门球队球迷会，加入球迷会，支持你所喜爱的球队。';
	
	include template('extend/desktop/ball_channel');	
} elseif ($type == 'basket' && $op == 'allcategory') {
	//当前论坛所有篮球类的分区的id
	$groupid_arr = array(129,194);
	$all_forums = array();
	foreach ($forums as $key => $forum) {
		if (in_array($forum['fup'], $groupid_arr)) {
			$all_forums[$forum['fup']]['fields'] = $forums[$forum['fup']];
			$all_forums[$forum['fup']]['sub'][] = $forum;
		}
	}
	
	$nobbname = TRUE;
	$navtitle = 'NBA球队球迷会_CBA篮球队_'.$_G['setting']['bbname'];
	$metakeywords = 'nba,篮球,球迷会,CBA';
	$metadescription = '汇集NBA30支东西部球队和CBA冠军球队的球迷会，篮球迷的聚集地。';
	
	include template('extend/desktop/ball_channel');
}



/**
 * 获取分区id
 * @param number $fid
 * @return number
 */
function get_groupid_by_fid($fid) {
	global $_G;
	$forums = $_G['cache']['forums'];
	switch ($forums[$fid]['type']) {
		case 'forum':
			$groupid = intval($forums[$fid]['fup']);
		break;
		
		case 'sub':
			$groupid = get_groupid_by_fid($forums[$fid]['fup']);
		break;
				
		default:
			$groupid = $fid;
		break;
	}
	return $groupid;
}
