<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: space_notice.php 34047 2013-09-25 04:41:45Z nemohou $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$perpage = 30;
$perpage = mob_perpage($perpage);

$page = empty($_GET['page'])?0:intval($_GET['page']);
if($page<1) $page = 1;
$start = ($page-1)*$perpage;

ckstart($start, $perpage);

$list = array();
$mynotice = $count = 0;
$multi = '';

if(empty($_G['member']['category_num']['manage']) && !in_array($_G['adminid'], array(1,2,3))) {
	unset($_G['notice_structure']['manage']);
}
$view = (!empty($_GET['view']) && (isset($_G['notice_structure'][$_GET[view]]) || in_array($_GET['view'], array('userapp'))))?$_GET['view']:'mypost';
$actives = array($view=>' class="a"');
$opactives[$view] = 'class="a"';
$categorynum = $newprompt = array();
$type_wap = trim($_GET['type']);  //用于模板判断
//$_G['uid'] = 1844;
if($view == 'userapp') {

	space_merge($space, 'status');

	if($_GET['op'] == 'del') {
		$appid = intval($_GET['appid']);
		C::t('common_myinvite')->delete_by_appid_touid($appid, $_G['uid']);
		showmessage('do_success', "home.php?mod=space&do=notice&view=userapp&quickforward=1");
	}

	$filtrate = 0;
	$count = 0;
	$apparr = array();
	$type = intval($_GET['type']);
	foreach(C::t('common_myinvite')->fetch_all_by_touid($_G['uid']) as $value) {
		$count++;
		$key = md5($value['typename'].$value['type']);
		$apparr[$key][] = $value;
		if($filtrate) {
			$filtrate--;
		} else {
			if($count < $perpage) {
				if($type && $value['appid'] == $type) {
					$list[$key][] = $value;
				} elseif(!$type) {
					$list[$key][] = $value;
				}
			}
		}
	}
	$mynotice = $count;

} else {
	if(!empty($_GET['ignore'])) {
		C::t('home_notification')->ignore($_G['uid']);
	}
	foreach (array('wall', 'piccomment', 'blogcomment', 'clickblog', 'clickpic', 'sharecomment', 'doing', 'friend', 'credit', 'bbs', 'system', 'thread', 'task', 'group') as $key) {
		$noticetypes[$key] = lang('notification', "type_$key");
	}

	$isread = in_array($_GET['isread'], array(0, 1)) ? intval($_GET['isread']) : 0;
	$category = $type = '';
	if(isset($_G['notice_structure'][$view])) {
		if(!in_array($view, array('mypost', 'interactive'))) {
			$category = $view;
		} else {
			$deftype = $_G['notice_structure'][$view][0];
			if($_G['member']['newprompt_num']) {
				foreach($_G['notice_structure'][$view] as $subtype) {
					if($_G['member']['newprompt_num'][$subtype]) {
						$deftype = $subtype;
						break;
					}
				}
			}
			$type = in_array($_GET['type'], $_G['notice_structure'][$view]) ? trim($_GET['type']) : $deftype;
		}
	}
	$wherearr = array();
	$new = -1;
	if(!empty($type)) {
		$wherearr[] = "`type`='$type'";
	}

	$sql = ' AND '.implode(' AND ', $wherearr);

                       
	$newnotify = false;
	$count = C::t('home_notification')->count_by_uid($_G['uid'], $new, $type, $category);
	if($count) {
		if($new == 1 && $perpage == 30) {
			$perpage = 200;
		}
		foreach(C::t('home_notification')->fetch_all_by_uid($_G['uid'], $new, $type, $start, $perpage, $category) as $value) {
			if($value['new']) {
				$newnotify = true;
				$value['style'] = 'color:#000;font-weight:bold;';
			} else {
				$value['style'] = '';
			}
			$value['rowid'] = '';
			if(in_array($value['type'], array('friend', 'poke'))) {
				$value['rowid'] = ' id="'.($value['type'] == 'friend' ? 'pendingFriend_' : 'pokeQuery_').$value['authorid'].'" ';
			}
			if($value['from_num'] > 0) $value['from_num'] = $value['from_num'] - 1;
			$list[$value['id']] = $value;
		}

		$multi = '';
		$multi = multi($count, $perpage, $page, "home.php?mod=space&do=$do&isread=1");
	}

	if($newnotify) {
		C::t('home_notification')->ignore($_G['uid'], $type, $category, true, true);
		if($_G['setting']['cloud_status']) {
			$noticeService = Cloud::loadClass('Service_Client_Notification');
			$noticeService->setNoticeFlag($_G['uid'], TIMESTAMP);
		}
	}
	helper_notification::update_newprompt($_G['uid'], ($type ? $type : $category));
	if($_G['setting']['my_app_status']) {
		$mynotice = C::t('common_myinvite')->count_by_touid($_G['uid']);
	}
	if($_G['member']['newprompt']) {
		$recountprompt = 0;
		foreach($_G['member']['category_num'] as $promptnum) {
			$recountprompt += $promptnum;
		}
		$recountprompt += $mynotice;
		if($recountprompt == 0) {
			C::t('common_member')->update($_G['uid'], array('newprompt' => 0));
		}
	}

	$readtag = array($type => ' class="a"');
                        //回复我的
                       if($type_wap == 'post'){
                            $arr = array();
                            $arr_ = array();
                            foreach(C::t('forum_thread')->my_fetch_all_by_authorid($_G['uid']) as $key=>$value){         
                                       //$arr [$key]['subject'] = $value['subject'];
                                       foreach(C::t('forum_post')->fetch_all_by_tid_authorid(null,$value['tid'],$_G['uid']) as $key1=>$value1){
                                              $arr [$key][$key1]['tid'] = $value['tid'];
                                              $arr [$key][$key1]['subject'] = $value['subject'];
                                              $arr [$key][$key1]['dateline'] = date('Y年n月d日 H:i',$value1 ['dateline']);
                                              $arr [$key][$key1]['author'] = $value1 ['author'];
                                              $arr [$key][$key1]['authorid'] = $value1 ['authorid'];
                                       }
                            }
                            foreach($arr as $k=>$v){
                                 foreach($v as $k1=>$v1){
                                     $arr_[]=$v1;
                                 }
                            }
                       }
                       //活动提醒
                       if($type_wap == 'activity'){
                            $a_arr = array();
                            $a_arr_ = array();
                            $type_ma = count(C::t('forum_activity')->fetch_tid_by_uid($_G['uid']));   //是否有创建活动
                           if($type_ma == 0){
                                  //申请加入活动
                                  foreach(C::t('forum_activityapply')->fetch_info_by_uid($_G['uid']) as $key=>$value){
                                            //主要是活动名称
                                            foreach(C::t('forum_thread')->fetch_all_by_tid_displayorder($value['tid']) as $key1=>$value1){
                                                  $a_arr [$key][$key1]['userid'] = $_G['uid'];
                                                  $a_arr [$key][$key1]['tid'] = $value['tid'];
                                                  $a_arr [$key][$key1]['username'] = $value['username'];
                                                  $a_arr [$key][$key1]['dateline'] = date('Y年n月d日 H:i',$value['dateline']);
                                                  $a_arr [$key][$key1]['subject'] = $value1['subject'] ;
                                           }
                                   }
                           }  else {
                                  foreach((C::t('forum_activity')->fetch_tid_by_uid($_G['uid'])) as $k=>$v){
                                            foreach (C::t('forum_activityapply')->fetch_info_by_tid($v['tid']) as $k1=>$v1){
                                                  if($v1['uid'] != $_G['uid']){
                                                          $a_arr [$k][$k1]['tid'] = $v['tid'];
                                                          $a_arr [$k][$k1]['userid'] = $v1['uid'];
                                                          $a_arr [$k][$k1]['username'] = $v1['username']; 
                                                          $a_arr [$k][$k1]['dateline'] = date('Y年n月d日 H:i',$v1['dateline']);
                                                          foreach(C::t('forum_thread')->fetch_all_by_tid_displayorder($v['tid']) as $k2=>$v2){
                                                                $a_arr [$k][$k1]['subject'] = $v2['subject'] ;
                                                            }
                                                  }
                                            }

                                        }

                           }    
                        foreach($a_arr as $k=>$v){
                                 foreach($v as $k1=>$v1){
                                       $a_arr_[]=$v1;
                                  }
                          }
                       }                       
                  //  echo "<pre>";
                  //  print_r($arr_);exit;
}

dsetcookie('promptstate_'.$_G['uid'], $newprompt, 31536000);
include_once template("diy:home/space_notice");

?>