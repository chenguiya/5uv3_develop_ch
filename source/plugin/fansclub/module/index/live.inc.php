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


if(defined('IN_MOBILE'))	{
        //wap 曼城赛程
        $league_name = $_G['forum']['channel']['name'];
        $team_name = $_G['forum']['fup_name'];
        $list_num = 12 ;
        $arr_game['match_arr'] = get_match($league_name , $team_name, $list_num);
}else{
    $arr_game = fansclub_get_live2($data);
    $arr_game_ = array();

foreach($arr_game['have_end']['game_array'] as $k=>$v){
	foreach($v['games'] as $k1=>$v1){
		$arr_game_[] = $v1;
	}	
	
}

if(trim($data['league_id']) == '' || count($arr_game_) > 7){
	$len = 7;
}else{
	$len = count($arr_game_);
}

for($i=0;$i<$len;$i++){
	$arr_game['have_end_'][$i] = $arr_game_[$i];	
	/*
         if(count($arr_game['have_end_'][$i]['live_list']) == 0 || $arr_game['have_end_'][$i]['live_list'][0]['url'] == ''){
         
		$arr_game['have_end_'][$i]['live_list'][0]['name'] = '等待更新';
		$arr_game['have_end_'][$i]['live_list'][0]['url'] = 'http://www.5usport.com/live';
	}
         
        for ($b=0,$bl=count($arr_game['have_end_'][$i]['promotion_list']);$b<$bl;$b++){
            if($arr_game['have_end_'][$i]['promotion_list'][$b] == ''){
                $arr_game['have_end_'][$i]['promotion_list'][$b] = '等待更新';
                $arr_game['have_end_'][$i]['promotion_list'][$b] = 'http://www.5usport.com/live';
            }
        }*/
	$arr_game['have_end_'][$i]['week'] = date('w',strtotime($arr_game_[$i]['game_time']));	
}

foreach($arr_game['have_end_'] as $k=>$v){
	if($v['week'] == 1){
		$arr_game['have_end_'][$k]['week'] = "周一";
	}elseif($v['week'] == 2){
		$arr_game['have_end_'][$k]['week'] = "周二";
	}elseif($v['week'] == 3){
		$arr_game['have_end_'][$k]['week'] = "周三";
	}elseif($v['week'] == 4){
		$arr_game['have_end_'][$k]['week'] = "周四";
	}elseif($v['week'] == 5){
		$arr_game['have_end_'][$k]['week'] = "周五";
	}elseif($v['week'] == 6){
		$arr_game['have_end_'][$k]['week'] = "周六";
	}elseif($v['week'] == 0){
		$arr_game['have_end_'][$k]['week'] = "周日";
	}
}

$arr_game_not = array();
foreach($arr_game['not_end']['game_array'] as $k7=>$v7){
	foreach($v7['games'] as $k8=>$v8){
		$arr_game_not[] = $v8;
	}	
}
$lens = count($arr_game_not);

for($j=0;$j<$lens;$j++){
	$arr_game['have_not_'][$j] = $arr_game_not[$j];	
	if(count($arr_game['have_not_'][$j]['live_list']) == 0 ){
		$arr_game['have_not_'][$j]['live_list'][0]['name'] = '等待更新';
		$arr_game['have_not_'][$j]['live_list'][0]['url'] = 'http://www.5usport.com/live';
	}
        if(count($arr_game['have_not_'][$j]['live_list']) == 1  && $arr_game['have_not_'][$j]['live_list'][0]['name'] == ''&& $arr_game['have_not_'][$j]['live_list'][0]['url'] == ''){
		$arr_game['have_not_'][$j]['live_list'][0]['name'] = '等待更新';
		$arr_game['have_not_'][$j]['live_list'][0]['url'] = 'http://www.5usport.com/live';
	}
        for ($k=0,$l=count($arr_game['have_not_'][$j]['live_list']);$k<$l; $k++){
                if($arr_game['have_not_'][$j]['live_list'][$k]['name'] == '' && $arr_game['have_not_'][$j]['live_list'][$k]['url'] != ''){
                    if(strpos($arr_game['have_not_'][$j]['live_list'][$k]['url'],'letv') !== FALSE){
                        $arr_game['have_not_'][$j]['live_list'][$k]['name'] = '乐视直播';
                    }elseif(strpos($arr_game['have_not_'][$j]['live_list'][$k]['url'],'pptv') !== FALSE){
                        $arr_game['have_not_'][$j]['live_list'][$k]['name'] = 'PPTV直播';
                    }elseif(strpos($arr_game['have_not_'][$j]['live_list'][$k]['url'],'qq') !== FALSE){
                        $arr_game['have_not_'][$j]['live_list'][$k]['name'] = 'QQ直播';
                    }elseif(strpos($arr_game['have_not_'][$j]['live_list'][$k]['url'],'cctv5') !== FALSE){
                        $arr_game['have_not_'][$j]['live_list'][$k]['name'] = 'CCTV5官网';
                    }
                }
        }
	$arr_game['have_not_'][$j]['week'] = date('w',strtotime($arr_game_not[$j]['game_time']));
}

foreach($arr_game['have_not_'] as $k=>$v){
	if($v['week'] == 1){
		$arr_game['have_not_'][$k]['week'] = "周一";
	}elseif($v['week'] == 2){
		$arr_game['have_not_'][$k]['week'] = "周二";
	}elseif($v['week'] == 3){
		$arr_game['have_not_'][$k]['week'] = "周三";
	}elseif($v['week'] == 4){
		$arr_game['have_not_'][$k]['week'] = "周四";
	}elseif($v['week'] == 5){
		$arr_game['have_not_'][$k]['week'] = "周五";
	}elseif($v['week'] == 6){
		$arr_game['have_not_'][$k]['week'] = "周六";
	}elseif($v['week'] == 0){
		$arr_game['have_not_'][$k]['week'] = "周日";
	}
}


}

