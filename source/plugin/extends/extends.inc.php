<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

global $_G;
//神回复换一换
$action = isset($_GET['action']) ? trim($_GET['action']) : 'change';
if ($action == 'replychange') {
	$data = $replies = $return = array();
	// $replies = DB::fetch_all("SELECT * FROM ".DB::table('forum_hotreply_number')." a LEFT JOIN ".DB::table('forum_post')." b ON (a.`pid`=b.`pid` AND b.`dateline` >= ".(time()-60*60*48).") ORDER BY a.`support` DESC LIMIT 0,20");
	$replies = DB::fetch_all("SELECT a.*, b.support FROM ".DB::table('forum_post')." a LEFT JOIN ".DB::table('forum_hotreply_number')." b ON a.`pid` = b.`pid` WHERE a.`dateline` >= ".(time()-60*60*48)." ORDER BY b.`support` DESC LIMIT 0,20");
	if(count(replies) > 0)
	{
		foreach ($replies as $key => $value) {
			if(intval($value['tid']) == 0)
				continue;
			$thread = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid=".$value['tid']);
			
			if($thread['displayorder'] < 0)
				continue;

			$replies[$key]['subject'] = $thread['subject'];
			$replies[$key]['avatar'] = avatar($value['authorid'], 'small');
			
			// zhangjh 2015-08-08 过滤中括号
			include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php');
			$search = array ("'\[.*?\].*?\[.*?\]'si");
			$replace = array ("");
			$replies[$key]['message'] = @preg_replace ($search, $replace, $value['message']);
			$replies[$key]['message'] = str_intercept($replies[$key]['message'], 0, 40);
			$replies[$key]['tid'] = $thread['tid'];
			$replies[$key]['pid'] = $value['pid'];
		}
	}
	$data['data'] = array_random($replies, 4);
// 	$str = '';
// 	foreach ($tmparr as $value) {
// 		$str .= '<li>';
// 		$str .= '<div class="reply_box cl">';
// 		$str .= '<a href="home.php?mod=space&uid='.$value[authorid].' target="_blank">'.$value[avatar].' &nbsp;'.$value[author].'</a>';
// 		$str .= '<span class="y sta_zan">'.$value['support'].'</span>';
// 		$str .= '</div>';
// 		$str .= '<p class="rep_title">'.$value[subject].'</p>';
// 		$str .= '<p class="rep_retitle"><i class="rep_icon"></i>'.$value[message].'</p>';
// 		$str .= '</li>';
// 	}
// 	var_dump($data['data']);die;
	include template('portal/index/godreply');	
	echo $return;
	exit;
} elseif ($action == 'userchange') {
	$formhash = $_GET['formhash'];
	$data = $members = $return = array();
	$members = DB::fetch_all("SELECT a.`uid`,a.`username`,a.`credits`,b.`bio` FROM ".DB::table('common_member')." a LEFT JOIN ".DB::table('common_member_profile')." b ON a.`uid`=b.`uid` ORDER BY `credits` DESC LIMIT 20");
	$data['data'] = array_random($members, 5);
	$str = '';
	foreach ($data['data'] as $key => $value) {
		$data['data'][$key]['displayorder'] = $key + 1;
		$data['data'][$key]['id'] = $value['uid'];
		$data['data'][$key]['fields']['avatar_middle'] = avatar($value['uid'], 'middle', 1);
		$data['data'][$key]['url'] = 'home.php?mod=space&uid='.$value['uid'];
		$data['data'][$key]['fields']['credits'] = $value['credits'];
		$data['data'][$key]['fields']['bio'] = $value['bio'];
	}
// 	var_dump($data['data']);die;
	include template('portal/index/active_fans');
	echo $return;
	exit;
} elseif ($action == 'hotthread') {
	global $_G;
	$item = C::t('common_block_item')->fetch_all_by_bid(135, true);
	$count = count($item);
// 	echo $count;die;
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 10;
	
	$maxpage = @ceil($count/$pagesize);
	$nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);
	$multipage_more = "plugin.php?id=extends&action=hotthread&&page=".$nextpage."&pagesize=".$pagesize;
	
	$data = $thread = array();
	$data['data'] = C::t('#extends#plugin_common_block_item')->fetch_all_by_bid(135, $page, $pagesize, true);
	
// 	var_dump($data['data']);die;
	include_once libfile('function/extends');
	foreach ($data['data'] as $key => $value) {
		$data['data'][$key]['fields'] = dunserialize($value['fields']);
			$data['data'][$key]['summary'] = get_message(0, $value['id']);
			$thread = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid=".$value['id']);
			$data['data'][$key]['replies'] = $thread['replies'];
			$data['data'][$key]['support_num'] = $thread['recommend_add'];
			$data['data'][$key]['dateline'] = date('m月d日 H:s', $data['data'][$key]['fields']['dateline']);
	}
	include template('portal/index/hot_thread');
	echo $return;
	exit;
}

//随机生成神回复
function array_random($arr, $num = 1) {
	shuffle($arr);
	
	$r = array();
	for ($i = 0; $i < $num; $i++) {
		$r[] = $arr[$i];
	}
	return $num == 1 ? $r[0] : $r;
}