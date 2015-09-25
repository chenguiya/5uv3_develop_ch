<?php

!defined('IN_DISCUZ') && exit('Access Denied');
define('IN_k_misign', '1');
$setting = $_G['cache']['plugin']['k_misign'];//获取签到插件后台设置参数
$tdtime = gmmktime(0,0,0,dgmdate($_G['timestamp'], 'n',$setting['tos']),dgmdate($_G['timestamp'], 'j',$setting['tos']),dgmdate($_G['timestamp'], 'Y',$setting['tos'])) - $setting['tos']*3600;  //$_G['timestamp'])=>当前时间   今天的起始日期
$op = htmlspecialchars($_GET['op']);
$page = $_GET['page'] ? $_GET['page']:1;
$nextpege = $page+1;
$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('plugin_k_misign')." WHERE time >= {$tdtime} group by uid ");
if($op =='list'){	
	$stat = ($page-1)*20;
	$pages = $page*20;
	$multipage = multi($num, 20, $page, "plugin.php?id=k_misign:sign&operation=list&op={$_GET[op]}&page={$nextpege}");
}else{
	$stat = 0;
	$pages = 20;
	$multipage = multi($num, 20, $page, "plugin.php?id=k_misign:sign&operation=list&op={$_GET[op]}&page={$nextpege}");
}
$qiandaodb_list = DB::fetch_first("SELECT * FROM ".DB::table('plugin_k_misign')." WHERE uid=$_G[uid] and  lastreward>0");
$todayqd  = DB::fetch_first("SELECT id FROM ".DB::table('plugin_k_misign')." WHERE time>$tdtime group by uid");
$todayqd['todayqd'] = count($todayqd);
$qiandaodb = DB::fetch_all("SELECT * FROM ".DB::table('plugin_k_misign')." WHERE time>$tdtime  group by uid order by time desc limit {$stat},{$pages} ");
$ranklist=DB::fetch_all("SELECT * FROM ".DB::table('common_member')." WHERE 1 order by credits desc limit 0,5  ");
$stats = DB::fetch_first("SELECT * FROM ".DB::table('plugin_k_misignset')." WHERE 1 order by highestq desc");
$todaystar['uid'] = DB::result_first("SELECT uid FROM ".DB::table('plugin_k_misign')." WHERE time >= {$tdtime} ORDER BY time ASC");
$todaystar = getuserbyuid($todaystar['uid']);
foreach($ranklist as $f){
	$f['avatar']=avatar($f['uid'],'small',true);
	$ranklist_fans[]=$f;
}
foreach($qiandaodb as $k=>$v){
	$foruminfo = DB::fetch_first("SELECT * FROM ".DB::table('forum_forum')." WHERE fid=$v[fid]");
	$arr[$k]=$v;
	$userinfo = getuserbyuid($v['uid']);
	$arr[$k]['username']=$userinfo['username'];
	$arr[$k]['forumname']=$foruminfo['name'];
	$arr[$k]['time']=date('Y-m-d H:i:s',$v['time']);
}
$numtostr = array(
	0 => 'zero',
	1 => 'one',
	2 => 'two',
	3 => 'three',
	4 => 'four',
	5 => 'five',
	6 => 'six',
	7 => 'seven',
	8 => 'eight',
	9 => 'nine',
);
include template('k_misign:sign');
?>