<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: thread_activity.php 28709 2012-03-08 08:53:48Z liulanbo $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$isverified = $applied = 0;
$ufielddata = $applyinfo = '';
if($_G['uid']) {
	$applyinfo = C::t('forum_activityapply')->fetch_info_for_user($_G['uid'], $_G['tid']);
	if($applyinfo) {
		$isverified = $applyinfo['verified'];
		if($applyinfo['ufielddata']) {
			$ufielddata = dunserialize($applyinfo['ufielddata']);
		}
		$applied = 1;
	}
}
//eidt by Daming 2015/9/28 添加签到人员列表
$applylist = $signedlist = array();
$activity = C::t('forum_activity')->fetch($_G['tid']);
$activityclose = $activity['expiration'] ? ($activity['expiration'] > TIMESTAMP ? 0 : 1) : 0;
$activity['starttimefrom_u'] = $activity['starttimefrom'];
// var_dump($activity['starttimefrom_u']);die;
$activity['starttimefrom'] = dgmdate($activity['starttimefrom'], 'u');
$activity['starttimeto'] = $activity['starttimeto'] ? dgmdate($activity['starttimeto']) : 0;
$activity['expiration'] = $activity['expiration'] ? dgmdate($activity['expiration']) : 0;
$activity['attachurl'] = $activity['thumb'] = '';
if($activity['ufield']) {
	$activity['ufield'] = dunserialize($activity['ufield']);
	if($activity['ufield']['userfield']) {
		$htmls = $settings = array();
		require_once libfile('function/profile');
		foreach($activity['ufield']['userfield'] as $fieldid) {
			if(empty($ufielddata['userfield'])) {
				$memberprofile = C::t('common_member_profile')->fetch($_G['uid']);
				foreach($activity['ufield']['userfield'] as $val) {
					$ufielddata['userfield'][$val] = $memberprofile[$val];
				}
				unset($memberprofile);
			}
			$html = profile_setting($fieldid, $ufielddata['userfield'], false, true);
			if($html) {
				$settings[$fieldid] = $_G['cache']['profilesetting'][$fieldid];
				$htmls[$fieldid] = $html;
			}
		}
	}
} else {
	$activity['ufield'] = '';
}

if($activity['aid']) {
	$attach = C::t('forum_attachment_n')->fetch('tid:'.$_G['tid'], $activity['aid']);
	if($attach['isimage']) {
		$activity['attachurl'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
		$activity['thumb'] = $attach['thumb'] ? getimgthumbname($activity['attachurl']) : $activity['attachurl'];
		$activity['width'] = $attach['thumb'] && $_G['setting']['thumbwidth'] < $attach['width'] ? $_G['setting']['thumbwidth'] : $attach['width'];
	}
	$skipaids[] = $activity['aid'];
}

//edit by Daming 2015/9/28 添加未签到人员列表
$applylistverified = $nosignedlist = array();
$noverifiednum = $nosignednum = 0;
$query = C::t('forum_activityapply')->fetch_all_for_thread($_G['tid'], 0, 0, 0, 1);
require_once libfile('function/extends');
foreach($query as $activityapplies) {
	$activityapplies['dateline'] = dgmdate($activityapplies['dateline'], 'u');
	$fielddata = dunserialize($activityapplies['ufielddata']);
	// 		var_dump($fielddata);die;
	//edit by Daming 用于加密显示身份证号码、电话号码
	$activityapplies['mobile'] = $fielddata['userfield']['mobile'];
	$activityapplies['en_mobile'] = encryptionDisplay($fielddata['userfield']['mobile'], 3, 4);
	$activityapplies['realname'] = $fielddata['userfield']['realname'];
	//end
	if($activityapplies['verified'] == 1) {		
		if(count($applylist) < $_G['setting']['activitypp']) {
			$activityapplies['message'] = preg_replace("/(".lang('forum/misc', 'contact').".*)/", '', $activityapplies['message']);
			$applylist[] = $activityapplies;
		}
	} else {
		if(count($applylistverified) < 8) {
			$applylistverified[] = $activityapplies;
		}
		$noverifiednum++;
	}
	
// 	var_dump($applylistverified);die;
}
//add by Daming 2015/09/28
$query = DB::fetch_all("SELECT * FROM ".DB::table('forum_activityapply')." WHERE tid=".$_G['tid']." AND verified=1 ORDER BY sign_time DESC");
// var_dump($query);die;
foreach ($query as $activityapplies) {
	$activityapplies['sign_time'] = dgmdate($activityapplies['sign_time'], 'u');
	$activityapplies['dateline'] = dgmdate($activityapplies['dateline'], 'u');
	$userfield = dunserialize($activityapplies['ufielddata']);
	// 		var_dump($userfield);die;
	$activityapplies['realname'] = $userfield['userfield']['realname'];
	$activityapplies['mobile'] = $userfield['userfield']['mobile'];
	$activityapplies['en_mobile'] = encryptionDisplay($userfield['userfield']['mobile'], 3, 4);
	if($activityapplies['registration'] == 1) {				
		if(count($signedlist) < $_G['setting']['activitypp']) {
			$activityapplies['message'] = preg_replace("/(".lang('forum/misc', 'contact').".*)/", '', $activityapplies['message']);				
			$signedlist[] = $activityapplies;			
		}
	} else {
		if(count($nosignedlist) < $_G['setting']['activitypp']) {
			$nosignedlist[] = $activityapplies;
		}
		$nosignednum++;
	}
}

// var_dump($signedlist);die;


$applynumbers = $activity['applynumber'];
$signednum = C::t('forum_activityapply')->count_signed_by_tid_issign($_G['tid']);
// var_dump($signednum);die;
$nosignednum = C::t('forum_activityapply')->count_signed_by_tid_issign($_G['tid'], 0);
$aboutmembers = $activity['number'] >= $applynumbers ? $activity['number'] - $applynumbers : 0;
$allapplynum = $applynumbers + $noverifiednum;
if($_G['forum']['status'] == 3) {
	$isgroupuser = groupperm($_G['forum'], $_G['uid']);
}
?>