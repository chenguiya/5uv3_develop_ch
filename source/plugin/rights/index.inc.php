<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
global $_G;
loadcache('plugin');
$_settings = $_G['cache']['plugin']['rights'];

if ($_G['uid']) {
	$credits = $_G['setting']['extcredits'];
	$creditstrans = $_G['setting']['creditstrans'];		//获取后台设置的支付货币类型
	$membercount = DB::fetch_first("SELECT * FROM ".DB::table('common_member_count')." WHERE uid=".$_G['uid']);
	$balance = $membercount['extcredits'.$creditstrans];
// 	var_dump($balance);die;
}

$ac = isset($_GET['op']) ? trim($_GET['op']) : 'mall';
$oparr = array('mall', 'me', 'log', 'buy', 'info');

if (!in_array($ac, $oparr)) showmessage('undefined_action');
$file = DISCUZ_ROOT.'./source/plugin/rights/module/index/' . $ac . '.inc.php';

if (!file_exists($file)) showmessage('source/plugin/rights/module/index/' . $ac . '.inc.php'.'不存在!');
include $file;

include template('rights:index/rights_'.$ac);