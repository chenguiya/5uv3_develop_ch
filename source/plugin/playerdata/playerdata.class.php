<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_playerdata{
	function __construct() {
		loadcache('plugin');
		include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php'); // 公共函数
	}
	
	
	
	
}

 
 
class plugin_playerdata_forum extends plugin_playerdata {
	
	//联赛排名
	function forumdisplay_league_rank() {
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;
		$data_domain = $_G['config']['playerdata']['domian'];
		$teamid=fansclub_get_team_player_id($_GET['fid']);  //通过fid获取球队ID
		if($_G['forum']['type'] == 'sub'){
			//如果是球员ID，则需要转换成球队ID
			$url=$data_domain."/v3/v3_api/get_info/getPlayer/SDsFJO4dS3D4dF64SDF46?id=".$teamid;
			$player = get_urldata($url);
			$teamid = $player->team_id;
		}
		$url=$data_domain."/v3/v3_api/get_info/getTeamById/SDsFJO4dS3D4dF64SDF46?id=".$teamid;
		$team= get_urldata($url);
		$url=$data_domain."/v3/v3_api/get_info/getScoreboardByTeamidLeagueid/SDsFJO4dS3D4dF64SDF46?team_id=".$teamid."&league_id=".$team->league_id;
		$scoreboard = get_urldata($url);
		include template("playerdata:channel_league_rank");
		return $html;
	}
	
	//赛程预告
	function forumdisplay_match_foreshow() {
		return ''; // 这个已不使用 by zhangjh 2015-08-27
		global $_G;
		$data_domain = $_G['config']['playerdata']['domian'];
		$teamid=fansclub_get_team_player_id($_GET['fid']);  //通过fid获取球队ID
		if($_G['forum']['type'] == 'sub'){
					//如果是球员ID，则需要转换成球队ID
					$url=$data_domain."/v3/v3_api/get_info/getPlayer/SDsFJO4dS3D4dF64SDF46?id=".$teamid;
					$player = get_urldata($url);
					$teamid = $player->team_id;
				}
		
		$url=$data_domain."/v3/v3_api/get_info/getTeamRelation/SDsFJO4dS3D4dF64SDF46?key=tencent_id&id=".$teamid;
		$team_relation = get_urldata($url);  //球队关联记录
		$url=$data_domain."/v3/v3_api/get_info/getWinMatchByTeamId/SDsFJO4dS3D4dF64SDF46?all=1&id=".$team_relation->win007_id;
		$match_list = get_urldata($url);
		
		// 2015-07-02 zhangjh 输出排序 开始
		// 第一场是最近一场的结束场次，第二、三场是直播和未来比赛，都跳到直播页面
		$arr_over_match = array();
		$arr_future_match = array();
		for($i = 0; $i < count($match_list); $i++)
		{
			if(count($arr_over_match) + count($arr_future_match) >= 3)
				break;
			
			if($match_list[$i]->status == "-1" && count($arr_over_match) == 0)
			{
				$arr_over_match[] = $match_list[$i];
			}
			
			if($match_list[$i]->status == "0" || $match_list[$i]->status == "1")
			{
				$arr_future_match[] = $match_list[$i];
			}
		}
		
		if(count($arr_future_match) < 2)
		{
			$arr_over_match = array();
			for($i = 0; $i < count($match_list); $i++)
			{
				if(count($arr_over_match) + count($arr_future_match) >= 3)
					break;
				
				if($match_list[$i]->status == "-1")
				{
					$arr_over_match[] = $match_list[$i];
				}
			}
		}
		
		$match_list = array();
		$match_list = array_merge($arr_over_match, $arr_future_match);
		// 2015-07-02 zhangjh 输出排序 结束
		
		include template("playerdata:channel_match_foreshow");
		return $html;
	}
	

	
}

function get_urldata($url,$return='array'){	
		$data = file_get_contents($url);
		if($return=='array'){
			$data = json_decode($data);
			$data  =  $data->content;
		}
		return $data;
}



?>


