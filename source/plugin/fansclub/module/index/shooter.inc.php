<?php

if(!defined('IN_DISCUZ')) exit('Access Denied');

$data = array();

if($_G['gp_league_id'] != '')
{
    $data['league_id'] = intval($_G['gp_league_id']);
}else{
    $data['league_id'] = 1;
}
$league_arr = array(1,15,13,2,3,7,100,8,41,21);
if(!in_array($data['league_id'], $league_arr)){
    $data['league_id'] = 1;
}

$arr_game = fansclub_get_live2($data);

for($i=0,$len=count($arr_game['shooter']['data']) ; $i<$len ;$i++){
    if(mb_strlen($arr_game['shooter']['data'][$i]['player']) > 14){
      $arr_game['shooter']['data'][$i]['newplayer'] = substr(strrchr($arr_game['shooter']['data'][$i]['player'], '·'),2);
    }
    if(strpos($arr_game['shooter']['data'][$i]['player'],'.') !== FALSE && mb_strlen($arr_game['shooter']['data'][$i]['player']) > 13){
        $arr_game['shooter']['data'][$i]['newplayer'] = substr(strrchr($arr_game['shooter']['data'][$i]['player'], '.'),1);
    }
    if(strpos($arr_game['shooter']['data'][$i]['player'],'.') === FALSE && strpos($arr_game['shooter']['data'][$i]['player'],'·') === FALSE || mb_strlen($arr_game['shooter']['data'][$i]['player']) < 15){
        $arr_game['shooter']['data'][$i]['newplayer'] = '';
    }
    if($arr_game['shooter']['data'][$i]['player'] == '内马尔·达席尔瓦'){
        $arr_game['shooter']['data'][$i]['newplayer'] = '内马尔';
    }
   if($arr_game['shooter']['data'][$i]['player'] == '克里斯蒂亚诺·罗纳尔多'){
        $arr_game['shooter']['data'][$i]['newplayer'] = 'C罗纳尔多';
    }
    
}
//echo "<pre>";
//print_r($arr_game['shooter']['data']);exit;

/*
 *  $url = 'http://www.usport.com.cn/plugin.php?id=fansclub:api&ac=get_data&method=shooter&league_id='.$data['league_id'];
 *  $result = curl_access($url);
 *  echo "<pre>";
 *  print_r($result);exit;
 */

