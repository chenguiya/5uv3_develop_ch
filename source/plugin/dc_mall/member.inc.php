<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$_lang = lang('plugin/dc_mall');
$action = $_GET['action'] ? $_GET['action'] : 'orders';
$config = @include DISCUZ_ROOT.'./source/plugin/dc_mall/data/config.php';
$version = $config['version'];
$arr = array('address','orders','payfor');
if(!in_array($action,$arr)) showmessage('undefined_action');
$file = DISCUZ_ROOT.'./source/plugin/dc_mall/module/member/'.$action.'.inc.php';
if (!file_exists($file)) showmessage('undefined_action');
if (!$_G['uid']) showmessage('not_loggedin','plugin.php?id=dc_mall');
$cvar = $_G['cache']['plugin']['dc_mall'];
$mallnav = C::t('#dc_mall#dc_mall_sort')->getdata();
$dh = $_lang['member_center'];
$loadtemp = 'member/'.$action;
@include $file;
$navtitle = $dh[$action].' - '.$cvar['title'];
include template('dc_mall:member/index');
?>