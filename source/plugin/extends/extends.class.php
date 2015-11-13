<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_extends {
	function __construct() {
		global $_G;
		loadcache('plugin');
	}
	
// 	function global_header() {
// 		require_once libfile('function/extends');
// 		$thread = get_activity_status_by_tid_(11109);
// 	}
	
	//首页顶部热门活动（幻灯片）
	function global_hot_activity() {
		$result = $activitylists = array();
		$result = C::t('common_block_item')->fetch_all_by_bid(96, true);
		foreach ($result as $value) {
			$activitylists[] = $value;
		}
		include template('extends:portal/hot_activity');
		return $return;
	}
	
	//首页视频图片
	function global_portal_pic_video() {
		$data = $thread = array();
		foreach ($data = C::t('common_block_item')->fetch_all_by_bid(67) as $key => $value) {
			$thread = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid=".$value['id']);
			$data[$key]['attachment'] = $thread['attachment'];
		}	
				
		include template('extends:portal/pic_video_list');
		return $return;
	}
	//首页头条新闻
	function global_portal_topicnews() {
		loadcache('block_data_124');
		$data = $thread = array();
		$data = getglobal('cache/block_data_124');
		if (!isset($data['dateline']) || TIMESTAMP - $data['dateline'] >= 1200 || TRUE) { // 2015-07-14 zhangjh 改成即时
			foreach ($data['data'] = C::t('common_block_item')->fetch_all_by_bid(124, true) as $key => $value) {
				$thread = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid=".$value['id']);
				$data['data'][$key]['fields'] = dunserialize($value['fields']);
				$data['data'][$key]['fields']['fid'] = $thread['fid'];
				$data['data'][$key]['views'] = $thread['views'];
				$data['data'][$key]['replies'] = $thread['replies'];
				$data['data'][$key]['recommend_add'] = $thread['recommend_add'];
			}
			$data['dateline'] = TIMESTAMP;
			savecache('block_data_124', $data);
		}
		include template('extends:portal/topicnews');
		return $return;
	}
	//首页最新新闻
	function global_portal_lastnews() {
		loadcache('block_data_125');
		$data = $thread = array();
		$data = getglobal('cache/block_data_125');
		if (!isset($data['dateline']) || TIMESTAMP - $data['dateline'] >= 1200 || TRUE) { // 2015-07-14 zhangjh 改成即时
			foreach ($data['data'] = C::t('common_block_item')->fetch_all_by_bid(125, true) as $key => $value) {
				$thread = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid=".$value['id']);
				$data['data'][$key]['fields'] = dunserialize($value['fields']);
				$data['data'][$key]['fields']['fid'] = $thread['fid'];
				$data['data'][$key]['views'] = $thread['views'];
				$data['data'][$key]['replies'] = $thread['replies'];
				$data['data'][$key]['recommend_add'] = $thread['recommend_add'];
			}
			$data['dateline'] = TIMESTAMP;
			savecache('block_data_125', $data);
		}
		include template('extends:portal/lastnews');
		return $return;
	}
		
	function global_forum_data() {
		global $_G;
		$data = $branch = array();
		//分会数
		$branch = explode(',', $_G['forum']['relatedgroup']);
		
		// zhangjh 2015-06-13 只显示plugin_fansclub_info有的球迷会数目
		$branch = fansclub_forum_recount($_G['forum']['fid']);
		
		$data['branchnum'] = count($branch);
		//会员数
		foreach ($branch as $vo) {
			$forum = C::t('forum_forum')->fetch_info_by_fid($vo);
			$data['membernum'] += $forum['membernum'];
		}
		//帖子数
		$data['threadnum'] = $_G['forum']['threads'];
		include template("extends:forum_info");		
		return $return;		
	}
	//首页赛程
	function global_protal_fixture() {
	    include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php');	
		// $live_game = get_live_fixture();
		// include template('extends:portal/fixture');
		$return = ''; // zhangjh 移除不使用的功能
		return $return;
	}
	
	//新版首页热门版块
	function global_hot_forum() {
		global $_G;
		$data['data'] = C::t('common_block_item')->fetch_all_by_bid(131, true);
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['fields'] = dunserialize($value['fields']);
		}
// 		var_dump($data['data']);die;
		include template('common/footer/hotforum');
		return $return;
	}
}

