<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
loadcache('plugin');

$fid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;

require_once libfile('function/feed');
require_once libfile('function/home', 'plugin/fansclub');

if(empty($_G['setting']['feedhotday'])) {
	$_G['setting']['feedhotday'] = 2;
}

$minhot = $_G['setting']['feedhotmin']<1?3:$_G['setting']['feedhotmin'];
/**
 *	球迷会logo：icon
 *	球迷会横幅：banner
 *	球迷会名称：name
 *	球迷会成员数：membernum
 *	今日发帖数：todaypost
 *	总帖数：threads
 */
$clubinfo = C::t('forum_forum')->fetch_info_by_fid($fid);

// var_dump($clubinfo);
// echo $fid;
// die;

//获取球迷会球迷成员
$allfanslist = C::t('forum_groupuser')->groupuserlist($fid);

if(empty($_GET['order'])) {
	$_GET['order'] = 'dateline';
}

if (empty($_GET['do'])) {
	$_GET['do'] = 'all';
}

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 20;

// ckstart($start, $perpage);
$uids = array();
foreach ($allfanslist as $fans) {
	$uids[] = $fans['uid'];
}
$struids = implode(',', $uids);
if ($_GET['do'] == 'all') {
	$where = "uid IN (" . $struids . ")";
} elseif ($_GET['do'] == 'hot') {
	$where = "uid IN (" . $struids . ") AND hot!=0";
}
if ($_GET['ajax'] == 1) {	

$feedlist = C::t('#fansclub#plugin_home_feed')->fetch_feedlist($where, $page, $pagesize, $_GET['order']);	
	
	foreach ($feedlist as $key => $feed) {
		switch ($feed['icon']) {
			case 'thread':
				if (!empty($feed['image_1'])) {
					$feedlist[$key]['attachment'] = get_attachment($feed['id']);
					$feedlist[$key]['credits'] = 3;
				} else {					
					$swf = create_video_html($feed['id']);
					$feedlist[$key]['video'] = '<embed src="'.$swf.'" quality="high" width="240" height="200" align="middle" allowScriptAccess="always" allowFullScreen="true" mode="transparent" type="application/x-shockwave-flash"></embed>';
					$feedlist[$key]['credits'] = 5;
				}
				$body_data = dunserialize($feed['body_data']);
				$feedlist[$key]['title_template'] = $body_data['subject'];
				$feedlist[$key]['message'] = $body_data['message'];
				$feedlist[$key]['data'] = get_thread_data($feed['id']);
				break;
			case 'album':
				$feedlist[$key]['title_template'] = '发布相册';
				$feedlist[$key]['credits'] = 6;
				break;
			case 'blog':
				$feedlist[$key]['title_template'] = '发布日志';
				$feedlist[$key]['credits'] = 2;
				break;
			
			case 'friend':
				$touser = dunserialize($feed['title_data']);
				$feedlist[$key]['title_template'] = $feed[username].'和'.$touser['touser'].'成为了好友';
				break;
			
			default:
				$feedlist[$key] = $feed;
				break;
		}		
		
	}
	echo json_encode($feedlist);
	exit;
} else {
	$feedlist = C::t('#fansclub#plugin_home_feed')->fetch_feedlist($where, $page, $pagesize, $_GET['order']);
	foreach ($feedlist as $key => $feed) {
		switch ($feed['icon']) {
			case 'thread':
				if (!empty($feed['image_1'])) {
					$feedlist[$key]['attachment'] = get_attachment($feed['id']);
					$feedlist[$key]['credits'] = 3;
				} else {					
					$swf = create_video_html($feed['id']);
					$feedlist[$key]['video'] = '<embed src="'.$swf.'" quality="high" width="240" height="200" align="middle" allowScriptAccess="always" allowFullScreen="true" mode="transparent" type="application/x-shockwave-flash"></embed>';
					$feedlist[$key]['credits'] = 5;
				}
				$body_data = dunserialize($feed['body_data']);
				$feedlist[$key]['title_template'] = $body_data['subject'];
				$feedlist[$key]['message'] = $body_data['message'];
				$feedlist[$key]['data'] = get_thread_data($feed['id']);
				break;
			case 'album':
				$body_data = dunserialize($feed['body_data']);
				$feedlist[$key]['title_template'] = $body_data['album'];
				$feedlist[$key]['credits'] = 6;
				break;
			case 'blog':
				$body_data = dunserialize($feed['body_data']);
				$feedlist[$key]['title_template'] = $body_data['subject'];				
				$feedlist[$key]['credits'] = 2;
				break;
			
			case 'friend':
				$body_data = dunserialize($feed['body_data']);
				$touser = dunserialize($feed['title_data']);
				$feedlist[$key]['title_template'] = $feed[username].'和'.$touser['touser'].'成为了好友';
				break;
			
			default:
				$feedlist[$key] = $feed;
				break;
		}
	}
}