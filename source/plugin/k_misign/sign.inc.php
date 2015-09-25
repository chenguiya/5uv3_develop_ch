<?php
!defined('IN_DISCUZ') && exit('Access Denied');
define('IN_k_misign', '1');
$setting = $_G['cache']['plugin']['k_misign'];//获取签到插件后台设置参数
$fid = htmlspecialchars($_GET['fid']);
$tdtime = gmmktime(0,0,0,dgmdate($_G['timestamp'], 'n',$setting['tos']),dgmdate($_G['timestamp'], 'j',$setting['tos']),dgmdate($_G['timestamp'], 'Y',$setting['tos'])) - $setting['tos']*3600;  //$_G['timestamp'])=>当前时间   今天的起始日期
$checksign = DB::fetch_first("select * from ".DB::table('plugin_k_misign')." where uid='$_G[uid]' and time>{$tdtime} ");
  //今天是否已经签到过，用来判断是否加积分
$htime = dgmdate($_G['timestamp'], 'H',$setting['tos']); //当前时间的小时
$nlvtext = str_replace(array("\r\n", "\n", "\r"), '/hhf/', $setting['lvtext']);//签到等级名称
$njlmain =str_replace(array("\r\n", "\n", "\r"), '/hhf/', $setting['jlmain']);//前N额外奖励
list($lv1name, $lv2name, $lv3name, $lv4name, $lv5name, $lv6name, $lv7name, $lv8name, $lv9name, $lv10name, $lvmastername) = explode("/hhf/", $nlvtext);
$extreward = explode("/hhf/", $njlmain);
$extreward_num = count($extreward);
$setting['groups'] = unserialize($setting['groups']);  //会员组
$setting['ban'] = explode(",",$setting['ban']);  //黑名单
if(empty($checksign)){  //如果今天已经签到过，后面的签到都不会有积分
	$credit = mt_rand($setting['mincredit'],$setting['maxcredit']);  //随机获取积分（最大与最小值之间）
}else{
	$credit =0;
}
$qiandaodb = DB::fetch_first("SELECT * FROM ".DB::table('plugin_k_misign')." WHERE uid='$_G[uid]' and fid={$fid}");//获取用户最新的签到记录
$num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('plugin_k_misign')." WHERE time >= {$tdtime} ");//计算今天已经有几个签到
if ($qiandaodb['days'] >= '1500') {
	$qiandaodb['level'] = 99;
} elseif ($qiandaodb['days'] >= '750') {
	$qiandaodb['level'] = 10;
} elseif ($qiandaodb['days'] >= '365') {
	$qiandaodb['level'] = 9;
} elseif ($qiandaodb['days'] >= '240') {
	$qiandaodb['level'] = 8;
} elseif ($qiandaodb['days'] >= '120') {
	$qiandaodb['level'] = 7;
} elseif ($qiandaodb['days'] >= '60') {
	$qiandaodb['level'] = 6;
} elseif ($qiandaodb['days'] >= '30') {
	$qiandaodb['level'] = 5;
} elseif ($qiandaodb['days'] >= '15') {
	$qiandaodb['level'] = 4;
} elseif ($qiandaodb['days'] >= '7') {
	$qiandaodb['level'] = 3;
} elseif ($qiandaodb['days'] >= '3') {
	$qiandaodb['level'] = 2;
} elseif ($qiandaodb['days'] >= '1') {
	$qiandaodb['level'] = 1;
}
$signset=DB::fetch_first("SELECT * FROM ".DB::table('plugin_k_misignset')." WHERE  fid={$fid}");
if(empty($signset)){
	DB::query("INSERT INTO ".DB::table('plugin_k_misignset')." (fid) VALUES ($fid)");
}
$stats = DB::fetch_first("SELECT * FROM ".DB::table('plugin_k_misignset')." WHERE fid={$fid}");
$qddb = DB::fetch_first("SELECT time FROM ".DB::table('plugin_k_misign')." ORDER BY time DESC limit 0,1");
$lastmonth = dgmdate($qddb['time'], 'm',$setting['tos']);
$nowmonth = dgmdate($_G['timestamp'], 'm',$setting['tos']);
if($nowmonth != $lastmonth){
	DB::query("UPDATE ".DB::table('plugin_k_misign')." SET mdays=0 WHERE uid");
}
$todaystar['uid'] = DB::result_first("SELECT uid FROM ".DB::table('plugin_k_misign')." WHERE time >= {$tdtime} ORDER BY time ASC");
$todaystar = getuserbyuid($todaystar['uid']);

