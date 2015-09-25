<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$_lang = lang('plugin/dc_mall');
$action = $_GET['action'] ? $_GET['action'] : 'goods';
$config =@include DISCUZ_ROOT.'./source/plugin/dc_mall/data/config.php';
$version =$config['version'];
$cvar = $_G['cache']['plugin']['dc_mall'];
if($cvar['isvip']==1){
	if($_G['cache']['plugin']['dc_vip']['open']){
		$_G['dc_mall']['vip']['open'] = true;
	}
}elseif($cvar['isvip']==2){
	$_G['dc_mall']['vip']['open'] = true;
}
$arr = array('address','goods','orders','sort');
if(!in_array($action,$arr)) showmessage('undefined_action');
$file = DISCUZ_ROOT.'./source/plugin/dc_mall/module/admin/'.$action.'.inc.php';
if (!file_exists($file)) showmessage('undefined_action');
if (!$_G['uid']||$_G['adminid'] != 1) showmessage('not_loggedin','plugin.php?id=dc_mall');
$cvar = $_G['cache']['plugin']['dc_mall'];
$mallnav = C::t('#dc_mall#dc_mall_sort')->getdata();
// $dh = $_lang['admin_center'];
$dh = array(
	'goods' =>  '礼品管理',
  	'orders' =>  '订单管理',
  	'sort' =>  '礼品类别',
  	'address' =>  '用户地址',
);
$loadtemp = 'admin/'.$action;
@include $file;
$navtitle = $dh[$action].' - '.$cvar['title'];
include template('dc_mall:admin/index');
?>