function get_match($league_name = '', $team_name = '', $list_num ) {
		//曼城赛程比赛    
                                             $url1 = 'http://api.5usport.com/v3/to_v3/phpcms/get_match?league_name='.urlencode($league_name).'&team_name='.urlencode($team_name).'&list_num='.$list_num;//本场比赛获取接口
    		$data = file_get_contents($url1);
		$data = json_decode($data);
                                            $data = objectToArray($data);        
                                            return $data;
}
function objectToArray($e){
	$e=(array)$e;
	foreach($e as $k=>$v){
		if( gettype($v)=='resource' ) return;
		if( gettype($v)=='object' || gettype($v)=='array' )
			$e[$k]=(array)objectToArray($v);
	}
	return $e;
}

/*
if(!defined('IN_DISCUZ')) exit('Access Denied');

$data = array();

if($_G['gp_league_id'] != '')
{
	$data['league_id'] = intval($_G['gp_league_id']);
}

if($_G['gp_page_size'] != '')
{
	$data['page_size'] = intval($_G['gp_page_size']);
}
else
{
	$data['page_size'] = 3; // 取最后3天
}

if($_G['gp_last_id'] != '' && is_date($_G['gp_last_id'])) // 显示从这天之后的比赛
{
	$data['last_id'] = trim($_G['gp_last_id']);
	$data['page_size'] = 1; // 取最后1天
}

$data['page_size'] = 3;
$data['game_count'] = 8; // 返回8条记录

$arr_game = fansclub_get_live($data);

$data = array();

if($_G['gp_league_id'] != '')
{
	$data['league_id'] = intval($_G['gp_league_id']);
}

if($_G['gp_match_status']!= '')
{
	$data['match_status'] = trim($_G['gp_match_status']);
}
//echo $data['match_status'];exit;
$arr_game = fansclub_get_live2($data);
//echo "<pre>";
//print_r($arr_game);
*/
