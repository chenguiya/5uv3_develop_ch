<?php if(!defined('IN_DISCUZ')) exit('Access Denied');

include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php'); // 公共函数

// $ac = $_GET['ac'] ? $_GET['ac'] : 'index';
$arr = array('index', 'inegral', 'match', 'shooter', 
    'channel_data', 'channel_index', 'channel_introduce','channel_member',
    'forumdisplay_index','forumdisplay_list', 'forumdisplay_move','forumdisplay_schedule', 'forumdisplay_team', 'forumdisplay_shooter', 'forumdisplay_inegral',
    'forumdisplay_mvp','forumdisplay_notice','forumdisplay_history','forumdisplay_data'); // 只允许的action
if(!in_array($ac, $arr)) showmessage('undefined_action');

if(strpos($_SERVER['HTTP_HOST'], '5usport.com') !== FALSE) // 正式服
{
}
else
{
    $_G['config']['playerdata']['domian'] = 'http://cid.usport.cc'; // 内网要修改的
    $_G['config']['playerdata']['domian'] = 'http://zhangjh.dev.usport.cc';
}

/*
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
*/

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
elseif(in_array($ac, array('channel_data', 'channel_index', 'channel_introduce','channel_member',
    'forumdisplay_index', 'forumdisplay_list', 'forumdisplay_move', 'forumdisplay_schedule', 'forumdisplay_team', 'forumdisplay_shooter', 'forumdisplay_inegral',
    'forumdisplay_mvp','forumdisplay_notice','forumdisplay_history','forumdisplay_data')))
{
    // 查数据方式同 forum.php?mod=group&fid=372&mobile=2
    // 页头数据
    // echo "<pre>";
    // echo $local_name."\n";
    // echo $_G['fid']."\n";
    // print_r($_G['forum'])."\n";
    // exit;
    // echo "查数据方式同 forum.php?mod=group&fid=372&mobile=2";
    /*
    
    -- 2015-11-03 改成下面的了
    http://www.usport.com.cn/forum.php?mod=forumdisplay&fid=1390&ac=channel_data OK
    http://www.usport.com.cn/forum.php?mod=forumdisplay&fid=1390&ac=channel_index
    http://www.usport.com.cn/forum.php?mod=forumdisplay&fid=1390&ac=channel_introduce
    http://www.usport.com.cn/forum.php?mod=forumdisplay&fid=1390&ac=channel_member
    http://www.usport.com.cn/forum.php?mod=forumdisplay&fid=1390&ac=forumdisplay_index
    http://www.usport.com.cn/forum.php?mod=forumdisplay&fid=1390&ac=forumdisplay_list
    http://www.usport.com.cn/forum.php?mod=forumdisplay&fid=1390&ac=forumdisplay_move
    http://www.usport.com.cn/forum.php?mod=forumdisplay&fid=1390&ac=forumdisplay_schedule
    http://www.usport.com.cn/forum.php?mod=forumdisplay&fid=1390&ac=forumdisplay_team
    
    */
}

if(defined('IN_MOBILE'))
{
    if($ac == 'channel_data') // 这个用其他目录下的模版
    {
        $arr = C::t('#fansclub#plugin_fansclub_ua_league_integral_shooter')->fetch(1);
        $integral_data = json_decode($arr['integral_data'], true);
        $shooter_data = json_decode($arr['shooter_data'], true);
    }
    include template('touch/forum/'.$ac);
}
else
{
    // die('The web version is coming soon...');
    include template('common/header');
    include template('fansclub:minjian/header');
    include template('fansclub:minjian/'.$ac);
    include template('common/footer');
}