function sign_msg($msg, $treferer = '') {
	global $_G;
	if(defined('IN_MOBILE')) {
		include template('k_misign:float');
		dexit();
	}else{
		include template('k_misign:float');
		dexit();
	}
}
if($_GET['operation'] == 'qiandao') {
	if($_GET['formhash'] != FORMHASH) {
		showmessage('undefined_action', NULL);
	}
	if(!in_array($_G['groupid'], $setting['groups'])){
		include template('common/header_ajax');
		echo lang('plugin/k_misign', 'groupontallow');
		include template('common/footer_ajax');
		exit();
	}
	if(in_array($_G['uid'],$setting['ban'])){
		include template('common/header_ajax');
		echo lang('plugin/k_misign', 'uidinblack');
		include template('common/footer_ajax');
		exit();
	}
	if($qiandaodb['time']>$tdtime){
		include template('common/header_ajax');
		echo lang('plugin/k_misign', 'tdyq');
		include template('common/footer_ajax');
		exit();
	}
	if($setting['lockopen']){
		while(discuz_process::islocked('k_misign', 5)){
			usleep(100000);
		}
	}
	if(!$qiandaodb['uid']) {
		DB::query("INSERT INTO ".DB::table('plugin_k_misign')." (uid,time,fid) VALUES ('$_G[uid]',$_G[timestamp],$fid)");
	}
	$row = $num+1;
	if(($tdtime - $qiandaodb['time']) < 86400){
		DB::query("UPDATE ".DB::table('plugin_k_misign')." SET days=days+1,mdays=mdays+1,time='$_G[timestamp]',reward=reward+{$credit},lastreward='$credit',lasted=lasted+1, row='$row' WHERE uid='$_G[uid]' and fid=$fid");
	} else {
		DB::query("UPDATE ".DB::table('plugin_k_misign')." SET days=days+1,mdays=mdays+1,time='$_G[timestamp]',reward=reward+{$credit},lastreward='$credit',lasted='1', row='$row' WHERE uid='$_G[uid]' and fid=$fid");
	}
	if(empty($checksign)){  //如果没有签到过才加积分
		updatemembercount($_G['uid'], array($setting['nrcredit'] => $credit));//更新用户的积分
		if($num <= ($extreward_num - 1) ) {  //如果是前几位签到额外简历
			list($exacr,$exacz) = explode("|", $extreward[$num]);
			$psc = $num+1;
			if($exacr && $exacz) updatemembercount($_G['uid'], array($exacr => $exacz));
		}
	}
	if(memory('check')) memory('set', 'k_misign_'.$_G['uid'], $_G['timestamp'], 86400);
	if($num == 0) {
		if($stats['todayq'] > $stats['highestq']) DB::query("UPDATE ".DB::table('plugin_k_misignset')." SET highestq='$stats[todayq]' WHERE fid=$fid");
		DB::query("UPDATE ".DB::table('plugin_k_misignset')." SET yesterdayq='$stats[todayq]',todayq=1 WHERE fid='$fid'");
	} else {
		DB::query("UPDATE ".DB::table('plugin_k_misignset')." SET todayq=todayq+1 WHERE fid=$fid");
	}
	if($setting['lockopen']) discuz_process::unlock('k_misign');
	$qiandaodb = DB::fetch_first("SELECT * FROM ".DB::table('plugin_k_misign')." WHERE uid='$_G[uid]' and fid=$fid");
	if($_GET['from'] == 'wsq'){
		include template('k_misign:sign');
		exit();
	}else{
		include template('common/header_ajax');
		if($_GET['from'] == 'insign'){
			echo '';
		}else{
			//echo "<div class=\"font\">".lang('plugin/k_misign','signed')."</div><span class=\"nums\">".lang('plugin/k_misign','row').$qiandaodb['lasted'].lang('plugin/k_misign','days')."</span><div class=\"fblock\"><div class=\"all\">".$stats['todayq'].lang('plugin/k_misign','people')."</div><div class=\"line\">".$qiandaodb['row']."</div></div>";
			echo "签到";
		}
		include template('common/footer_ajax');
	}
}
if ($qiandaodb['days'] >= '1500') {
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.Master]{$lvmastername}</b></font> .";
} elseif ($qiandaodb['days'] >= '750') {
	$q['lvqd'] = 1500 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.10]{$lv10name}{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.Master]{$lvmastername}</b></font> .";
} elseif ($qiandaodb['days'] >= '365') {
	$q['lvqd'] = 750 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.9]{$lv9name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.10]{$lv10name}</b></font> .";
} elseif ($qiandaodb['days'] >= '240') {
	$q['lvqd'] = 365 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.8]{$lv8name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.9]{$lv9name}</b></font> .";
} elseif ($qiandaodb['days'] >= '120') {
	$q['lvqd'] = 240 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.7]{$lv7name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.8]{$lv8name}</b></font> .";
} elseif ($qiandaodb['days'] >= '60') {
	$q['lvqd'] = 120 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.6]{$lv6name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.7]{$lv7name}</b></font> .";
} elseif ($qiandaodb['days'] >= '30') {
	$q['lvqd'] = 60 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.5]{$lv5name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.6]{$lv6name}</b></font> .";
} elseif ($qiandaodb['days'] >= '15') {
	$q['lvqd'] = 30 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.4]{$lv4name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.5]{$lv5name}</b></font> .";
} elseif ($qiandaodb['days'] >= '7') {
	$q['lvqd'] = 15 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.3]{$lv3name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.4]{$lv4name}</b></font> .";
} elseif ($qiandaodb['days'] >= '3') {
	$q['lvqd'] = 7 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.2]{$lv2name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.3]{$lv3name}</b></font> .";
} elseif ($qiandaodb['days'] >= '1') {
	$q['lvqd'] = 3 - $qiandaodb['days'];
	$q['level'] = "{$lang['level']}<font color=green><b>[LV.1]{$lv1name}</b></font>{$lang['level2']} <font color=#FF0000><b>{$q['lvqd']}</b></font> {$lang['level3']} <font color=#FF0000><b>[LV.2]{$lv2name}</b></font> .";
}
?>