<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: forum_group.php 33695 2013-08-03 04:39:22Z nemohou $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/group');
$_G['action']['action'] = 3;
$_G['action']['fid'] = $_G['fid'];
$_G['basescript'] = 'group';
//edit by Daming, add 'introduce, activity' for wap
$actionarray = array('join', 'out', 'create', 'viewmember', 'manage', 'index', 'memberlist', 'recommend', 'introduce', 'activity');
$action = getgpc('action') && in_array($_GET['action'], $actionarray) ? $_GET['action'] : 'index';

if(in_array($action, array('join', 'out', 'create', 'manage', 'recommend'))) {
	if(empty($_G['uid'])) {
		showmessage('not_loggedin', '', '', array('login' => 1));
	}
}
if(empty($_G['fid']) && $action != 'create') {
	showmessage('group_rediret_now', 'group.php');
}
$first = &$_G['cache']['grouptype']['first'];
$second = &$_G['cache']['grouptype']['second'];
$rssauth = $_G['rssauth'];
$rsshead = $_G['setting']['rssstatus'] ? ('<link rel="alternate" type="application/rss+xml" title="'.$_G['setting']['bbname'].' - '.$navtitle.'" href="'.$_G['siteurl'].'forum.php?mod=rss&fid='.$_G['fid'].'&amp;auth='.$rssauth."\" />\n") : '';
if($_G['fid']) {
	if($_G['forum']['status'] != 3) {
		showmessage('forum_not_group', 'group.php');
	} elseif($_G['forum']['level'] == -1) {
		showmessage('group_verify', '', array(), array('alert' => 'info'));
	} elseif($_G['forum']['jointype'] < 0 && !$_G['forum']['ismoderator']) {
		showmessage('forum_group_status_off', 'group.php');
	}
	$groupcache = getgroupcache($_G['fid'], array('replies', 'views', 'digest', 'lastpost', 'ranking', 'activityuser', 'newuserlist'), 604800);

	$_G['forum']['icon'] = get_groupimg($_G['forum']['icon'], 'icon');
	$_G['forum']['banner'] = get_groupimg($_G['forum']['banner']);
	$_G['forum']['dateline'] = dgmdate($_G['forum']['dateline'], 'd');
	$_G['forum']['posts'] = intval($_G['forum']['posts']);
	$_G['grouptypeid'] = $_G['forum']['fup'];
	$groupuser = C::t('forum_groupuser')->fetch_userinfo($_G['uid'], $_G['fid']);
	//add by Daming 2015/9/22 fetch group’s area
	include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php');
	$groupinfo = get_fansclub_info($_G['fid']);
	$_G['forum']['province_name'] = $groupinfo['province_name'];
	$_G['forum']['city_name'] = $groupinfo['city_name'];
	$_G['forum']['color'] = $groupinfo['color'];	
	
// 	var_dump($groupinfo);die;
// 	$_G['forum']['membernum'] = $groupuser;
	$onlinemember = grouponline($_G['fid'], 1);
	$groupmanagers = $_G['forum']['moderators'];
	
	$nav = get_groupnav($_G['forum']);
	$groupnav = $nav['nav'];

	$seodata = array('forum' => $_G['forum']['name'], 'first' => $nav['first']['name'], 'second' => $nav['second']['name'], 'gdes' => $_G['forum']['description']);
	list($navtitle, $metadescription, $metakeywords) = get_seosetting('grouppage', $seodata);
	if(!$navtitle) {
		$navtitle = helper_seo::get_title_page($_G['forum']['name'], $_G['page']).' - '.$_G['setting']['navs'][3]['navname'];
		$nobbname = false;
	} else {
		$nobbname = true;
	}
	if(!$metakeywords) {
		$metakeywords = $_G['forum']['name'];
	}
	if(!$metadescription) {
		$metadescription = $_G['forum']['name'];
	}
	$_G['seokeywords'] = $_G['setting']['seokeywords']['group'];
	$_G['seodescription'] = $_G['setting']['seodescription']['group'];
	
	// zhangjh 2015-05-08 合并在top显示的
	include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php'); // 公共函数
	$_G['forum'] = array_merge($_G['forum'], get_fansclub_info($_G['fid']));
	
}

if(in_array($action, array('out', 'viewmember', 'manage', 'index', 'memberlist'))) {
	$status = groupperm($_G['forum'], $_G['uid'], $action, $groupuser);
	if($status == -1) {
		showmessage('forum_not_group', 'group.php');
	} elseif($status == 1) {
		showmessage('forum_group_status_off');
	}
	if($action != 'index') {
		if($status == 2) {
			showmessage('forum_group_noallowed', "forum.php?mod=group&fid=$_G[fid]");
		} elseif($status == 3) {
			showmessage('forum_group_moderated', "forum.php?mod=group&fid=$_G[fid]");
		}
	}
}

if(in_array($action, array('index')) && $status != 2) {

	$newuserlist = $activityuserlist = array();
	foreach($groupcache['newuserlist']['data'] as $user) {
		$newuserlist[$user['uid']] = $user;
		$newuserlist[$user['uid']]['online'] = !empty($onlinemember['list']) && is_array($onlinemember['list']) && !empty($onlinemember['list'][$user['uid']]) ? 1 : 0;
	}

	$activityuser = array_slice($groupcache['activityuser']['data'], 0, 8);
	foreach($activityuser as $user) {
		$activityuserlist[$user['uid']] = $user;
		$activityuserlist[$user['uid']]['online'] = !empty($onlinemember['list']) && is_array($onlinemember['list']) && !empty($onlinemember['list'][$user['uid']]) ? 1 : 0;
	}

	$groupviewed_list = get_viewedgroup();

}

$showpoll = $showtrade = $showreward = $showactivity = $showdebate = 0;
if($_G['forum']['allowpostspecial']) {
	$showpoll = $_G['forum']['allowpostspecial'] & 1;
	$showtrade = $_G['forum']['allowpostspecial'] & 2;
	$showreward = isset($_G['setting']['extcredits'][$_G['setting']['creditstransextra'][2]]) && ($_G['forum']['allowpostspecial'] & 4);
	$showactivity = $_G['forum']['allowpostspecial'] & 8;
	$showdebate = $_G['forum']['allowpostspecial'] & 16;
}

