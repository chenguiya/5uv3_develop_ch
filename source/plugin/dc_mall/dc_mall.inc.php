<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$_lang = lang('plugin/dc_mall');
$action = $_GET['action'] ? $_GET['action'] : 'index';
$config =@include DISCUZ_ROOT.'./source/plugin/dc_mall/data/config.php';
$version =$config['version'];
$cvar = $_G['cache']['plugin']['dc_mall'];
if($cvar['isvip']==1){
	if($_G['cache']['plugin']['dc_vip']['open']){
		$_G['dc_mall']['vip']['open'] = true;
		if($_G['dc_plugin']['vip']['user']){
			$_G['dc_mall']['vip']['user'] = true;
		}
	}
}elseif($cvar['isvip']==2){
	$_G['dc_mall']['vip']['open'] = true;
	$vipgroup = unserialize($cvar['vipgroup']);
	if(in_array($_G['groupid'],$vipgroup)){
		$_G['dc_mall']['vip']['user'] = true;
	}
}
$arr = array('goods','index','pay');
if(!in_array($action,$arr)) showmessage('undefined_action');
$file = DISCUZ_ROOT.'./source/plugin/dc_mall/module/index/'.$action.'.inc.php';
if (!file_exists($file)||!$cvar['open']) showmessage('undefined_action');
$mallnav = C::t('#dc_mall#dc_mall_sort')->getdata();
$sortid = dintval($_GET['sortid']);
if(empty($mallnav[$sortid]))$sortid=0;
@include $file;
$croppath = DISCUZ_ROOT.'./source/plugin/dc_mall/data/cron.php';
$cronupdate = @include $croppath;
if(TIMESTAMP-$cronupdate['timestamp']>$cvar['autotime']*60){
	require_once DISCUZ_ROOT.'./source/plugin/dc_mall/cache/cache_mallinfo.php';
	build_cache_plugin_mallinfo();
	$configdata = 'return '.var_export(array('timestamp'=>TIMESTAMP), true).";\n\n";
	if($fp = @fopen($croppath, 'wb')) {
		fwrite($fp, "<?php\n//plugin mall temp upgrade check file, DO NOT modify me!\n//Identify: ".md5($configdata)."\n\n$configdata?>");
		fclose($fp);
	}
}
include template('dc_mall:index/'.$action);
?>