class plugin_extends_forum extends plugin_extends {
	
	function viewthread_forum_data_output() {
		global $_G;
		$data = $branch = array();
		//分会数
		$branch = explode(',', $_G['forum']['relatedgroup']);
		
		// zhangjh 2015-06-13 只显示plugin_fansclub_info有的球迷会数目
		$branch = fansclub_forum_recount($_G['forum']['fid']);
		
		$data['branchnum'] = count($branch);
		//会员数
		foreach ($branch as $vo) {
			$forum = C::t('forum_forum')->fetch_info_by_fid($vo);
			$data['membernum'] += $forum['membernum'];
		}
		//帖子数
		$data['threadnum'] = $_G['forum']['threads'];
		include template("extends:forum_info");		
		return $return;		
	}
	
	function viewthread_sub_nav() {
		global $_G;
		//根据版块id获取对应的分类id
		if ($fid = intval($_G['fid'])) {
			$result = DB::fetch_first("SELECT * FROM ".DB::table('plugin_fansclub_info')." WHERE relation_fid=".$fid);
			$gid = 8;
			return '<li><a href="group.php?gid=' . $gid . '" target="_blank" title="">球迷会</a></li>';
		} else {
			return '';
		}
	}
	
	function viewthread_forum_info_output() {
		global $_G;
		require_once libfile('function/extends');
		if ($_G['forum']['type'] == 'sub') {
			$data['data'] = get_team_info_by_fid($_G['forum']['fup']);			
		} else {
			$data['data'] = get_team_info_by_fid($_G['forum']['fid']);
		}
		if ($_G['forum']['type'] == 'sub') {
			$forumfields = C::t('forum_forum')->fetch_info_by_fid($_G['forum']['fup']);
		} else {
			$forumfields = C::t('forum_forum')->fetch_info_by_fid($_G['forum']['fid']);
		}
		$data['data']['icon'] = $forumfields['icon'];
		$branch = fansclub_forum_recount($_G['forum']['fid']);
		$data['data']['branchnum'] = count($branch);
// 		var_dump($data['data']);die;
		include template('forum/viewthread/forum_info');
		return $return;
	}
	
	function viewthread_column1_output() {
		global $_G;
		
		$data = array('name' => '花花视界', 'keyword' => '体育花边', 'tagid' => 19031);
		$tidarray = array();		
		$id = intval($data['tagid']);
		$shownum = 4;
		$query = C::t('common_tagitem')->select($id, 0, 'tid', 'itemid', 'DESC', 0, $shownum);
		foreach ($query as $result) {
			$tidarray[$result['itemid']] = $result['itemid'];
		}
		require_once libfile('function/core', 'plugin/extends');
		$data['data'] = getthreadsbytids($tidarray);	
		
		include template('forum/viewthread/column');
		return $return;
	}
	
	function viewthread_column2_output() {
		global $_G;
	
		$data = array('name' => '精品文章', 'keyword' => '5U原创', 'tagid' => 18804);
		$tidarray = array();
		$id = intval($data['tagid']);
		$shownum = 4;
		$query = C::t('common_tagitem')->select($id, 0, 'tid', 'itemid', 'DESC', 0, $shownum);
		foreach ($query as $result) {
			$tidarray[$result['itemid']] = $result['itemid'];
		}
		require_once libfile('function/core', 'plugin/extends');
		$data['data'] = getthreadsbytids($tidarray);
	
		include template('forum/viewthread/column');
		return $return;
	}
/* 	
	function viewthread_fastpost_group_output() {
// 		global $_G;	
// 		if (!$_G['uid']) return false;
// 		if (!$_G['setting']['groupstatus']) return false;		//网站是否开启了群组功能
		
// 		//已加入的群组
// 		if(helper_access::check_module('group')) {
// 			$mygroups = $groupids = array();
// 			$groupids = C::t('forum_groupuser')->fetch_all_fid_by_uids($_G['uid']);
// 			array_slice($groupids, 0, 20);
// 			$query = C::t('forum_forum')->fetch_all_info_by_fids($groupids);
// 			foreach($query as $group) {
// 				$mygroups[$group['fid']] = $group['name'];
// 			}
// 		}
// 		include template('extends:group');
// 		return $group;		
*	}
*/
/*
        function forumdisplay_fastpost_group_output() {
// 		global $_G;
// 		if (!$_G['uid']) return false;
// 		if (!$_G['setting']['groupstatus']) return false;		//网站是否开启了群组功能
	
// 		//已加入的群组
// 		if(helper_access::check_module('group')) {
// 			$mygroups = $groupids = array();
// 			$groupids = C::t('forum_groupuser')->fetch_all_fid_by_uids($_G['uid']);
// 			array_slice($groupids, 0, 20);
// 			$query = C::t('forum_forum')->fetch_all_info_by_fids($groupids);
// 			foreach($query as $group) {
// 				$mygroups[$group['fid']] = $group['name'];
// 			}
// 		}
// 		include template('extends:group');
// 		return $group;
	}*/    
}
class plugin_extends_portal extends plugin_extends {
	//首页焦点图
	function index_focus_output() {
		$data = array();
		$data['data'] = C::t('common_block_item')->fetch_all_by_bid(132, true);
		include template('portal/index/focus');
		return $return;
	}
	
