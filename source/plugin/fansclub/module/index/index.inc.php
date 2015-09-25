<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

require_once libfile('function/extends');
$_rightsdettings = $_G['cache']['plugin']['rights'];

// 2015-08-04 zhangjh 球迷联盟社区1.1
$search = trim($_G['gp_search']);
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

if($search != '') // 有搜索的
{
	$search_info = C::t('#fansclub#plugin_forum_forum')->fetch_group_by_name($search);
	if(count($search_info) == 0)
	{
		showmessage('没有找到记录', '');
	}
}

$mem_check = memory('check'); // 先检查缓存是否生效
$arr_group_show = $arr_province_city = FALSE;

if($mem_check != '' && $search == '') // 有搜索的不用缓存
{
	$arr_group_show = memory('get', 'fansclub_arr_group_show');
	$arr_province_city = memory('get', 'fansclub_arr_province_city');
}

if($arr_group_show == FALSE || $arr_province_city == FALSE || TRUE) // 暂时不用CACHE
{
	$arr_group_show = array(); // 右下群组
	// ====================================== 全部群组 ======================================
	$arr_province_city = fansclub_get_province_city(TRUE, TRUE); // 省市数组
	
	$conditions = '1 = 1';
	if($cityid != 0)
	{
		$conditions .= ' AND f.city_id = '.$cityid;
	}
	if($search != '')
	{
		$conditions .= ' AND f.relation_fid = '.$search_info[0]['fid'];
	}
	
	// 更新人数等
	C::t('#fansclub#plugin_fansclub_info')->update_info_from_forum();
	 
	// 加分页
	$limit = 8;
	$arr_group_list = C::t('#fansclub#plugin_fansclub_info')->fetch_all_for_search($conditions, ($page - 1) * $limit, $limit, 'members'); // 列表
	$group_count = C::t('#fansclub#plugin_fansclub_info')->fetch_all_for_search($conditions, -1); // 总条数
	$multipage = fansclub_multi($group_count, $limit, $page, 'group/', '');
	
	for($i = 0; $i < count($arr_group_list); $i++)
	{
		$_arr_forumfield = C::t('forum_forumfield')->fetch($arr_group_list[$i]['fid']);
		$arr_group_show[$i] = $arr_group_list[$i];
		$arr_group_show[$i]['description_short'] = str_intercept($_arr_forumfield['description'], 0, 17);
		$arr_group_show[$i]['banner'] = $_G['siteurl'].'data/attachment/group/'.$_arr_forumfield['banner'];
		$arr_group_show[$i]['icon'] = $_G['siteurl'].'data/attachment/group/'.$_arr_forumfield['icon'];
		
		//判断球迷会是否有球迷会特权
		if ($_rightsdettings['open'] == 1 && checkmemberrights(35, $arr_group_list[$i]['fid'], 'clubid'))
		{
			$arr_group_show[$i]['specialclub'] = TRUE;
		}
		else
		{
			$arr_group_show[$i]['specialclub'] = FALSE;
		}
	}
	
	if($mem_check != '')
	{
		if($search == '' && $cityid == 0)
		{
			memory('set', 'fansclub_arr_group_show', $arr_group_show, 60*60);
			memory('set', 'fansclub_arr_province_city', $arr_province_city, 60*60);
		}
		else
		{
			memory('rm', 'fansclub_arr_group_show');
			memory('rm', 'fansclub_arr_province_city');
		}
	}
}

// 优秀会长
include_once libfile('function/extends');
$chairmanlists = C::t('#fansclub#plugin_fansclub_info')->fetch_all_for_search('1 = 1', 0, 3, 'members'); // 列表
for($i = 0; $i < count($chairmanlists); $i++)
{
	//获取球迷会信息
	$clubinfo = C::t('#fansclub#plugin_fansclub_info')->fetch_fansclub_info_by_fid($chairmanlists[$i]['fid']);
	
	//获取球迷会会长信息
	$userinfo = DB::fetch_first('SELECT * FROM '.DB::table('forum_groupuser').' WHERE fid='.$chairmanlists[$i]['fid']." AND level=1");
	$chairmanlists[$i]['checkrights'] = userrightsperm(32, $chairmanlists[$i]['fid']);
	$chairmanlists[$i]['name'] = $clubinfo['name'];			
	$chairmanlists[$i]['uid'] = $userinfo['uid'];			//会长用户id
	$chairmanlists[$i]['avatar'] = avatar($userinfo['uid'], 'middle', true);
	$chairmanlists[$i]['username'] = $userinfo['username'];		//会长用户名
	$userfield = DB::fetch_first("SELECT * FROM ".DB::table('common_member_field_forum')." WHERE uid=".intval($userinfo['uid']));
	$chairmanlists[$i]['sign'] = $userfield['sightml'];
}
