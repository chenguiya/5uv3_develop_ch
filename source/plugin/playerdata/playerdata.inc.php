<?php

	if(!defined('IN_DISCUZ')) exit('Access Denied');
	require './source/function/function_forum.php';
	include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php');
	
	loadforum();
	include_once(DISCUZ_ROOT.'./source/plugin/fansclub/function.inc.php'); // 公共函数
	// $_G['forum'] = array_merge($_G['forum'], get_fansclub_info($_G['fid']));
    
    $arr_forum_add = DB::fetch_all("select * from ".DB::table('forum_forumfield')." where fid = ".intval($_G['fid']));
    $_G['forum']['banner'] = $_G['setting']['attachurl'].'common/'.$arr_forum_add[0]['banner'];
    
	//控制器共用代码
	$data_domain = $_G['config']['playerdata']['domian'];
	$teamid=fansclub_get_team_player_id($_GET['fid']);  //通过fid获取球队ID
	//echo $teamid;die;
	if($_G['forum']['type'] == 'sub'){
					//如果是球员ID，则需要转换成球队ID
					$url=$data_domain."/v3/v3_api/get_info/getPlayer/SDsFJO4dS3D4dF64SDF46?id=".$teamid;
					$player = get_urldata($url);
					$teamid = $player->team_id;
	}
	
	//判断NBA
	require_once libfile('function/extends');
	$groupid = getgroup($_G['fid']);
	
	$url=$data_domain."/v3/v3_api/get_info/getTeamById/SDsFJO4dS3D4dF64SDF46?id=".$teamid;
	$team = get_urldata($url); //球队信息
	$url=$data_domain."/v3/v3_api/get_info/getScoreboardByTeamidLeagueid/SDsFJO4dS3D4dF64SDF46?team_id=".$teamid."&league_id=".$team->league_id;
	$team_scoreboard = get_urldata($url);  //积分榜
	
	//球员信息
	$playerurl=$data_domain."/v3/v3_api/get_info/getPlayerRoutineByTeamId/SDsFJO4dS3D4dF64SDF46?id=".$teamid;
	$playerinfo = get_urldata($playerurl);
	$positionArr = array('前锋'=>array('锋','a'),'中场'=>array('前卫','b'),'后卫'=>array('后卫','c'));
	$positionName = array('前锋','中场','后卫');
	if($playerinfo) foreach ($playerinfo as $key => $value) {
								$headurl = $data_domain."/v3/v3_api/get_info/getPlayer/SDsFJO4dS3D4dF64SDF46?id=".$value->player_id;
								$playerinfo[$key]->head = get_head($headurl);
								$i = 0;
								foreach ($positionArr as $name => $arr) {
									if(stristr($value->position,$arr[0])){
										$playerlist[$i][$key] = $value;
										break;	
									}
									$i++;
								}		
						}
	
	//守门员数据在另一张表，单独做
	$goalkeeperurl=$data_domain."/v3/v3_api/get_info/getGoalkeeperStatisticsByTeamId/SDsFJO4dS3D4dF64SDF46?id=".$teamid;
	$goalkeeper = get_urldata($goalkeeperurl);
	if($goalkeeper) foreach ($goalkeeper as $key => $value) {
								$headurl = $data_domain."/v3/v3_api/get_info/getPlayer/SDsFJO4dS3D4dF64SDF46?id=".$value->player_id;
								$goalkeeper[$key]->head = get_head($headurl);
								}
	switch ($_GET['ac']) {
  //阵容页面		
		case 'formation':
			if($groupid == '129') die('NBA暂无阵容！');
			if($_G['forum']['type'] == 'sub') die('该球员暂无阵容！');
			//球队教练信息
			$url=$data_domain."/v3/v3_api/get_info/getMatchByTeam/SDsFJO4dS3D4dF64SDF46?over=1&id=".$teamid;
			$last_match = get_urldata($url); //获取最后一场已结束的比赛
			$team_a = $last_match->team_a;
			$url=$data_domain."/v3/v3_api/get_info/getFormationByMatchId/SDsFJO4dS3D4dF64SDF46?id=".$last_match->match_id;
			$formation_arr  = get_urldata($url); //获取最后一场比赛ID得到该场比赛阵容
			$last_formation = array();
			foreach ($formation_arr as $k => $v) {
					$headurl = $data_domain."/v3/v3_api/get_info/getPlayer/SDsFJO4dS3D4dF64SDF46?id=".$v->player_id;
					$formation_arr[$k]->head =  get_head($headurl);
					if($v->team_name == $team_a){ //是否为主队
						$last_formation['a_team'][] = $formation_arr[$k];
					}else{
						$last_formation['b_team'][] = $formation_arr[$k];
					} 
			}
			//debug($last_formation);
			$coachinfourl=$data_domain."/v3/v3_api/get_info/getSodaCoachInfoByTeamId/SDsFJO4dS3D4dF64SDF46?id_type=tencent_id&id=".$teamid;
			$coachinfo = get_urldata($coachinfourl); //所有生涯执教信息
			$coachurl=$data_domain."/v3/v3_api/get_info/getSodaCoachByTeamId/SDsFJO4dS3D4dF64SDF46?id_type=tencent_id&id=".$teamid;
			$coach = get_urldata($coachurl); //教练基本信息
			$coach_head = explode('http://picture.sodasoccer.com/photo_play/med/photo_play/',$coach->head);
			$file = '/data/www/5uv3/data/photo_play/pic/'.$coach_head[1];
			$coach->head = file_exists($file) ? $coach_head[1] : '';
			ksort($playerlist);

			break;
  //数据页面			
			case 'data':
			if($groupid == '129') die('NBA暂无数据！');
			if($_G['forum']['type'] == 'forum'){ //球队
				//技术统计
				$url=$data_domain."/v3/v3_api/get_info/getEventRoutineByTeamIdSeason/SDsFJO4dS3D4dF64SDF46?team_id=".$teamid;
				$eventroutine= get_urldata($url); //常规数据
				$url=$data_domain."/v3/v3_api/get_info/getEventAttackByTeamIdSeason/SDsFJO4dS3D4dF64SDF46?team_id=".$teamid;
				$eventattack= get_urldata($url); //攻击数据
				$url=$data_domain."/v3/v3_api/get_info/getEventDefendByTeamIdSeason/SDsFJO4dS3D4dF64SDF46?team_id=".$teamid;
				$eventdefend= get_urldata($url); //防守数据
				//debug($eventdefend);
				//单场技术统计
				$url=$data_domain."/v3/v3_api/get_info/getMatchRoutineByTeamIdSeason/SDsFJO4dS3D4dF64SDF46?team_id=".$teamid;
				$matchroutine= get_urldata($url); //常规数据
				$url=$data_domain."/v3/v3_api/get_info/getMatchAttackByTeamIdSeason/SDsFJO4dS3D4dF64SDF46?team_id=".$teamid;
				$matchattack= get_urldata($url); //攻击数据
				$url=$data_domain."/v3/v3_api/get_info/getMatchDefendByTeamIdSeason/SDsFJO4dS3D4dF64SDF46?team_id=".$teamid;
				$matchdefend= get_urldata($url); //防守数据
			}else if($_G['forum']['type'] == 'sub'){  //球员
				$playerid = fansclub_get_team_player_id($_GET['fid']);
				//技术统计
				$url=$data_domain."/v3/v3_api/get_info/getPlayerRoutineByPlayerId/SDsFJO4dS3D4dF64SDF46?id=".$playerid;
				$eventroutine[] = get_urldata($url); //常规数据
				//debug($eventroutine);
				$url=$data_domain."/v3/v3_api/get_info/getPlayerAttackByPlayerId/SDsFJO4dS3D4dF64SDF46?id=".$playerid;
				$eventattack[] = get_urldata($url); //攻击数据
				//debug($eventattack);
				$url=$data_domain."/v3/v3_api/get_info/getPlayerDefendByPlayerId/SDsFJO4dS3D4dF64SDF46?id=".$playerid;
				$eventdefend[] = get_urldata($url); //防守数据
				//debug($eventdefend);
			}
			//积分榜
			$url=$data_domain."/v3/v3_api/get_info/getScoreboardByLeagueid/SDsFJO4dS3D4dF64SDF46?league_id=".$team->league_id;
			$scoreboard = get_urldata($url);
			break;
			
			case 'playerinfo':
			if($groupid == '129') die('NBA暂无信息！');
			// zhangjh 2015-06-15
			$_arr_forum = C::t('forum_forum')->fetch(intval($_GET['fid']));
			if($_arr_forum['type'] != 'sub')
			{
				showmessage('没有找到记录', ''); // 不是球员不显示
			}
			
			//获取球员信息
			$playerid = fansclub_get_team_player_id($_GET['fid']);  //默认等于1，后面会由链接传入
			$url=$data_domain."/v3/v3_api/get_info/getSodaPlayerByQQPlayerid/SDsFJO4dS3D4dF64SDF46?id=".$playerid;
			$player_id = get_urldata($url);  //球员信息
			$playerid = $player_id->player_id;
			$url=$data_domain."/v3/v3_api/get_info/getSodaPlayerInfoByPlayerid/SDsFJO4dS3D4dF64SDF46?id=".$playerid;
			$player_info = get_urldata($url);  //球员信息
			$url=$data_domain."/v3/v3_api/get_info/getWinTeamById/SDsFJO4dS3D4dF64SDF46?key=soda_id&id=".$player_info->team_id;
			$win_team = get_urldata($url);  //通过搜达球队ID获取球探网球队ID，获取头像
			$url=$data_domain."/v3/v3_api/get_info/getSodaPlayerResumeByPlayerid/SDsFJO4dS3D4dF64SDF46?id=".$playerid;
			$player_resume = get_urldata($url);  //球员履历
			$url=$data_domain."/v3/v3_api/get_info/getSodaPlayerEventByPlayerid/SDsFJO4dS3D4dF64SDF46?id=".$playerid;
			$player_event = get_urldata($url);  //球员赛事
			$url=$data_domain."/v3/v3_api/get_info/getSodaPlayerTransferByPlayerid/SDsFJO4dS3D4dF64SDF46?id=".$playerid;
			$player_transfer = get_urldata($url);  //球员转会
			break;
		default:
			die('该模块不存在！');
			break;
	}
	
	// 2015-06-24 zhangjh
	$forum_is_open = $_G['forum']['status'] != 0 ? TRUE : FALSE;
	
	// 2015-06-30 zhangjh 加TKD
	$nobbname = TRUE;
	if($_GET['ac'] == 'formation')
	{
		$navtitle = $_G['forum']['name'].'阵容_'.$_G['setting']['bbname'];
		$metakeywords = $_G['forum']['name'].'阵容';
		$metadescription = $_G['forum']['name'].'所有阵容，球员球衣号码尽在5u体育。';
	}
	elseif($_GET['ac'] == 'data')
	{
		$navtitle = $_G['forum']['name'].'赛程、数据_'.$_G['setting']['bbname'];
		$metakeywords = $_G['forum']['name'].'赛程、数据';
		$metadescription = '5U'.$_G['forum']['name'].'频道用数据说话，在这里你可以随时随地掌控'.$_G['forum']['name'].'各种最新数据和其球员数据，让你轻轻松松成为球迷眼中的专家、数据大师，是球迷聚会吹侃必备技能之一。';
	}
	elseif($_GET['ac'] == 'playerinfo')
	{
		$navtitle = $_G['forum']['name'].'全部详细资料_'.$_G['setting']['bbname'];
		$metakeywords = $_G['forum']['name'].'全部详细资料';
		$metadescription = '5U'.$_G['forum']['name'].'频道有最完善的'.$_G['forum']['name'].'资料，关于'.$_G['forum']['name'].'全部详细资料都可以在这里找到。让你轻轻松松成为球迷眼中的专家、数据大师。';
	}

	include template('playerdata:channel_'.$_GET['ac']);
	
	
	//获取球队的ID
	function get_teamid(){
			return "";
	}
	
	function get_urldata($url,$return='array'){	
		$data = file_get_contents($url);
		if($return=='array'){
			$data = json_decode($data);
			$data  =  $data->content;
		}
		return $data;
	}
	
	function get_head($headurl,$return='array'){	
	// 2015-06-30 zhangjh 修改取头像的方法
	$arr_info = explode('id=', $headurl);
	$player_id = trim($arr_info[1]);
	if($player_id == '')
	{
		$data = './template/usportstyle/common/images/player_img.gif';
	}
	else
	{
		$data = './data/photo_play/c_player/'.$player_id.'.jpg';
	}
	/*
		$data = file_get_contents($headurl);
		if($return=='array'){
			$data = json_decode($data);
			$data  =  $data->content->head;
		}
	*/
	return $data;
	}
?>