	//新首页推荐球迷会--官方发布位置
	function index_official_output() {
		global $_G;
		
		$data = array();
		include_once libfile('function/extends');
		$data['data'] = C::t('common_block_item')->fetch_all_by_bid(145, true);
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['fields'] = dunserialize($value['fields']);
			$data['data'][$key]['area'] = get_fansclub_area($value['id']);
 //                                                                 球迷会人数
                                                                    $userfield = DB::fetch_first("SELECT membernum FROM ".DB::table('forum_forumfield')." WHERE fid=".intval($value['id']));
                                                                     $data['data'][$key]['membernum'] = $userfield['membernum'];
		} 
		include template('portal/index/official');
		return $return;
	}
	
	//首页热门帖子
	function index_hot_thread_output() {
		$data = $thread = array();
		include_once libfile('function/extends');
		$data['data'] = C::t('#extends#plugin_common_block_item')->fetch_all_by_bid(135, 1, 10, true);
		
		$item = C::t('common_block_item')->fetch_all_by_bid(135, true);
		$count = count($item);
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 10;
		
		$maxpage = @ceil($count/$pagesize);
		$nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);
		//$multipage_more = "plugin.php?id=extends&action=hotthread&page=".$nextpage."&pagesize=".$pagesize;
		$multipage_more = "plugin.php?id=extends&action=hotthread&pagesize=".$pagesize;
		
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['fields'] = dunserialize($value['fields']);
			$data['data'][$key]['summary'] = get_message(0, $value['id']);
			$thread = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid=".$value['id']);
			$data['data'][$key]['replies'] = $thread['replies'];
			$data['data'][$key]['support_num'] = $thread['recommend_add'];
			$data['data'][$key]['dateline'] = date('m月d日 H:s', $data['data'][$key]['fields']['dateline']);
            
                                                                    preg_match("#fid=(\d+)#", $data['data'][$key]['fields']['forumurl'], $matches);
                                                                    $data['data'][$key]['fid'] = $matches[1];
		}
		include template('portal/index/hot_thread');
		return $return;
	}
	//首页热门活动
// 	function index_hot_activity_output() {
// 		$data = $activity = array();
// 		$data['data'] = C::t('#extends#plugin_common_block_item')->fetch_all_by_bid(151, true);
// 		var_dump($data);die;
// 		foreach ($data['data'] as $key => $value) {
// 			$activity = DB::fetch_first("SELECT * FROM ".DB::table('forum_activity')." WHERE tid=".$value['id']);
// 			$data['data'][$key]['starttimefrom'] = date('Y-m-d', $activity['starttimefrom']);
// 			if ($activity['starttimeto']) $data['data'][$key]['starttimeto'] = date('Y-m-d', $activity['starttimeto']);
// 			if (!empty($activity['starttimeto'])) {
// 				if ($activity['starttimeto'] > TIMESTAMP) {
// 					$data['data'][$key]['status'] = true;
// 				} else {
// 					$data['data'][$key]['status'] = false;
// 				}
// 			} else {
// 				$data['data'][$key]['status'] = true;
// 			}
			
