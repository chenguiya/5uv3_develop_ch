<?php
if(!defined('IN_DISCUZ')) exit('Access Denied');

$data = array();

if($_G['gp_league_id'] != '')
{
	$data['league_id'] = intval($_G['gp_league_id']);
}

if($_G['gp_match_status'])
{
	$data['match_status'] = intval($_G['gp_match_status']);
}

$arr_game = fansclub_get_live2($data);
$arr_game_ = array();

foreach($arr_game['have_end']['game_array'] as $k=>$v){
	foreach($v['games'] as $k1=>$v1){
		$arr_game_[] = $v1;
		$arr_game_[$k]['week'] = $v['week'];
	}	
}
for($i=0;$i<7;$i++){
	$arr_game['have_end_'][$i] = $arr_game_[$i];
}


//echo "<pre>";
//print_r($arr_game['have_end_']);exit;

/**
 * 上面是之前的
 * 先做 <未结束>和<已结束>两种状态，<未结束>排序用从现在到未来，<已结束>保留一个星期的赛程，排序用从现在到以前
 */