if($_G['group']['allowpost']) {
	$_G['group']['allowpostpoll'] = $_G['group']['allowpostpoll'] && $showpoll;
	$_G['group']['allowposttrade'] = $_G['group']['allowposttrade'] && $showtrade;
	$_G['group']['allowpostreward'] = $_G['group']['allowpostreward'] && $showreward;
	$_G['group']['allowpostactivity'] = $_G['group']['allowpostactivity'] && $showactivity;
	$_G['group']['allowpostdebate'] = $_G['group']['allowpostdebate'] && $showdebate;
}
if($action == 'index') {

	$newthreadlist = $livethread = array();
	if($status != 2) {		
		if ($_GET['mobile'] == 2 || $_GET['wap'] == 1) {
			
			$count = C::t('forum_thread')->count_thread_by_fid_type($_G['fid'], 0);
						
			$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
			$perpage = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 10;	
						
			$threadlist = array();
		
			$start = ($page - 1)*$perpage;
			$threadlist = C::t('forum_thread')->fetch_all_common_by_fid_displayorder($_G['fid'], 0, null, null, $start, $perpage, 'displayorder');
			require_once libfile('function/extends');
			foreach ($threadlist as $key => $value) {
				$threadlist[$key]['author_avatar'] = avatar($value['authorid'], 'small', 1);
				if ($value['attachment'] == '2') {
					$aids = wap_getattachment($value['tid'], 3);
// 					var_dump($value['tid']);
					foreach ($aids as $aid) {
						$threadlist[$key]['img'][] = getforumimg($aid, 0, 320, 170, 2);
					}
				} elseif ($value['attachment'] == '3') {
					$threadlist[$key]['video'] = create_video($value['tid']);
				}
// 				$threadlist[$key]['dateline'] = date('Y-m-d H:i', $value['dateline']);
				$threadlist[$key]['dateline'] = dgmdate($value['dateline'], 'u');
			}
			
			$maxpage = @ceil($count/$perpage);
			$nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);
			
			$multipage_more = "forum.php?mod=group&fid=".$_G['fid']."&page=".$nextpage."&ajax=1";			
			if ($_GET['ajax'] == 1) {
				$result = array('nextpage' => $nextpage, 'threadlist' => $threadlist);
				echo json_encode($result);
				exit;
			}
// 					var_dump($threadlist);
// 					die;
		}
		
		loadcache('forumstick');
		$forumstickytids = '';
		if(isset($_G['cache']['forumstick'][$_G['forum']['fup']])) {
			$forumstickytids = $_G['cache']['forumstick'][$_G['forum']['fup']];
		}
		require_once libfile('function/feed');
		if($forumstickytids) {
			foreach(C::t('forum_thread')->fetch_all_by_tid_or_fid($_G['fid'], $forumstickytids) as $row) {
				$row['dateline'] = dgmdate($row['dateline'], 'd');
				$row['lastpost'] = dgmdate($row['lastpost'], 'u');
				$row['allreplies'] = $row['replies'] + $row['comments'];
				$row['lastposterenc'] = rawurlencode($row['lastposter']);
				$stickythread[$row['tid']] = $row;
			}
		}
		$newthreadlist = getgroupcache($_G['fid'], array('dateline'), 0, 10, 0, 1);
		foreach($newthreadlist['dateline']['data'] as $v){
			$tids[]=$v['tid'];
		}
		if(count($tids) > 0)
		{
			$strtids=join(',',$tids);
			$msginfos=DB::fetch_all("SELECT tid,message FROM %t WHERE tid IN($strtids)",array('forum_post'));
			if(!function_exists('messagecutstr')){
				require_once libfile('function/post');
			}
			foreach($msginfos as $v){
				$cutmsginfos[$v['tid']]=messagecutstr($v['message'],200);
			}
		}
		foreach($newthreadlist['dateline']['data'] as $key => $thread) {
			$newthreadlist['dateline']['data'][$key]['cutmsg'] = $cutmsginfos[$thread['tid']];
			if(!empty($stickythread) && $stickythread[$thread[tid]]) {
				unset($newthreadlist['dateline']['data'][$key]);
				continue;
			}
			$newthreadlist['dateline']['data'][$key]['allreplies'] = $newthreadlist['dateline']['data'][$key]['replies'] + $newthreadlist['dateline']['data'][$key]['comments'];
			if($thread['closed'] == 1) {
				$newthreadlist['dateline']['data'][$key]['folder'] = 'lock';
			} elseif(empty($_G['cookie']['oldtopics']) || strpos($_G['cookie']['oldtopics'], 'D'.$thread['tid'].'D') === FALSE) {
				$newthreadlist['dateline']['data'][$key]['folder'] = 'new';
			} else {
				$newthreadlist['dateline']['data'][$key]['folder'] = 'common';
			}
		}

		if($stickythread) {
			$newthreadlist['dateline']['data'] = array_merge($stickythread, $newthreadlist['dateline']['data']);
		}
		$groupfeedlist = array();
		if(!IS_ROBOT) {
			$activityuser = array_keys($groupcache['activityuser']['data']);
			if($activityuser) {
				$query = C::t('home_feed')->fetch_all_by_uid_dateline($activityuser);
				foreach($query as $feed) {
					if($feed['friend'] == 0) {
						$groupfeedlist[] = mkfeed($feed);
					}
				}
			}
		}

		if($_G['forum']['livetid']) {
			include_once libfile('function/post');
			$livethread = C::t('forum_thread')->fetch($_G['forum']['livetid']);
			$livepost = C::t('forum_post')->fetch_threadpost_by_tid_invisible($_G['forum']['livetid']);
			$livemessage = messagecutstr($livepost['message'], 200);
			$liveallowpostreply = $groupuser['uid'] && $groupuser['level'] ? true : false;
			list($seccodecheck, $secqaacheck) = seccheck('post', 'newthread');
		}

	} else {
		$newuserlist = $activityuserlist = array();
		$newuserlist = array_slice($groupcache['newuserlist']['data'], 0, 4);
		foreach($newuserlist as $user) {
			$newuserlist[$user['uid']] = $user;
			$newuserlist[$user['uid']]['online'] = !empty($onlinemember['list']) && is_array($onlinemember['list']) && !empty($onlinemember['list'][$user['uid']]) ? 1 : 0;
		}
	}

	write_groupviewed($_G['fid']);
	include template('diy:group/group:'.$_G['fid']);

} elseif($action == 'memberlist') {
	
	if (!$_G['uid']) {
		$isadmin = false;
	} else {
		$isadmin = isadminbyuid($_G['uid'], $_G['fid']);
	}

	$oparray = array('card', 'address', 'alluser');
	$op = getgpc('op') && in_array($_GET['op'], $oparray) ?  $_GET['op'] : 'alluser';
	$page = intval(getgpc('page')) ? intval($_GET['page']) : 1;
	$perpage = isset($_GET['mobile']) ? 16 : 50;
	$start = ($page - 1) * $perpage;

	$alluserlist = $adminuserlist = array();
	$staruserlist = $page < 2 ? C::t('forum_groupuser')->groupuserlist($_G['fid'], 'lastupdate', 0, 0, array('level' => '3'), array('uid', 'username', 'level', 'joindateline', 'lastupdate')) : '';
	$adminlist = $groupmanagers && $page < 2 ? $groupmanagers : array();

	
	if($op == 'alluser') {
		
		if ($_GET['mobile'] || $_GET['wap'] == 1) {
			$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
			$perpage = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 20;
			$maxpage = @ceil($_G['forum']['membernum']/$perpage);
			$nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);
			
			$start = ($page - 1) * $perpage;
			
			$alluserlist = C::t('forum_groupuser')->groupuserlist($_G['fid'], 'lastupdate', $perpage, $start);
			foreach ($alluserlist as $key => $user) {
// 				$alluserlist[$key]['username'] = cutstr($user['username'], '12', '...');
				$alluserlist[$key]['avatar'] = avatar($user['uid'], 'middle', true);
			}
			$multipage_more = "forum.php?mod=group&action=memberlist&fid=".$_G['fid']."&page=".$nextpage."&ajax=1";
			if ($_GET['ajax'] == 1) {
				echo json_encode(array('nextpage' => $nextpage, 'userlist' => $alluserlist));
				exit;
			}			
		} else {
			$alluserlist = C::t('forum_groupuser')->groupuserlist($_G['fid'], 'lastupdate', $perpage, $start, "AND level='4'", '', $onlinemember['list']);;
		}
		
// 		var_dump($alluserlist);die;
		
		$multipage = multi($_G['forum']['membernum'], $perpage, $page, 'forum.php?mod=group&action=memberlist&op=alluser&fid='.$_G['fid']);

		if($adminlist) {
			foreach($adminlist as $user) {
				$adminuserlist[$user['uid']] = $user;
				$adminuserlist[$user['uid']]['online'] = $onlinemember['list'] && is_array($onlinemember['list']) && $onlinemember['list'][$user['uid']] ? 1 : 0;
			}
		}
	}
	
	
	
	// 2015-06-29 zhangjh 加 navtitle metakeywords 和 metadescription
	$nobbname = TRUE;
	$navtitle = $_G['forum']['name'].'会员_'.$_G['setting']['bbname'].'球迷会';
	$metakeywords = $_G['forum']['name'].'会员';
	$metadescription = '所有'.$_G['forum']['name'].'的成员都将在这个页面呈现，包括他们的基本资料、图片以及详细信息。';
	
	include template('diy:group/group:'.$_G['fid']);

} elseif($action == 'join') {
	
	// 是否只可以加入一个球迷会
	// $jioned = DB::result_first("SELECT uid FROM ".DB::table('forum_groupuser')." WHERE uid='$_G[uid]'");
	$jioned = 0; 
	$arr_tmp = C::t('#fansclub#plugin_forum_groupuser')->fetch_all_group_by_uid(intval($_G['uid']));
	for($i = 0; $i < count($arr_tmp); $i++)
	{
		$forum_tmp = C::t('forum_forum')->fetch($arr_tmp[$i]['fid']);
		
		if($forum_tmp['status'] == 3) // 只计算群组
			$jioned++;
	}
	/*
	if($jioned > 0 && $_GET['mobile'] == 2) {
		showmessage('亲，只能加入一个球迷会哦！', "forum.php?mod=group&fid=$_G[fid]");
	} else {
		showmessage('亲，只能加入一个球迷会哦！', "fans/topic/$_G[fid]/");
	}
    */
	
	// 加入成功，清理自己的缓存
	$mem_check = memory('check'); // 先检查缓存是否生效
	if($mem_check != '')
	{
		memory('rm', 'fansclub_arr_user_havejoin_'.$_G['uid']);
	}
	
	$inviteuid = 0;
	$membermaximum = $_G['current_grouplevel']['specialswitch']['membermaximum'];
	if(!empty($membermaximum)) {
		$curnum = C::t('forum_groupuser')->fetch_count_by_fid($_G['fid']);
		if($curnum >= $membermaximum) {
			showmessage('group_member_maximum', '', array('membermaximum' => $membermaximum));
		}
	}
	if($groupuser['uid']) {
		showmessage('group_has_joined', "forum.php?mod=group&fid=$_G[fid]");
	} else {
		$modmember = 4;
		$showmessage = 'group_join_succeed';
		$confirmjoin = TRUE;
		$inviteuid = C::t('forum_groupinvite')->fetch_uid_by_inviteuid($_G['fid'], $_G['uid']);
		if($_G['forum']['jointype'] == 1) {
			if(!$inviteuid) {
				$confirmjoin = FALSE;
				$showmessage = 'group_join_need_invite';
			}
		} elseif($_G['forum']['jointype'] == 2) {
			$modmember = !empty($groupmanagers[$inviteuid]) || $_G['adminid'] == 1 ? 4 : 0;
			
			// zhangjh 2015-06-11 这个逻辑好像有问题
			// !empty($groupmanagers[$inviteuid]) && $showmessage = 'group_join_apply_succeed';
			if($modmember == 0) $showmessage = 'group_join_apply_succeed';
		}

		if($confirmjoin) {
			C::t('forum_groupuser')->insert($_G['fid'], $_G['uid'], $_G['username'], $modmember, TIMESTAMP, TIMESTAMP);
			if($_G['forum']['jointype'] == 2 && (empty($inviteuid) || empty($groupmanagers[$inviteuid]))) {
				foreach($groupmanagers as $manage) {
					notification_add($manage['uid'], 'group', 'group_member_join', array('fid' => $_G['fid'], 'groupname' => $_G['forum']['name'], 'url' => $_G['siteurl'].'forum.php?mod=group&action=manage&op=checkuser&fid='.$_G['fid']), 1);
				}
			} else {
			}
			if($inviteuid) {
				C::t('forum_groupinvite')->delete_by_inviteuid($_G['fid'], $_G['uid']);
			}
			if($modmember == 4) {
				C::t('forum_forumfield')->update_membernum($_G['fid']);
			}
			C::t('forum_forumfield')->update($_G['fid'], array('lastupdate' => TIMESTAMP));
		}
		include_once libfile('function/stat');
		updatestat('groupjoin');
		delgroupcache($_G['fid'], array('activityuser', 'newuserlist'));
		if ($_GET['forward'] == 'current') showmessage($showmessage, NULL, NULL, array('timeout'=>1, 'alert'=>'right'));
		showmessage($showmessage, "forum.php?mod=group&fid=$_G[fid]");
	}

} elseif($action == 'out') {

	if($_G['uid'] == $_G['forum']['founderuid']) {
		showmessage('group_exit_founder');
	}
	$showmessage = 'group_exit_succeed';
		C::t('forum_groupuser')->delete_by_fid($_G['fid'], $_G['uid']);
		C::t('forum_forumfield')->update_membernum($_G['fid'], -1);
	update_groupmoderators($_G['fid']);
	delgroupcache($_G['fid'], array('activityuser', 'newuserlist'));
	
	// 退出成功，清理自己的缓存
	$mem_check = memory('check'); // 先检查缓存是否生效
	if($mem_check != '')
	{
		memory('rm', 'fansclub_arr_user_havejoin_'.$_G['uid']);
	}
	if ($_GET['mobile']) {
		showmessage($showmessage, "forum.php?mod=group&fid=$_G[fid]");
	} else {
		showmessage($showmessage, "forum.php?mod=forumdisplay&fid=$_G[fid]");
	}	

} elseif($action == 'create') {

	if(!$_G['group']['allowbuildgroup']) {
		showmessage('group_create_usergroup_failed', "group.php");
	}

	$creditstransextra = $_G['setting']['creditstransextra']['12'] ? $_G['setting']['creditstransextra']['12'] : $_G['setting']['creditstrans'];
	if($_G['group']['buildgroupcredits']) {
		if(empty($creditstransextra)) {
			$_G['group']['buildgroupcredits'] = 0;
		} else {
			getuserprofile('extcredits'.$creditstransextra);
			if($_G['member']['extcredits'.$creditstransextra] < $_G['group']['buildgroupcredits']) {
				showmessage('group_create_usergroup_credits_failed', '', array('buildgroupcredits' => $_G['group']['buildgroupcredits']. $_G['setting']['extcredits'][$creditstransextra]['unit'].$_G['setting']['extcredits'][$creditstransextra]['title']));
			}
		}
	}

	$groupnum = C::t('forum_forumfield')->fetch_groupnum_by_founderuid($_G['uid']);
	$allowbuildgroup = $_G['group']['allowbuildgroup'] - $groupnum;
	if($allowbuildgroup < 1) {
		showmessage('group_create_max_failed');
	}
	$_GET['fupid'] = intval($_GET['fupid']);
	$_GET['groupid'] = intval($_GET['groupid']);

	if(!submitcheck('createsubmit')) {
		$groupselect = get_groupselect(getgpc('fupid'), getgpc('groupid'));
	} else {
		$parentid = intval($_GET['parentid']);
		$fup = intval($_GET['fup']);
		$name = censor(dhtmlspecialchars(cutstr(trim($_GET['name']), 20, '')));
		$censormod = censormod($name);
		if(empty($name)) {
			showmessage('group_name_empty');
		} elseif($censormod) {
			showmessage('group_name_failed');
		} elseif(empty($parentid) && empty($fup)) {
			showmessage('group_category_empty');
		}
		if(empty($_G['cache']['grouptype']['first'][$parentid]) && empty($_G['cache']['grouptype']['second'][$fup])
			|| $_G['cache']['grouptype']['first'][$parentid]['secondlist'] &&  !in_array($_G['cache']['grouptype']['second'][$fup]['fid'], $_G['cache']['grouptype']['first'][$parentid]['secondlist'])) {
			showmessage('group_category_error');
		}
		if(empty($fup)) {
			$fup = $parentid;
		}
		if(C::t('forum_forum')->fetch_fid_by_name($name)) {
			showmessage('group_name_exist');
		}
		require_once libfile('function/discuzcode');
		$descriptionnew = discuzcode(dhtmlspecialchars(censor(trim($_GET['descriptionnew']))), 0, 0, 0, 0, 1, 1, 0, 0, 1);
		$censormod = censormod($descriptionnew);
		if($censormod) {
			showmessage('group_description_failed');
		}
		if(empty($_G['setting']['groupmod']) || $_G['adminid'] == 1) {
			$levelinfo = C::t('forum_grouplevel')->fetch_by_credits();
			$levelid = $levelinfo['levelid'];
		} else {
			$levelid = -1;
		}
		$newfid = C::t('forum_forum')->insert_group($fup, 'sub', $name, '3', $levelid);
		if($newfid) {
			$jointype = intval($_GET['jointype']);
			$gviewperm = intval($_GET['gviewperm']);
			$fieldarray = array('fid' => $newfid, 'description' => $descriptionnew, 'jointype' => $jointype, 'gviewperm' => $gviewperm, 'dateline' => TIMESTAMP, 'founderuid' => $_G['uid'], 'foundername' => $_G['username'], 'membernum' => 1);
			C::t('forum_forumfield')->insert($fieldarray);
			C::t('forum_forumfield')->update_groupnum($fup, 1);
			C::t('forum_groupuser')->insert($newfid, $_G['uid'], $_G['username'], 1, TIMESTAMP);
			require_once libfile('function/cache');
			updatecache('grouptype');
		}
		if($creditstransextra && $_G['group']['buildgroupcredits']) {
			updatemembercount($_G['uid'], array($creditstransextra => -$_G['group']['buildgroupcredits']), 1, 'BGR', $newfid);
		}
		include_once libfile('function/stat');
		updatestat('group');
		if($levelid == -1) {
			showmessage('group_create_mod_succeed', "group.php?mod=my&view=manager", array(), array('alert' => 'right', 'showdialog' => 1, 'showmsg' => true, 'locationtime' => true));
		}
		showmessage('group_create_succeed', "forum.php?mod=group&action=manage&fid=$newfid", array(), array('showdialog' => 1, 'showmsg' => true, 'locationtime' => true));
	}

	include template('diy:group/group:'.$_G['fid']);

} elseif($action == 'manage'){
	if(!$_G['forum']['ismoderator']) {
		showmessage('group_admin_noallowed');
	}
	$specialswitch = $_G['current_grouplevel']['specialswitch'];

	$oparray = array('group', 'checkuser', 'manageuser', 'threadtype', 'demise');
	$_GET['op'] = getgpc('op') && in_array($_GET['op'], $oparray) ?  $_GET['op'] : 'group';
	if(empty($groupmanagers[$_G[uid]]) && !in_array($_GET['op'], array('group', 'threadtype', 'demise')) && $_G['adminid'] != 1) {
		showmessage('group_admin_noallowed');
	}
	$page = intval(getgpc('page')) ? intval($_GET['page']) : 1;
	$perpage = 50;
	$start = ($page - 1) * $perpage;
	$url = 'forum.php?mod=group&action=manage&op='.$_GET['op'].'&fid='.$_G['fid'];
	if($_GET['op'] == 'group') {
		$domainlength = checkperm('domainlength');
		if(submitcheck('groupmanage')) {
			$forumarr = array();
			if(isset($_GET['domain']) && $_G['forum']['domain'] != $_GET['domain']) {
				$domain = strtolower(trim($_GET['domain']));
				if($_G['setting']['allowgroupdomain'] && !empty($_G['setting']['domain']['root']['group']) && $domainlength) {
					checklowerlimit('modifydomain');
				}
				require_once libfile('function/delete');
				if(empty($domainlength) || empty($domain)) {
					$domain = '';
					deletedomain($_G['fid'], 'group');
				} else {
					require_once libfile('function/domain');
					if(domaincheck($domain, $_G['setting']['domain']['root']['group'], $domainlength)) {
						deletedomain($_G['fid'], 'group');
						C::t('common_domain')->insert(array('domain' => $domain, 'domainroot' => $_G['setting']['domain']['root']['group'], 'id' => $_G['fid'], 'idtype' => 'group'));
					}

				}
				$forumarr['domain'] = $domain;
				updatecreditbyaction('modifydomain');
			}

			if(($_GET['name'] && !empty($specialswitch['allowchangename'])) || ($_GET['fup'] && !empty($specialswitch['allowchangetype']))) {
				if($_G['uid'] != $_G['forum']['founderuid'] && $_G['adminid'] != 1) {
					showmessage('group_edit_only_founder');
				}
				$fup = intval($_GET['fup']);
				$parentid = intval($_GET['parentid']);

				if(isset($_GET['name'])) {
					$_GET['name'] = censor(dhtmlspecialchars(cutstr(trim($_GET['name']), 20, '')));
					if(empty($_GET['name'])) {
						showmessage('group_name_empty');
					}
					$censormod = censormod($_GET['name']);
					if($censormod) {
						showmessage('group_name_failed');
					}
				} elseif(isset($_GET['parentid']) && empty($parentid) && empty($fup)) {
					showmessage('group_category_empty');
				}
				if(!empty($_GET['name']) && $_GET['name'] != $_G['forum']['name']) {
					if(C::t('forum_forum')->fetch_fid_by_name($_GET['name'])) {
						showmessage('group_name_exist', $url);
					}
					$forumarr['name'] = $_GET['name'];
				}
				if(empty($fup)) {
					$fup = $parentid;
				}
				if(isset($_GET['parentid']) && $fup != $_G['forum']['fup']) {
					$forumarr['fup'] = $fup;
				}
			}
			
			if($forumarr) {
				C::t('forum_forum')->update($_G['fid'], $forumarr);
				if($forumarr['fup']) {
					C::t('forum_forumfield')->update_groupnum($forumarr['fup'], 1);
					C::t('forum_forumfield')->update_groupnum($_G['forum']['fup'], -1);
					require_once libfile('function/cache');
					updatecache('grouptype');
				}
			}

			$setarr = array();
			$deletebanner = $_GET['deletebanner'];
			$iconnew = upload_icon_banner($_G['forum'], $_FILES['iconnew'], 'icon');
			$bannernew = upload_icon_banner($_G['forum'], $_FILES['bannernew'], 'banner');
			if($iconnew) {
				$setarr['icon'] = $iconnew;
				$group_recommend = dunserialize($_G['setting']['group_recommend']);
				if($group_recommend[$_G['fid']]) {
					$group_recommend[$_G['fid']]['icon'] = get_groupimg($iconnew);
					C::t('common_setting')->update('group_recommend', $group_recommend);
					include libfile('function/cache');
					updatecache('setting');
				}
			}
			if($bannernew && empty($deletebanner)) {
				$setarr['banner'] = $bannernew;
			} elseif($deletebanner) {
				$setarr['banner'] = '';
				@unlink($_G['forum']['banner']);
			}
			require_once libfile('function/discuzcode');
			$_GET['descriptionnew'] = discuzcode(censor(trim($_GET['descriptionnew'])), 0, 0, 0, 0, 1, 1, 0, 0, 1);
			$censormod = censormod($_GET['descriptionnew']);
			if($censormod) {
				showmessage('group_description_failed');
			}
			$_GET['jointypenew'] = intval($_GET['jointypenew']);
			if($_GET['jointypenew'] == '-1' && $_G['uid'] != $_G['forum']['founderuid']) {
				showmessage('group_close_only_founder');
			}
			
			// zhangjh 2015-05-06 修改 所属分类 和 地区 开始
			//if(intval(trim($_GET['league_id'])) == 0 || intval(trim($_GET['club_id'])) == 0) // 这个可能没有
			//{
				// showmessage('【所属分类】没有选择【联赛】或【球会】');
			//}
			//elseif(intval(trim($_GET['province_id'])) == 0 || intval(trim($_GET['city_id'])) == 0)
			//{
				// showmessage('【地区】没有选择【省份】或【城市】');
			//}
			//else
			if((intval(trim($_GET['league_id'])) > 0 && intval(trim($_GET['club_id'])) > 0) || 
			   (intval(trim($_GET['province_id'])) > 0 && intval(trim($_GET['city_id'])) > 0))
			{
				include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php'); // fansclub的公共函数
				
				if(intval(trim($_GET['league_id'])) > 0 && intval(trim($_GET['club_id'])) > 0) // 有选版块
				{
					$apply = array();
					$apply['league_id'] = $_GET['league_id'] + 0;
					$apply['club_id'] = $_GET['club_id'] + 0;
					$apply['star_id'] = $_GET['star_id'] + 0;
					$new_fup = fansclub_add_group_type($apply);
					
					if($apply['star_id'] == 0)
					{
						$relation_fid = $apply['club_id'];
					}
					else
					{
						$relation_fid = $apply['star_id'];
					}
					
					if($new_fup != $_G['forum']['fup'] && $new_fup != 0) // 如果fansclub的上级有改动
					{
						$fansclub_forumarr = array();
						$fansclub_forumarr['fup'] = $new_fup;
						C::t('forum_forum')->update($_G['fid'], $fansclub_forumarr);
						
						C::t('forum_forumfield')->update_groupnum($new_fup, 1);
						C::t('forum_forumfield')->update_groupnum($_G['forum']['fup'], -1);
						require_once libfile('function/cache');
						updatecache('grouptype');
						
						// 重新设置板块关联
						$old_arr_info = get_fansclub_info($_G['fid']);
						fansclub_remove_forum_relation($old_arr_info['relation_fid'], $_G['fid']);
						fansclub_add_forum_relation($relation_fid, $_G['fid']);
					}
				}
				//exit;
				
				$fansclub_setarr = array();
				
				if(intval(trim($_GET['league_id'])) > 0 && intval(trim($_GET['club_id'])) > 0)
				{
					$fansclub_setarr['fup'] = $new_fup;
					$fansclub_setarr['relation_fid'] = $relation_fid;
					$fansclub_setarr['relation_name'] = fansclub_get_forum_name($relation_fid);
					$fansclub_setarr['league_id'] = $_GET['league_id'];
					$fansclub_setarr['club_id'] = $_GET['club_id'];
					$fansclub_setarr['star_id'] = $_GET['star_id'];
				}
				
				if(trim($_GET['name']) != '')
				{
					$fansclub_setarr['name'] = trim($_GET['name']);
				}
				
				if(intval(trim($_GET['province_id'])) > 0 && intval(trim($_GET['city_id'])) > 0)
				{
					$fansclub_setarr['province_city'] = $_GET['fansclubprovince'].' '.$_GET['fansclubcity'];
					$fansclub_setarr['province_id'] = $_GET['province_id'];
					$fansclub_setarr['city_id'] = $_GET['city_id'];
					$fansclub_setarr['district_id'] = $_GET['district_id'];
					$fansclub_setarr['community_id'] = $_GET['community_id'];
				}
				
				if($iconnew) {
					$fansclub_setarr['logo'] = $iconnew;
				}
				$fansclub_setarr['brief'] = $_GET['descriptionnew'];
				C::t('#fansclub#plugin_fansclub_info')->update($_G['fid'], $fansclub_setarr);
			}
			// zhangjh 2015-05-06 修改 所属分类 和 地区 结束
			
			$_GET['gviewpermnew'] = intval($_GET['gviewpermnew']);
			$setarr['description'] = $_GET['descriptionnew'];
			$setarr['jointype'] = $_GET['jointypenew'];
			$setarr['gviewperm'] = $_GET['gviewpermnew'];
			C::t('forum_forumfield')->update($_G['fid'], $setarr);
			showmessage('group_setup_succeed', $url);
		} else {
			$firstgid = $_G['cache']['grouptype']['second'][$_G['forum']['fup']]['fup'];
			$groupselect = get_groupselect($firstgid, $_G['forum']['fup']);
			$gviewpermselect = $jointypeselect = array('','','');
			require_once libfile('function/editor');
			$_G['forum']['descriptionnew'] = html2bbcode($_G['forum']['description']);
			$jointypeselect[$_G['forum']['jointype']] = 'checked="checked"';
			$gviewpermselect[$_G['forum']['gviewperm']] = 'checked="checked"';
			if($_G['setting']['allowgroupdomain'] && !empty($_G['setting']['domain']['root']['group']) && $domainlength) {
				loadcache('creditrule');
				getuserprofile('extcredits1');
				$rule = $_G['cache']['creditrule']['modifydomain'];
				$credits = $consume = $common = '';
				for($i = 1; $i <= 8; $i++) {
					if($_G['setting']['extcredits'][$i] && $rule['extcredits'.$i]) {
						$consume .= $common.$_G['setting']['extcredits'][$i]['title'].$rule['extcredits'.$i].$_G['setting']['extcredits'][$i]['unit'];
						$credits .= $common.$_G['setting']['extcredits'][$i]['title'].$_G['member']['extcredits'.$i].$_G['setting']['extcredits'][$i]['unit'];
						$common = ',';
					}
				}
			}
		}
	} elseif($_GET['op'] == 'checkuser') {
		$checktype = 0;
		$checkusers = array();
		if(!empty($_GET['uid'])) {
			$checkusers = array($_GET['uid']);
			$checktype = intval($_GET['checktype']);
		} elseif(getgpc('checkall') == 1 || getgpc('checkall') == 2) {
			$checktype = $_GET['checkall'];
			$query = C::t('forum_groupuser')->fetch_all_by_fid($_G['fid'], 1);
			foreach($query as $row) {
				$checkusers[] = $row['uid'];
			}
		}
		if($checkusers) {
			
			// 一个成员只能加入一个球迷会 by zhangjh 2015-08-20
			if($checktype == 1)
			{
				$new_checkusers = array();
				$have_join = array();
				$str_tmp_user = '';
				foreach($checkusers as $uid)
				{
					$jioned = 0; 
					$arr_tmp = C::t('#fansclub#plugin_forum_groupuser')->fetch_all_group_by_uid(intval($uid));
					for($i = 0; $i < count($arr_tmp); $i++)
					{
						$forum_tmp = C::t('forum_forum')->fetch($arr_tmp[$i]['fid']);
						
						if($forum_tmp['status'] == 3) // 只计算群组
							$jioned++;
					}
					if($jioned > 0 && FALSE) // zhangjh 2015-09-09 取消只能加入一个球迷会
					{
						$have_join[] = $uid;
					}
					else
					{
						$new_checkusers[] = $uid;
					}
				}
				$checkusers = $new_checkusers;
				
				if(count($have_join) > 0)
				{
					$arr_tmp_user = C::t('common_member')->fetch_all_username_by_uid($have_join);
					$str_tmp_user = implode(',', $arr_tmp_user);
				}
			}
			
			if($checkusers)
			{
				foreach($checkusers as $uid) {
					$notification = $checktype == 1 ? 'group_member_check' : 'group_member_check_failed';
					notification_add($uid, 'group', $notification, array('fid' => $_G['fid'], 'groupname' => $_G['forum']['name'], 'url' => $_G['siteurl'].'forum.php?mod=group&fid='.$_G['fid']), 1);
				}
				if($checktype == 1) {
					C::t('forum_groupuser')->update_for_user($checkusers, $_G['fid'], null, null, 4);
					C::t('forum_forumfield')->update_membernum($_G['fid'], count($checkusers));
				} elseif($checktype == 2) {
					C::t('forum_groupuser')->delete_by_fid($_G['fid'], $checkusers);
				}
				if($checktype == 1) {
					showmessage('group_moderate_succeed', $url);
				} else {
					showmessage('group_moderate_failed', $url);
				}
			}
			else
			{
				showmessage($str_tmp_user.' 已经加入其他球迷会，请重新操作', $url);
			}
			
		} else {
			$checkusers = array();
			$userlist = C::t('forum_groupuser')->groupuserlist($_G['fid'], 'joindateline', $perpage, $start, array('level' => 0));
			$checknum = C::t('forum_groupuser')->fetch_count_by_fid($_G['fid'], 1);
			$multipage = multi($checknum, $perpage, $page, $url);
			foreach($userlist as $user) {
				$user['joindateline'] = date('Y-m-d', $user['joindateline']);
				$checkusers[$user['uid']] = $user;
			}
			
// 			var_dump($checkusers);die;
		}
	} elseif($_GET['op'] == 'manageuser') {
		$mtype = array(1 => lang('group/template', 'group_moderator'), 2 => lang('group/template', 'group_moderator_vice'), 3 => lang('group/template', 'group_star_member_title'), 4 => lang('group/misc', 'group_normal_member'), 5 => lang('group/misc', 'group_goaway'));
		
		// 2015-05-15 修改by zhangjh 
		// 取后台设置的等级名称
		include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php'); // 公共函数
		$arr_member = fansclub_get_member_level();
		if(count($arr_member) > 0)
		{
			$mtype = array();
			for($i = 0; $i < count($arr_member); $i++)
			{
				$mtype[$arr_member[$i]['id']] = '设为'.$arr_member[$i]['title'];
			}
			$mtype[5] = '踢出本会';
		}
		
		if(!submitcheck('manageuser')) {
			if (isset($_GET['muid'])) {
				$muid = intval($_GET['muid']);
				if(!$groupmanagers[$muid] || count($groupmanagers) > 1) {
					include DISCUZ_ROOT.'./source/plugin/rights/function/function_common.php';
					if (check_member_rights('member', $muid, 33)) {
						showmessage('此球迷拥有免被踢权益。', $url);
					} else {
						C::t('forum_groupuser')->delete_by_fid($_G['fid'], $muid);
						C::t('forum_forumfield')->update_membernum($_G['fid'], -1);
					}
					update_groupmoderators($_G['fid']);
					showmessage('group_setup_succeed', "forum.php?mod=group&action=memberlist&fid=".$_G['fid']);
				} else {
					showmessage('group_only_one_moderator', $url);
				}
			} else {
				$userlist = array();
				if ($_GET['mobile'] == 2 || $_GET['wap'] == 1) {	
					$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
					$perpage = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 20;
					$start = ($page - 1) * $perpage;
					
					$userlist = C::t('forum_groupuser')->groupuserlist($_G['fid'], '', $perpage, $start);
					foreach ($userlist as $key => $user) {
// 						$userlist[$key]['username'] = cutstr($user['username'], '8', '...');
						$userlist[$key]['avatar'] = avatar($user['uid'], 'small', true);
					}
					$maxpage = @ceil($_G['forum']['membernum']/$perpage);
					$nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);	
					$multipage_more = "forum.php?mod=group&action=manage&op=manageuser&fid=".$_G['fid']."&page=".$nextpage."&ajax=1";
					if ($_GET['ajax'] == 1) {
						echo json_encode(array('nextpage' => $nextpage, 'userlist' => $userlist));
						exit;
					}
// 					var_dump($userlist);die;
				} else {
					if(empty($_GET['srchuser'])) {
						$staruserlist = $page < 2 ? C::t('forum_groupuser')->groupuserlist($_G['fid'], '', 0, 0, array('level' => '3'), array('uid', 'username', 'level', 'joindateline', 'lastupdate')) : '';
						$adminuserlist = $groupmanagers && $page < 2 ? $groupmanagers : array();
						$multipage = multi($_G['forum']['membernum'], $perpage, $page, $url);
					} else {
						$start = 0;
					}
					$userlist = C::t('forum_groupuser')->groupuserlist($_G['fid'], '', $perpage, $start, $_GET['srchuser'] ? "AND username like '".addslashes($_GET[srchuser])."%'" : "AND level='4'");
				}
				
			}
			
// 			var_dump($userlist);die;
			
		} else {
			$muser = getgpc('muid');
			$targetlevel = $_GET['targetlevel'];
			if($muser && is_array($muser)) {
				foreach($muser as $muid => $mlevel) {
					if($_G['adminid'] != 1 && $_G['forum']['founderuid'] != $_G['uid'] && $groupmanagers[$muid] && $groupmanagers[$muid]['level'] <= $groupuser['level']) {
						showmessage('group_member_level_admin_noallowed.', $url);
					}
					if($_G['adminid'] == 1 || ($muid != $_G['uid'] && ($_G['forum']['founderuid'] == $_G['uid'] || !$groupmanagers[$muid] || $groupmanagers[$muid]['level'] > $groupuser['level']))) {
						if($targetlevel != 5) {
							C::t('forum_groupuser')->update_for_user($muid, $_G['fid'], null, null, $targetlevel);
						} else {
							if(!$groupmanagers[$muid] || count($groupmanagers) > 1) {
								include DISCUZ_ROOT.'./source/plugin/rights/function/function_common.php';
								if (check_member_rights('member', $muid, 33)) {
									showmessage('此球迷拥有免被踢权益。', $url);
								} else {
									C::t('forum_groupuser')->delete_by_fid($_G['fid'], $muid);
									C::t('forum_forumfield')->update_membernum($_G['fid'], -1);
								}								
							} else {
								showmessage('group_only_one_moderator', $url);
							}
						}
					}
				}
				update_groupmoderators($_G['fid']);
				showmessage('group_setup_succeed', $url.'&page='.$page);
			} else {
				showmessage('group_choose_member', $url);
			}
		}
		
	} elseif($_GET['op'] == 'threadtype') {
		if(empty($specialswitch['allowthreadtype'])) {
			showmessage('group_level_cannot_do');
		}
		if($_G['uid'] != $_G['forum']['founderuid'] && $_G['adminid'] != 1) {
			showmessage('group_threadtype_only_founder');
		}
		$typenumlimit = 20;
		if(!submitcheck('groupthreadtype')) {
			$threadtypes = $checkeds = array();
			if(empty($_G['forum']['threadtypes'])) {
				$checkeds['status'][0] = 'checked';
				$display = 'none';
			} else {
				$display = '';
				$_G['forum']['threadtypes']['status'] = 1;
				foreach($_G['forum']['threadtypes'] as $key => $val) {
					$val = intval($val);
					$checkeds[$key][$val] = 'checked';
				}
			}
			foreach(C::t('forum_threadclass')->fetch_all_by_fid($_G['fid']) as $type) {
				$type['enablechecked'] = isset($_G['forum']['threadtypes']['types'][$type['typeid']]) ? ' checked="checked"' : '';
				$type['name'] = dhtmlspecialchars($type['name']);
				$threadtypes[] = $type;
			}
		} else {
			$threadtypesnew = $_GET['threadtypesnew'];
			$threadtypesnew['types'] = $threadtypes['special'] = $threadtypes['show'] = array();
			if(is_array($_GET['newname']) && $_GET['newname']) {
				$newname = array_unique($_GET['newname']);
				if($newname) {
					foreach($newname as $key => $val) {
						$val = dhtmlspecialchars(censor(cutstr(trim($val), 16, '')));
						if($_GET['newenable'][$key] && $val) {
							$newtype = C::t('forum_threadclass')->fetch_by_fid_name($_G['fid'], $val);
							$newtypeid = $newtype['typeid'];
							if(!$newtypeid) {
								$typenum = C::t('forum_threadclass')->count_by_fid($_G['fid']);
								if($typenum < $typenumlimit) {
									$threadtypes_newdisplayorder = intval($_GET['newdisplayorder'][$key]);
									$newtypeid = C::t('forum_threadclass')->insert(array('fid' => $_G['fid'], 'name' => $val, 'displayorder' => $threadtypes_newdisplayorder), true);
								}
							}
							if($newtypeid) {
								$threadtypesnew['options']['name'][$newtypeid] = $val;
								$threadtypesnew['options']['displayorder'][$newtypeid] = $threadtypes_newdisplayorder;
								$threadtypesnew['options']['enable'][$newtypeid] = 1;
							}
						}
					}
				}
				$threadtypesnew['status'] = 1;
			} else {
				$newname = array();
			}
			if($threadtypesnew['status']) {
				if(is_array($threadtypesnew['options']) && $threadtypesnew['options']) {

					if(!empty($threadtypesnew['options']['enable'])) {
						$typeids = array_keys($threadtypesnew['options']['enable']);
					} else {
						$typeids = array(0);
					}
					if(!empty($threadtypesnew['options']['delete'])) {
						C::t('forum_threadclass')->delete_by_typeid_fid($threadtypesnew['options']['delete'], $_G['fid']);
					}
					foreach(C::t('forum_threadclass')->fetch_all_by_typeid_fid($typeids, $_G['fid']) as $type) {
						if($threadtypesnew['options']['name'][$type['typeid']] != $type['name'] || $threadtypesnew['options']['displayorder'][$type['typeid']] != $type['displayorder']) {
							$threadtypesnew['options']['name'][$type['typeid']] = dhtmlspecialchars(censor(cutstr(trim($threadtypesnew['options']['name'][$type['typeid']]), 16, '')));
							$threadtypesnew['options']['displayorder'][$type['typeid']] = intval($threadtypesnew['options']['displayorder'][$type['typeid']]);
							C::t('forum_threadclass')->update_by_typeid_fid($type['typeid'], $_G['fid'], array(
								'name' => $threadtypesnew['options']['name'][$type['typeid']],
								'displayorder' => $threadtypesnew['options']['displayorder'][$type['typeid']],
							));
						}
					}
				}
				if($threadtypesnew && $typeids) {
					foreach(C::t('forum_threadclass')->fetch_all_by_typeid($typeids) as $type) {
						if($threadtypesnew['options']['enable'][$type['typeid']]) {
							$threadtypesnew['types'][$type['typeid']] = $threadtypesnew['options']['name'][$type['typeid']];
						}
					}
				}
				$threadtypesnew = !empty($threadtypesnew) ? serialize($threadtypesnew) : '';
			} else {
				$threadtypesnew = '';
			}
			C::t('forum_forumfield')->update($_G['fid'], array('threadtypes' => $threadtypesnew));
			showmessage('group_threadtype_edit_succeed', $url);
		}
	} elseif($_GET['op'] == 'demise') {
		if((!empty($_G['forum']['founderuid']) && $_G['forum']['founderuid'] == $_G['uid']) || $_G['adminid'] == 1) {
			$ucresult = $allowbuildgroup = $groupnum = 0;
			if(count($groupmanagers) <= 1) {
				showmessage('group_cannot_demise');
			}

			if(submitcheck('groupdemise')) {
				$suid = intval($_GET['suid']);
				if(empty($suid)) {
					showmessage('group_demise_choose_receiver');
				}
				if(empty($_GET['grouppwd'])) {
					showmessage('group_demise_password');
				}
				loaducenter();
				$ucresult = uc_user_login($_G['uid'], $_GET['grouppwd'], 1);
				if(!is_array($ucresult) || $ucresult[0] < 1) {
					showmessage('group_demise_password_error');
				}
				$user = getuserbyuid($suid);
				loadcache('usergroup_'.$user['groupid']);
				$allowbuildgroup = $_G['cache']['usergroup_'.$user['groupid']]['allowbuildgroup'];
				if($allowbuildgroup > 0) {
					$groupnum = C::t('forum_forumfield')->fetch_groupnum_by_founderuid($suid);
				}
				if(empty($allowbuildgroup) || $allowbuildgroup - $groupnum < 1) {
					showmessage('group_demise_receiver_cannot_do');
				}
				C::t('forum_forumfield')->update($_G['fid'], array('founderuid' => $suid, 'foundername' => $user['username']));
				C::t('forum_groupuser')->update_for_user($suid, $_G['fid'], NULL, NULL, 1);
				update_groupmoderators($_G['fid']);
				sendpm($suid, lang('group/misc', 'group_demise_message_title', array('forum' => $_G['forum']['name'])), lang('group/misc', 'group_demise_message_body', array('forum' => $_G['forum']['name'], 'siteurl' => $_G['siteurl'], 'fid' => $_G['fid'])), $_G['uid']);
				showmessage('group_demise_succeed', 'forum.php?mod=group&action=manage&fid='.$_G['fid']);
			}
		} else {
			showmessage('group_demise_founder_only');
		}
	} else {
		showmessage('undefined_action');
	}
	
	$nobbname = TRUE;
	$navtitle = $_G['forum']['name'].'管理_'.$_G['setting']['bbname'].'球迷会';
	$metakeywords = $_G['forum']['name'].'管理';
	$metadescription = '管理'.$_G['forum']['name'].'的帖子、成员以及版块其他事项。';

	include template('diy:group/group:'.$_G['fid']);

} elseif($action == 'recommend') {
	if(!$_G['forum']['ismoderator'] || !in_array($_G['adminid'], array(1,2))) {
		showmessage('group_admin_noallowed');
	}
	if(submitcheck('grouprecommend')) {
		if($_GET['recommend'] != $_G['forum']['recommend']) {
			C::t('forum_forum')->update($_G['fid'], array('recommend' => intval($_GET['recommend'])));
			require_once libfile('function/cache');
			updatecache('forumrecommend');
		}
		showmessage('grouprecommend_succeed', '', array(), array('alert' => 'right', 'closetime' => true, 'showdialog' => 1));
	} else {
		require_once libfile('function/forumlist');
		$forumselect = forumselect(FALSE, 0, $_G['forum']['recommend']);
	}
	include template('group/group_recommend');
} elseif ($action == 'introduce') {			//add by Daming 2015/8/26 for wap
	ini_set('display_errors', 'on');
	$PNG_TEMP_DIR = DISCUZ_ROOT.'data'.DIRECTORY_SEPARATOR.'attachment'.DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
	
	include DISCUZ_ROOT.'source/class/phpqrcode/phpqrcode.php';
	$status = groupperm($_G['forum'], $_G['uid'], $action, $groupuser);
	
	$filename = $PNG_TEMP_DIR.'qrcode_'.$_G['uid'].'.png';
	if (!file_exists($filename)) {
		$urlToEncode = "$_G[siteurl]forum.php?mod=group&fid=$_G[fid]&mobile=2";
		$errorCorrectionLevel = "L"; 
		$matrixPointSize = "7"; 
		
		QRcode::png($urlToEncode, $filename, $errorCorrectionLevel, $matrixPointSize);
		chmod($filename, 0777);
	}	
	include template('diy:group/group:'.$_G['fid']);
} elseif ($action == 'activity') {			//add by Daming 2015/9/1 for wap
	$status = groupperm($_G['forum'], $_G['uid'], $action, $groupuser);
	
	$count = C::t('forum_thread')->count_thread_by_fid_type($_G['fid'], 4);
	
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	$perpage = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 10;
	
	$threadlist = array();
	
	$start = ($page - 1)*$perpage;
	$activitylist = C::t('forum_thread')->fetch_all_by_fid_special_displayorder($_G['fid'], 0, 4, $start, $perpage);

	require_once libfile('function/extends');
	foreach ($activitylist as $key => $value) {
		$threadlist[$key]['tid'] = $value['tid'];
		$threadlist[$key]['title'] = $value['subject'];
// 		$threadlist[$key]['author_avatar'] = avatar($value['authorid'], 'small', 1);
		$activity = C::t('forum_activity')->fetch($value['tid']);		
		$threadlist[$key]['starttimefrom'] = date('Y-m-d',$activity['starttimefrom']);
		$threadlist[$key]['starttimeto'] = $activity['starttimeto'] ? date('Y-m-d', $activity['starttimeto']) : 0;
		$threadlist[$key]['status'] = $activity['starttimeto'] && TIMESTAMP > $activity['starttimeto'] ? 0 : 1;
						
		if($activity['aid']) {			
			$threadlist[$key]['thumb'] = getforumimg($activity['aid'], 0, 320, 170, 2);
		}
	}
		
	$maxpage = @ceil($count/$perpage);
	$nextpage = ($page + 1) > $maxpage ? 1 : ($page + 1);
		
	$multipage_more = "forum.php?mod=group&action=activity&fid=".$_G['fid']."&page=".$nextpage."&ajax=1";
	if ($_GET['ajax'] == 1) {
		$result = array('nextpage' => $nextpage, 'threadlist' => $threadlist);
		echo json_encode($result);
		exit;
	}
	
// 	var_dump($threadlist);die;	
// 	var_dump($count);die;
	include template('diy:group/group:'.$_G['fid']);
} 

function isadminbyuid($uid, $fid) {
	$query = DB::fetch_first("SELECT level FROM ".DB::table('forum_groupuser')." WHERE `uid`=$uid AND `fid`=$fid");
	if ($query['level'] == 1) {
		return true;
	} else {
		return false;
	}
}


?>