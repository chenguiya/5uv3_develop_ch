<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
//echo "<pre>";
//print_r($arr_game['leaguescore']['data']);exit;