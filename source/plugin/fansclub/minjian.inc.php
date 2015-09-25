<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php'); // 公共函数

$ac = $_GET['ac'] ? $_GET['ac'] : 'index';
$arr = array('index', 'inegral', 'match', 'shooter'); // 只允许的action
if(!in_array($ac, $arr)) showmessage('undefined_action');

if(strpos($_SERVER['HTTP_HOST'], '5usport.com') !== FALSE)
{
}
else
{
	$_G['config']['playerdata']['domian'] = 'http://cid.usport.cc'; // 内网要修改的
	$_G['config']['playerdata']['domian'] = 'http://zhangjh.dev.usport.cc';
}

$mem_check = memory('check'); // 先检查缓存是否生效
if($mem_check != '') // 可以用缓存
{
	$team_list = memory('get', 'fansclub_minjian_arr_team_list');
	$match_list = memory('get', 'fansclub_minjian_arr_match_list');
	$ranking_list = memory('get', 'fansclub_minjian_arr_ranking_list');
	$shooter = memory('get', 'fansclub_minjian_arr_shooter');
	$assists = memory('get', 'fansclub_minjian_arr_assists');
	$bln_need_load = !is_array($team_list) ? TRUE : FALSE;
}
else
{
	$bln_need_load = TRUE;
}

if($ac == 'index')
{
	$data = array('league_id' => '1');
	if($bln_need_load)
	{
		$team_url = fansclub_get_api_url($data, 'uadata/get_team');
		$team_result = json_decode(curl_access($team_url), TRUE);
		$team_list = $team_result['team_list'];
		memory('set', 'fansclub_minjian_arr_team_list', $team_list, 60*60*24);
		
		$match_url = fansclub_get_api_url($data, 'uadata/get_match');
		$match_result = json_decode(curl_access($match_url), TRUE);
		$match_list = $match_result['match_list'];
		memory('set', 'fansclub_minjian_arr_match_list', $match_list, 60*60*24);
		
		$ranking_url = fansclub_get_api_url($data, 'uadata/get_ranking');
		$ranking_result = json_decode(curl_access($ranking_url), TRUE);
		$ranking_list = $ranking_result['ranking_list'];
		memory('set', 'fansclub_minjian_arr_ranking_list', $ranking_list, 60*60*24);
		
		$shooter_url = fansclub_get_api_url($data, 'uadata/get_shooter');
		$shooter_result = json_decode(curl_access($shooter_url), TRUE);
		$shooter = $shooter_result['shooter_list'];
		memory('set', 'fansclub_minjian_arr_shooter', $shooter, 60*60*24);
		
		$assists_url = fansclub_get_api_url($data, 'uadata/get_assists');
		$assists_result = json_decode(curl_access($assists_url), TRUE);
		$assists = $assists_result['assists_list'];
		memory('set', 'fansclub_minjian_arr_assists', $assists, 60*60*24);
	}
}
elseif($ac == 'inegral')
{
}
elseif($ac == 'match')
{
}
elseif($ac == 'shooter')
{
}


include template('common/header');
include template('fansclub:minjian/header');
include template('fansclub:minjian/'.$ac);
include template('common/footer');