// 		}
// 		include template('portal/index/hot_activity');
// 		return $return;
// 	}
	function index_hot_activity_output() {
		$data = $activity = array();
		$data['data'] = C::t('common_block_item')->fetch_all_by_bid(154, true);
		foreach ($data['data'] as $key => $value) {
			unset($data['data'][$key]['fields']);
			$data['data'][$key]['fields'] = dunserialize($value['fields']);
			if (!empty($data['data'][$key]['fields']['starttimeto'])) {
				if ($data['data'][$key]['fields']['starttimeto'] > TIMESTAMP) {
					$data['data'][$key]['status'] = true;
				} else {
					$data['data'][$key]['status'] = false;
				}
			} else {
				$data['data'][$key]['status'] = true;
			}
		}
// 		var_dump($data['data']);die;
		include template('portal/index/hot_activity');
		return $return;
	}
	//首页神回复
	function index_godreply_output() {
		$data = array();
		// $data['data'] = DB::fetch_all("SELECT * FROM ".DB::table('forum_hotreply_number')." a LEFT JOIN ".DB::table('forum_post')." b ON (a.`pid`=b.`pid` AND b.`dateline` >= ".(time()-60*60*48).") ORDER BY a.`support` DESC LIMIT 0,4");
		$sql = "SELECT a.*, b.support FROM ".DB::table('forum_post')." a LEFT JOIN ".DB::table('forum_hotreply_number')." b ON a.`pid` = b.`pid` WHERE a.`dateline` >= ".(time()-60*60*48)." ORDER BY b.`support` DESC LIMIT 0,4";
		$data['data'] = DB::fetch_all($sql);
		if(count($data['data']) > 0)
		{
			foreach ($data['data'] as $key => $value) {
				if(intval($value['tid']) == 0)
					continue;
				$thread = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid=".$value['tid']);
				$data['data'][$key]['subject'] = $thread['subject'];
				$data['data'][$key]['avatar'] = avatar($value['authorid'], 'small');
				
				// zhangjh 2015-08-08 过滤中括号
				include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php');
				$search = array ("'\[.*?\]'si");
				$replace = array ("");
				$value['message'] = @preg_replace($search, $replace, $value['message']);
				$data['data'][$key]['message'] = str_intercept($value['message'], 0, 40);
				$data['data'][$key]['tid'] = $thread['tid'];
				$data['data'][$key]['pid'] = $value['pid'];
			}
		}
		include template('portal/index/godreply');
		return $return;
	}	
	//首页认证用户
	function index_recommend_user_output($param) {
		global $_G;
		$data = $thread = array();
		$data['data'] = C::t('common_block_item')->fetch_all_by_bid(134, true);
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['fields'] = dunserialize($value['fields']);
			$thread = DB::fetch_all("SELECT `tid`,`subject` FROM ".DB::table('forum_thread')." WHERE `authorid`=".$value['id']." ORDER BY `dateline` DESC LIMIT 2");
			$data['data'][$key]['lastthread'] = $thread;
		}
		include template('portal/index/recommend_user');
		return $return;
	}	
                       //首页七天活跃粉丝
                       function index_active_fans_output($param) {
		global $_G;
		$data = array();
                                             $time = time();
                                            $data['data'] = DB::fetch_all('SELECT count(a.log_id) as post, a.g_username,a.g_uid,b.extcredits1,b.digestposts,c.bio  FROM '.DB::table('plugin_fansclub_user_log').' as a LEFT JOIN '.DB::table('common_member_count')." as b on b.uid = a.g_uid LEFT JOIN ".DB::table('common_member_profile')." as c  on b.uid = c.uid  WHERE a.log_type='thread_post'  and a.log_time >$time -7*24*3600  group by a.g_uid order by post desc limit 5");
                                            foreach ($data['data'] as $key => $value) {
                                                    $data['data'][$key]['jifen'] = $value['extcredits1']*2+$value['post']+$value['digestposts']*5;
                                                    $data['data'][$key]['order'] = $key+1;
                                                    $data['data'][$key]['image'] = $_G['setting']['ucenterurl'].'/avatar.php?uid='.$value['g_uid'].'&size=small';
                                            }                                          
		include template('portal/index/active_fans');
		return $return;
	}
}
?>