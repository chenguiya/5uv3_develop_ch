<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

$op = trim($_GET['op']);
$apply_id = intval($_GET['apply_id']);

if($op == 'join')
{
	if(!$_G['uid']) fansclub_showsuccess('请要先登录','member.php?mod=logging&action=login', 'notice'); // 要先登录
	if(submitcheck('apply_support_submit')) fansclub_apply_support($apply_id, TRUE);
	exit();
}
else
{
	// 默认显示记录
	$apply = C::t('#fansclub#plugin_fansclub_apply_log')->fetch($apply_id);
	
	if(count($apply) > 0) 
	{
		$formhash = FORMHASH;
		$support = C::t('#fansclub#plugin_fansclub_apply_support')->fetch_all_by_ids($apply_id);
		for($i = 0; $i < count($support); $i++)
		{
			$support[$i]['avatar_url'] = $_G['siteurl'].'uc_server/avatar.php?uid='.$support[$i]['uid'].'&size=middle';
		}
		
		$apply['differ'] = $apply['need_support'] - $apply['have_support'];
		$apply['url'] = $_G['siteurl'].'plugin.php?id=fansclub&ac=apply_support&apply_id='.$apply['apply_id'];
		
		$fansclub_relation = ''; // 球迷会类别
		$fansclub_relation .= ($apply['league_id'] > 0) ? fansclub_get_forum_name($apply['league_id']) : '';
		$fansclub_relation .= ($apply['club_id'] > 0) ? ' - '.fansclub_get_forum_name($apply['club_id']) : '';
		$fansclub_relation .= ($apply['star_id'] > 0) ? ' - '.fansclub_get_forum_name($apply['star_id']) : '';
		
		$fansclub_range = ''; // 球迷会范围
		$fansclub_range .= ($apply['province_id'] > 0) ? fansclub_get_district_name($apply['province_id']) : '';
		$fansclub_range .= ($apply['city_id'] > 0) ? ' - '.fansclub_get_district_name($apply['city_id']) : '';
		$fansclub_range .= ($apply['district_id'] > 0) ? ' - '.fansclub_get_district_name($apply['district_id']) : '';
		$fansclub_range .= ($apply['community_id'] > 0) ? ' - '.fansclub_get_district_name($apply['community_id']) : '';
		
		$fansclub_logo = ($apply['fansclub_logo'] != '') ? '<img src="'.$_G['siteurl'].'data/attachment/group/'.$apply['fansclub_logo'].'">' : '没有';
		
		$apply['url_short'] = $apply['url'];
		
	}
	else
	{
		showmessage('没有记录','plugin.php?id=fansclub:fansclub');
	}
}
